<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateApplication;
use App\Models\Department;
use App\Models\Offering;
use App\Models\Onboarding;
use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferingController extends Controller
{
    /**
     * Display a listing of offerings for Admin/HR.
     */
    public function index(Request $request): View
    {
        $query = Offering::with(['candidate.user', 'position', 'application']);

        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->whereHas('candidate', function ($cq) use ($search) {
                    $cq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('position', function ($pq) use ($search) {
                    $pq->where('title', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status') && array_key_exists($request->query('status'), Offering::STATUSES)) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->query('position_id'));
        }

        $offerings = $query->latest()->paginate(10)->withQueryString();
        $positions = Position::orderBy('title')->get();

        return view('offerings.index', [
            'offerings' => $offerings,
            'positions' => $positions,
            'statuses' => Offering::STATUSES,
            'selectedStatus' => $request->query('status'),
            'selectedPosition' => $request->query('position_id'),
            'search' => $request->query('search'),
        ]);
    }

    /**
     * Show the form for creating a new offering.
     */
    public function create(Request $request): View
    {
        $positions = Position::where('status', 'active')->orderBy('title')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $candidates = Candidate::with('user')->orderBy('first_name')->get();

        $selectedApplication = null;
        if ($request->filled('application_id')) {
            $selectedApplication = CandidateApplication::with(['candidate.user', 'position'])->find($request->query('application_id'));
        }

        return view('offerings.create', [
            'positions' => $positions,
            'departments' => $departments,
            'candidates' => $candidates,
            'selectedApplication' => $selectedApplication,
            'employmentTypes' => Offering::EMPLOYMENT_TYPES,
            'statuses' => Offering::STATUSES,
        ]);
    }

    /**
     * Store a newly created offering in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'candidate_id' => ['required', 'exists:candidates,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'candidate_application_id' => ['nullable', 'exists:candidate_applications,id'],
            'department' => ['required', 'string', 'max:255'],
            'salary' => ['required', 'numeric', 'min:0'],
            'employment_type' => ['required', 'string', 'in:'.implode(',', Offering::EMPLOYMENT_TYPES)],
            'join_date' => ['required', 'date'],
            'benefits' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(Offering::STATUSES))],
        ]);

        $validated['salary_offered'] = $validated['salary'];
        $validated['start_date'] = $validated['join_date'];

        if ($validated['status'] === Offering::STATUS_SENT) {
            $validated['sent_at'] = Carbon::now();
        }

        $offering = Offering::create($validated);

        // Transition application stage if application id provided
        if (! empty($validated['candidate_application_id'])) {
            $app = CandidateApplication::find($validated['candidate_application_id']);
            if ($app) {
                $app->update(['stage' => 'offering']);
            }
        }

        $user = Auth::user();
        $prefix = $user && $user->role && $user->role->name === 'admin' ? 'admin' : 'hr';

        return redirect()->route("{$prefix}.offerings.show", $offering->id)
            ->with('success', 'Surat penawaran kerja (Offering Letter) berhasil dibuat.');
    }

    /**
     * Display the specified offering letter.
     */
    public function show(Offering $offering): View
    {
        $offering->load(['candidate.user', 'candidate.profile', 'position', 'application']);

        return view('offerings.show', [
            'offering' => $offering,
        ]);
    }

    /**
     * Show the form for editing the specified offering.
     */
    public function edit(Offering $offering): View
    {
        $positions = Position::orderBy('title')->get();
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('offerings.edit', [
            'offering' => $offering,
            'positions' => $positions,
            'departments' => $departments,
            'employmentTypes' => Offering::EMPLOYMENT_TYPES,
            'statuses' => Offering::STATUSES,
        ]);
    }

    /**
     * Update the specified offering in storage.
     */
    public function update(Request $request, Offering $offering): RedirectResponse
    {
        $validated = $request->validate([
            'department' => ['required', 'string', 'max:255'],
            'salary' => ['required', 'numeric', 'min:0'],
            'employment_type' => ['required', 'string', 'in:'.implode(',', Offering::EMPLOYMENT_TYPES)],
            'join_date' => ['required', 'date'],
            'benefits' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:'.implode(',', array_keys(Offering::STATUSES))],
        ]);

        $validated['salary_offered'] = $validated['salary'];
        $validated['start_date'] = $validated['join_date'];

        if ($validated['status'] === Offering::STATUS_SENT && ! $offering->sent_at) {
            $validated['sent_at'] = Carbon::now();
        }

        $offering->update($validated);

        $user = Auth::user();
        $prefix = $user && $user->role && $user->role->name === 'admin' ? 'admin' : 'hr';

        return redirect()->route("{$prefix}.offerings.show", $offering->id)
            ->with('success', 'Data surat penawaran kerja berhasil diperbarui.');
    }

    /**
     * Mark offering as sent to the candidate.
     */
    public function send(Offering $offering): RedirectResponse
    {
        $offering->update([
            'status' => Offering::STATUS_SENT,
            'sent_at' => Carbon::now(),
        ]);

        if ($offering->application) {
            $offering->application->update(['stage' => 'offering']);
        }

        $user = Auth::user();
        $prefix = $user && $user->role && $user->role->name === 'admin' ? 'admin' : 'hr';

        return redirect()->route("{$prefix}.offerings.show", $offering->id)
            ->with('success', 'Surat penawaran telah resmi dikirimkan kepada kandidat.');
    }

    /**
     * Remove the specified offering from storage.
     */
    public function destroy(Offering $offering): RedirectResponse
    {
        $offering->delete();

        $user = Auth::user();
        $prefix = $user && $user->role && $user->role->name === 'admin' ? 'admin' : 'hr';

        return redirect()->route("{$prefix}.offerings.index")
            ->with('success', 'Surat penawaran kerja berhasil dihapus.');
    }

    // =========================================================================
    // CANDIDATE OFFERING PORTAL METHODS
    // =========================================================================

    /**
     * Display candidate's received offering letters.
     */
    public function candidateIndex(): View
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->firstOrFail();

        $offerings = Offering::where('candidate_id', $candidate->id)
            ->with(['position', 'application'])
            ->latest()
            ->get();

        return view('kadidat.offerings.index', [
            'offerings' => $offerings,
            'candidate' => $candidate,
        ]);
    }

    /**
     * Display candidate's detailed offering letter with response buttons.
     */
    public function candidateShow(Offering $offering): View
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->firstOrFail();

        if ($offering->candidate_id !== $candidate->id) {
            abort(403, 'Akses penawaran ini tidak diizinkan.');
        }

        $offering->load(['position', 'application']);

        return view('kadidat.offerings.show', [
            'offering' => $offering,
            'candidate' => $candidate,
        ]);
    }

    /**
     * Candidate responds to offering (accept or reject).
     * Accepting automatically transitions to Onboarding stage!
     */
    public function candidateRespond(Request $request, Offering $offering): RedirectResponse
    {
        $user = Auth::user();
        $candidate = Candidate::where('user_id', $user->id)->firstOrFail();

        if ($offering->candidate_id !== $candidate->id) {
            abort(403, 'Akses penawaran ini tidak diizinkan.');
        }

        $validated = $request->validate([
            'action' => ['required', 'in:accept,reject'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['action'] === 'accept') {
            $offering->update([
                'status' => Offering::STATUS_ACCEPTED,
                'responded_at' => Carbon::now(),
                'notes' => $validated['notes'] ? ($offering->notes.' [Catatan Penerimaan: '.$validated['notes'].']') : $offering->notes,
            ]);

            // Update application stage to onboarding
            if ($offering->application) {
                $offering->application->update([
                    'stage' => 'onboarding',
                    'status' => 'passed',
                ]);
            }

            // Automatically initialize onboarding record
            $onboarding = Onboarding::firstOrCreate([
                'candidate_id' => $candidate->id,
                'position_id' => $offering->position_id,
            ], [
                'candidate_application_id' => $offering->candidate_application_id,
                'offering_id' => $offering->id,
                'start_date' => $offering->getEffectiveJoinDateAttribute() ?? Carbon::now()->addDays(14),
                'join_date' => $offering->getEffectiveJoinDateAttribute() ?? Carbon::now()->addDays(14),
                'status' => Onboarding::STATUS_PENDING,
                'checklist_percentage' => 0,
                'notes' => 'Kandidat menerima penawaran kerja. Silakan lengkapi berkas onboarding.',
            ]);

            return redirect()->route('kadidat.onboarding.index')
                ->with('success', 'Selamat! Anda telah menerima penawaran kerja. Silakan mulai melengkapi berkas checklist onboarding berikut.');
        }

        // Reject action
        $offering->update([
            'status' => Offering::STATUS_REJECTED,
            'responded_at' => Carbon::now(),
            'notes' => $validated['notes'] ? ($offering->notes.' [Alasan Penolakan: '.$validated['notes'].']') : $offering->notes,
        ]);

        if ($offering->application) {
            $offering->application->update([
                'status' => 'rejected',
                'hr_notes' => 'Kandidat menolak penawaran kerja: '.($validated['notes'] ?? 'Tanpa catatan tambahan.'),
            ]);
        }

        return redirect()->route('kadidat.offerings.index')
            ->with('info', 'Status penawaran kerja telah diperbarui menjadi ditolak.');
    }

    /**
     * Print-ready official job offering letter document (A4 / Save to PDF).
     * Accessible by Admin, HR, and the Candidate owner.
     */
    public function printLetter(Offering $offering): View
    {
        $user = Auth::user();
        $userRole = $user?->role?->name;

        // Check permission
        if (! in_array($userRole, ['admin', 'hr'], true)) {
            $candidate = Candidate::where('user_id', $user->id)->first();
            if (! $candidate || $offering->candidate_id !== $candidate->id) {
                abort(403, 'Anda tidak memiliki izin untuk mencetak dokumen offering ini.');
            }
        }

        $offering->loadMissing([
            'candidate.profile',
            'position',
            'application',
        ]);

        return view('offerings.print', [
            'offering' => $offering,
            'candidate' => $offering->candidate,
        ]);
    }
}
