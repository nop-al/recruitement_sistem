<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\AssessmentQuestion;
use App\Models\Candidate;
use App\Models\CandidateTestResult;
use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    /**
     * Display a listing of assessments for Admin/HR.
     */
    public function index(Request $request): View
    {
        $query = Assessment::with(['position', 'questions'])->withCount(['questions', 'results']);

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where('title', 'like', "%{$search}%");
        }

        if ($request->filled('test_type') && in_array($request->query('test_type'), Assessment::TEST_TYPES, true)) {
            $query->where('test_type', $request->query('test_type'));
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->query('position_id'));
        }

        $assessments = $query->latest()->paginate(10)->withQueryString();
        $positions = Position::orderBy('title')->get();

        return view('assessments.index', [
            'assessments' => $assessments,
            'positions' => $positions,
            'testTypes' => Assessment::TEST_TYPES,
            'selectedType' => $request->query('test_type'),
            'selectedPosition' => $request->query('position_id'),
            'search' => $request->query('search'),
        ]);
    }

    /**
     * Show the form for creating a new assessment.
     */
    public function create(): View
    {
        $positions = Position::where('status', 'active')->orderBy('title')->get();

        return view('assessments.create', [
            'positions' => $positions,
            'testTypes' => Assessment::TEST_TYPES,
        ]);
    }

    /**
     * Store a newly created assessment in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'test_type' => ['required', 'string', 'in:'.implode(',', Assessment::TEST_TYPES)],
            'position_id' => ['nullable', 'exists:positions,id'],
            'description' => ['nullable', 'string'],
            'duration' => ['required', 'integer', 'min:5', 'max:180'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        $assessment = Assessment::create($validated);

        $user = Auth::user();
        $prefix = $user && $user->role && $user->role->name === 'admin' ? 'admin' : 'hr';

        return redirect()->route("{$prefix}.assessments.show", $assessment->id)
            ->with('success', "Tes '{$assessment->title}' berhasil dibuat. Silakan tambahkan butir pertanyaan pada Bank Soal.");
    }

    /**
     * Display the specified assessment details, question bank, and results.
     */
    public function show(Assessment $assessment): View
    {
        $assessment->load([
            'position',
            'questions.answers',
            'results.candidate.user',
            'results.application.position',
        ]);

        return view('assessments.show', [
            'assessment' => $assessment,
            'questions' => $assessment->questions,
            'results' => $assessment->results()->latest()->paginate(15),
        ]);
    }

    /**
     * Show the form for editing the specified assessment.
     */
    public function edit(Assessment $assessment): View
    {
        $positions = Position::orderBy('title')->get();

        return view('assessments.edit', [
            'assessment' => $assessment,
            'positions' => $positions,
            'testTypes' => Assessment::TEST_TYPES,
        ]);
    }

    /**
     * Update the specified assessment in storage.
     */
    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'test_type' => ['required', 'string', 'in:'.implode(',', Assessment::TEST_TYPES)],
            'position_id' => ['nullable', 'exists:positions,id'],
            'description' => ['nullable', 'string'],
            'duration' => ['required', 'integer', 'min:5', 'max:180'],
            'passing_score' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->has('is_active');

        $assessment->update($validated);

        $user = Auth::user();
        $prefix = $user && $user->role && $user->role->name === 'admin' ? 'admin' : 'hr';

        return redirect()->route("{$prefix}.assessments.show", $assessment->id)
            ->with('success', 'Data tes berhasil diperbarui.');
    }

    /**
     * Remove the specified assessment from storage.
     */
    public function destroy(Assessment $assessment): RedirectResponse
    {
        $title = $assessment->title;
        $assessment->delete();

        $user = Auth::user();
        $prefix = $user && $user->role && $user->role->name === 'admin' ? 'admin' : 'hr';

        return redirect()->route("{$prefix}.assessments.index")
            ->with('success', "Tes '{$title}' berhasil dihapus.");
    }

    /**
     * Add a question with its multiple-choice answers into Question Bank.
     */
    public function storeQuestion(Request $request, Assessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
            'explanation' => ['nullable', 'string'],
            'options' => ['required', 'array', 'min:2', 'max:6'],
            'options.*' => ['required', 'string'],
            'correct_option' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated, $assessment) {
            $question = AssessmentQuestion::create([
                'assessment_id' => $assessment->id,
                'question_text' => $validated['question_text'],
                'points' => $validated['points'],
                'explanation' => $validated['explanation'] ?? null,
            ]);

            foreach ($validated['options'] as $index => $optionText) {
                AssessmentAnswer::create([
                    'assessment_question_id' => $question->id,
                    'answer_text' => $optionText,
                    'is_correct' => ((int) $validated['correct_option'] === (int) $index),
                ]);
            }
        });

        $user = Auth::user();
        $prefix = $user && $user->role && $user->role->name === 'admin' ? 'admin' : 'hr';

        return redirect()->route("{$prefix}.assessments.show", $assessment->id)
            ->with('success', 'Soal baru berhasil ditambahkan ke Bank Soal.');
    }

    /**
     * Remove a question from Question Bank.
     */
    public function destroyQuestion(Assessment $assessment, AssessmentQuestion $question): RedirectResponse
    {
        $question->delete();

        $user = Auth::user();
        $prefix = $user && $user->role && $user->role->name === 'admin' ? 'admin' : 'hr';

        return redirect()->route("{$prefix}.assessments.show", $assessment->id)
            ->with('success', 'Butir soal berhasil dihapus.');
    }

    // =========================================================================
    // CANDIDATE ONLINE ASSESSMENT METHODS
    // =========================================================================

    /**
     * Display candidate's available tests & test results.
     */
    public function candidateIndex(): View
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->first();

        // Get candidate's applied position IDs
        $appliedPositionIds = $candidate
            ? $candidate->applications()->pluck('position_id')->toArray()
            : [];

        // Active tests for all positions or candidate's applied positions
        $assessments = Assessment::where('is_active', true)
            ->where(function ($query) use ($appliedPositionIds) {
                $query->whereNull('position_id')
                    ->orWhereIn('position_id', $appliedPositionIds);
            })
            ->with(['position', 'questions'])
            ->withCount('questions')
            ->get();

        $myResults = $candidate
            ? $candidate->testResults()->with('assessment')->latest()->get()
            : collect();

        return view('kadidat.assessments.index', [
            'assessments' => $assessments,
            'myResults' => $myResults,
            'candidate' => $candidate,
        ]);
    }

    /**
     * Open candidate test taker session with timer countdown.
     */
    public function candidateTake(Assessment $assessment): View|RedirectResponse
    {
        if (! $assessment->is_active) {
            return redirect()->route('kadidat.assessments.index')
                ->with('error', 'Tes ini saat ini tidak sedang dibuka.');
        }

        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->first();

        if (! $candidate) {
            return redirect()->route('kadidat.dashboard')
                ->with('error', 'Profil kandidat belum terdaftar.');
        }

        // Check if candidate already has a completed result
        $existingResult = CandidateTestResult::where('candidate_id', $candidate->id)
            ->where('assessment_id', $assessment->id)
            ->whereNotNull('completed_at')
            ->first();

        if ($existingResult) {
            return redirect()->route('kadidat.assessments.result', $existingResult->id)
                ->with('info', 'Anda telah menyelesaikan tes ini sebelumnya.');
        }

        $questions = $assessment->questions()->with('answers')->get();

        if ($questions->isEmpty()) {
            return redirect()->route('kadidat.assessments.index')
                ->with('error', 'Bank soal untuk tes ini belum tersedia.');
        }

        return view('kadidat.assessments.take', [
            'assessment' => $assessment,
            'questions' => $questions,
            'candidate' => $candidate,
        ]);
    }

    /**
     * Submit candidate's assessment answers, auto calculate score, and determine result.
     */
    public function candidateSubmit(Request $request, Assessment $assessment): RedirectResponse
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->firstOrFail();

        $answersInput = $request->input('answers', []); // [question_id => answer_id]
        $durationSpent = (int) $request->input('duration_spent', 0);

        $questions = $assessment->questions()->with('answers')->get();
        $totalQuestions = $questions->count();
        $totalPoints = $questions->sum('points');
        $earnedPoints = 0;
        $correctCount = 0;

        foreach ($questions as $question) {
            $selectedAnswerId = $answersInput[$question->id] ?? null;
            if ($selectedAnswerId) {
                $correctAnswer = $question->answers->firstWhere('is_correct', true);
                if ($correctAnswer && (int) $correctAnswer->id === (int) $selectedAnswerId) {
                    $earnedPoints += $question->points;
                    $correctCount++;
                }
            }
        }

        $calculatedScore = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100, 2) : 0;
        $isPassed = $calculatedScore >= $assessment->passing_score;
        $resultStatus = $isPassed ? 'passed' : 'failed';

        // Find candidate application matching the assessment's position (if any)
        $application = $candidate->applications()
            ->when($assessment->position_id, function ($q) use ($assessment) {
                $q->where('position_id', $assessment->position_id);
            })
            ->latest()
            ->first();

        $testResult = CandidateTestResult::create([
            'candidate_id' => $candidate->id,
            'candidate_application_id' => $application?->id,
            'assessment_id' => $assessment->id,
            'score' => $calculatedScore,
            'result' => $resultStatus,
            'duration_spent' => $durationSpent,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctCount,
            'answers_payload' => $answersInput,
            'started_at' => Carbon::now()->subSeconds($durationSpent),
            'completed_at' => Carbon::now(),
            'notes' => $isPassed
                ? "Lolos passing score ({$assessment->passing_score}%)."
                : "Belum memenuhi passing score ({$assessment->passing_score}%).",
        ]);

        // If candidate's application was in assessment stage and passed, optionally update match score
        if ($application && $application->stage === 'assessment' && $isPassed) {
            $application->update([
                'match_score' => max($application->match_score ?? 80, (int) $calculatedScore),
            ]);
        }

        return redirect()->route('kadidat.assessments.result', $testResult->id)
            ->with('success', 'Jawaban tes berhasil dikirim dan dinilai.');
    }

    /**
     * Display test score and result evaluation to candidate.
     */
    public function candidateResult(CandidateTestResult $result): View|RedirectResponse
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->first();

        // Ensure candidate owns this result or is Admin/HR
        $userRole = $user->role?->name ?? 'guest';
        if ($userRole === 'kandidat' && (! $candidate || $result->candidate_id !== $candidate->id)) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $result->load(['assessment.position', 'assessment.questions.answers']);

        return view('kadidat.assessments.result', [
            'result' => $result,
            'assessment' => $result->assessment,
        ]);
    }
}
