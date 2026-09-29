<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Candidate;
use App\Models\CandidateApplication;
use App\Models\InterviewAssessment;
use App\Models\Position;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InterviewController extends Controller
{
    /**
     * Display a listing of all interview sessions with filters.
     */
    public function index(Request $request): View
    {
        $query = InterviewAssessment::with(['candidate.profile', 'position', 'interviewer'])
            ->latest('scheduled_at');

        // Search candidate name, email, or position title
        if ($request->filled('search')) {
            $term = trim($request->query('search'));
            $query->where(function ($q) use ($term) {
                $q->whereHas('candidate', function ($cq) use ($term) {
                    $cq->where('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                })->orWhereHas('position', function ($pq) use ($term) {
                    $pq->where('title', 'like', "%{$term}%")
                        ->orWhere('department', 'like', "%{$term}%");
                });
            });
        }

        // Filter by status (scheduled, completed, cancelled)
        if ($request->filled('status') && in_array($request->query('status'), ['scheduled', 'completed', 'cancelled'], true)) {
            $query->where('status', $request->query('status'));
        }

        // Filter by position
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->query('position_id'));
        }

        // Filter by platform (zoom vs google meet vs offline)
        if ($request->filled('platform')) {
            if ($request->query('platform') === 'zoom') {
                $query->where('room_link', 'like', '%zoom%');
            } elseif ($request->query('platform') === 'meet') {
                $query->where('room_link', 'like', '%meet.google%');
            } elseif ($request->query('platform') === 'offline') {
                $query->where(function ($q) {
                    $q->where('interview_method', 'offline')
                        ->orWhereNull('room_link');
                });
            }
        }

        $interviews = $query->paginate(10)->withQueryString();

        // Metrics for summary cards
        $totalAssigned = InterviewAssessment::count();
        $totalScheduled = InterviewAssessment::where('status', 'scheduled')->count();
        $totalCompleted = InterviewAssessment::where('status', 'completed')->count();
        $avgScore = InterviewAssessment::where('status', 'completed')->avg('total_score') ?? 0;

        $positions = Position::orderBy('title')->get();

        return view('interview.index', [
            'interviews' => $interviews,
            'totalAssigned' => $totalAssigned,
            'totalScheduled' => $totalScheduled,
            'totalCompleted' => $totalCompleted,
            'avgScore' => round($avgScore, 1),
            'positions' => $positions,
            'searchTerm' => $request->query('search'),
            'selectedStatus' => $request->query('status'),
            'selectedPosition' => $request->query('position_id'),
            'selectedPlatform' => $request->query('platform'),
        ]);
    }

    /**
     * Show the form for scheduling a new online interview.
     */
    public function create(Request $request): View
    {
        // Applicants who applied and can be invited to interview
        $applications = CandidateApplication::with(['candidate.profile', 'position'])
            ->whereIn('stage', ['screening', 'assessment', 'interview'])
            ->latest('applied_at')
            ->get();

        // Also get all candidates in case scheduling without existing application
        $candidates = Candidate::with('profile')->orderBy('first_name')->get();
        $positions = Position::where('status', 'active')->orderBy('title')->get();

        // Available interviewers (Interviewer, HR, Admin)
        $interviewers = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['interviewer', 'hr', 'admin']);
        })->orderBy('name')->get();

        // Generate unique meeting links by default
        $randomMeetCode = strtolower(Str::random(3)).'-'.strtolower(Str::random(4)).'-'.strtolower(Str::random(3));
        $uniqueMeetLink = 'https://meet.google.com/'.$randomMeetCode;

        $randomZoomId = rand(100, 999).rand(100, 999).rand(1000, 9999);
        $randomZoomPwd = Str::random(6);
        $uniqueZoomLink = 'https://zoom.us/j/'.$randomZoomId.'?pwd='.$randomZoomPwd;

        return view('interview.create', [
            'applications' => $applications,
            'candidates' => $candidates,
            'positions' => $positions,
            'interviewers' => $interviewers,
            'uniqueMeetLink' => $uniqueMeetLink,
            'uniqueZoomLink' => $uniqueZoomLink,
            'selectedCandidateId' => $request->query('candidate_id'),
            'selectedPositionId' => $request->query('position_id'),
        ]);
    }

    /**
     * Store a newly created interview schedule.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'candidate_id' => ['required', 'exists:candidates,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'interviewer_id' => ['nullable', 'exists:users,id'],
            'interview_type' => ['required', 'string', 'max:255'],
            'interview_method' => ['nullable', 'string', 'in:online,offline'],
            'scheduled_at' => ['required', 'date'],
            'platform' => ['nullable', 'string', 'in:google_meet,zoom,other,offline'],
            'room_link' => ['nullable', 'string', 'max:500'],
            'location' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['interview_method'] = $validated['interview_method'] ?? 'online';

        if ($validated['interview_method'] === 'online' && empty($validated['room_link'])) {
            return redirect()->back()->withInput()->withErrors(['room_link' => 'Tautan meeting virtual wajib diisi untuk wawancara online.']);
        }

        if ($validated['interview_method'] === 'offline' && empty($validated['location'])) {
            return redirect()->back()->withInput()->withErrors(['location' => 'Nama gedung / ruangan / alamat kantor wajib diisi untuk wawancara offline.']);
        }

        $interviewerId = $validated['interviewer_id'] ?? Auth::id();

        $interview = InterviewAssessment::create([
            'candidate_id' => $validated['candidate_id'],
            'position_id' => $validated['position_id'],
            'interviewer_id' => $interviewerId,
            'interview_type' => $validated['interview_type'],
            'interview_method' => $validated['interview_method'],
            'scheduled_at' => $validated['scheduled_at'],
            'room_link' => $validated['interview_method'] === 'online' ? $validated['room_link'] : null,
            'location' => $validated['interview_method'] === 'offline' ? $validated['location'] : null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'scheduled',
        ]);

        // Automatically advance candidate application stage to interview if applicable
        $application = CandidateApplication::where('candidate_id', $validated['candidate_id'])
            ->where('position_id', $validated['position_id'])
            ->first();

        if ($application && in_array($application->stage, ['screening', 'assessment'], true)) {
            $application->update([
                'stage' => 'interview',
                'status' => 'in_review',
            ]);
        }

        $methodLabel = $validated['interview_method'] === 'offline' ? 'tatap muka (offline)' : 'online';
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'schedule_interview',
            'description' => "Menjadwalkan wawancara {$methodLabel} untuk ".($interview->candidate?->full_name ?? 'Kandidat').' pada posisi '.($interview->position?->title ?? '-'),
        ]);

        return redirect()->route($this->getIndexRoute())
            ->with('success', 'Sesi wawancara online dengan link ruang virtual berhasil dijadwalkan!');
    }

    /**
     * Display interview detail and scoring evaluation sheet.
     */
    public function show(InterviewAssessment $interview): View
    {
        $interview->load([
            'candidate.profile',
            'candidate.education',
            'candidate.experiences',
            'candidate.skills',
            'candidate.user',
            'position',
            'interviewer',
        ]);

        $application = CandidateApplication::where('candidate_id', $interview->candidate_id)
            ->where('position_id', $interview->position_id)
            ->first();

        return view('interview.show', [
            'interview' => $interview,
            'candidate' => $interview->candidate,
            'position' => $interview->position,
            'application' => $application,
        ]);
    }

    /**
     * Submit interview scores and evaluation recommendation.
     */
    public function evaluate(Request $request, InterviewAssessment $interview): RedirectResponse
    {
        $validated = $request->validate([
            'technical_score' => ['required', 'integer', 'min:0', 'max:100'],
            'communication_score' => ['required', 'integer', 'min:0', 'max:100'],
            'culture_score' => ['required', 'integer', 'min:0', 'max:100'],
            'recommendation' => ['required', 'in:pass,hold,fail'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $totalScore = (int) round(($validated['technical_score'] + $validated['communication_score'] + $validated['culture_score']) / 3);

        $interview->update([
            'technical_score' => $validated['technical_score'],
            'communication_score' => $validated['communication_score'],
            'culture_score' => $validated['culture_score'],
            'total_score' => $totalScore,
            'recommendation' => $validated['recommendation'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'completed',
        ]);

        // If recommended to pass, update candidate application status
        $application = CandidateApplication::where('candidate_id', $interview->candidate_id)
            ->where('position_id', $interview->position_id)
            ->first();

        if ($application) {
            if ($validated['recommendation'] === 'pass') {
                $application->update([
                    'status' => 'passed',
                    'match_score' => max($application->match_score ?? 0, $totalScore),
                ]);
            } elseif ($validated['recommendation'] === 'fail') {
                $application->update([
                    'status' => 'rejected',
                ]);
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'evaluate_interview',
            'description' => 'Menginput hasil penilaian wawancara '.($interview->candidate?->full_name ?? 'Kandidat').' dengan rekomendasi: '.strtoupper($validated['recommendation']),
        ]);

        return redirect()->back()
            ->with('success', 'Hasil penilaian dan rekomendasi evaluasi wawancara berhasil disimpan!');
    }

    /**
     * Delete or cancel an interview session.
     */
    public function destroy(InterviewAssessment $interview): RedirectResponse
    {
        $candidateName = $interview->candidate?->full_name ?? 'Kandidat';
        $positionTitle = $interview->position?->title ?? 'Posisi';

        $interview->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'delete_interview',
            'description' => "Menghapus sesi wawancara untuk {$candidateName} ({$positionTitle})",
        ]);

        return redirect()->route($this->getIndexRoute())
            ->with('success', "Sesi wawancara untuk {$candidateName} berhasil dibatalkan dan dihapus.");
    }

    /**
     * Determine index route based on authenticated user's role.
     */
    private function getIndexRoute(): string
    {
        $roleName = Auth::user()?->role?->name;

        return match ($roleName) {
            'admin' => 'admin.interviews.index',
            'hr' => 'hr.interviews.index',
            default => 'interview.interviews.index',
        };
    }
}
