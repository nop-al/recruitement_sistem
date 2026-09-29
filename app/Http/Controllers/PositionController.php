<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Candidate;
use App\Models\CandidateApplication;
use App\Models\CandidateProfile;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PositionController extends Controller
{
    /**
     * Display a listing of job vacancies.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $userRole = $user?->role?->name;

        $query = Position::withCount('applications')->latest();

        // If candidate, default to active positions only unless specified
        if ($userRole === 'kandidat') {
            $query->where('status', 'active');
        } elseif ($request->filled('status') && in_array($request->query('status'), ['active', 'draft', 'closed'], true)) {
            $query->where('status', $request->query('status'));
        }

        // Search by keyword
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->query('department'));
        }

        // Filter by job type
        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        $positions = $query->paginate(9)->withQueryString();

        // Get unique departments for filter dropdown
        $departments = Department::where('is_active', true)->orderBy('name')->pluck('name');
        if ($departments->isEmpty()) {
            $departments = Position::distinct()->orderBy('department')->pluck('department');
        }

        // Check which positions candidate has already applied to
        $appliedPositionIds = [];
        $candidate = null;
        $hasActiveOffering = false;
        if ($user && $userRole === 'kandidat') {
            $candidate = Candidate::where('user_id', $user->id)->first();
            if ($candidate) {
                $appliedPositionIds = $candidate->applications()->pluck('position_id')->toArray();
                $hasActiveOffering = $candidate->hasActiveOffering();
            }
        }

        return view('positions.index', [
            'positions' => $positions,
            'departments' => $departments,
            'appliedPositionIds' => $appliedPositionIds,
            'candidate' => $candidate,
            'hasActiveOffering' => $hasActiveOffering,
            'searchTerm' => $request->query('search'),
            'selectedDepartment' => $request->query('department'),
            'selectedType' => $request->query('type'),
            'selectedStatus' => $request->query('status'),
            'totalActive' => Position::where('status', 'active')->count(),
            'totalAll' => Position::count(),
        ]);
    }

    /**
     * Display the specified job position details.
     */
    public function show(Position $position): View
    {
        $user = Auth::user();
        $userRole = $user?->role?->name;

        // Eager load applications with candidate profile for Admin & HR
        $position->load([
            'applications.candidate.profile',
        ]);

        $hasApplied = false;
        $userApplication = null;
        $candidate = null;
        $hasActiveOffering = false;

        if ($user && $userRole === 'kandidat') {
            $candidate = Candidate::with('profile')->where('user_id', $user->id)->first();
            if ($candidate) {
                $userApplication = $candidate->applications()->where('position_id', $position->id)->first();
                $hasApplied = (bool) $userApplication;
                $hasActiveOffering = $candidate->hasActiveOffering();
            }
        }

        return view('positions.detail', [
            'position' => $position,
            'hasApplied' => $hasApplied,
            'userApplication' => $userApplication,
            'candidate' => $candidate,
            'hasActiveOffering' => $hasActiveOffering,
        ]);
    }

    /**
     * Show the form for creating a new job position.
     */
    public function create(): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->pluck('name');
        if ($departments->isEmpty()) {
            $departments = collect([
                'Technology & Engineering',
                'Product & Design',
                'Human Resources',
                'Data & Analytics',
                'Finance & Accounting',
                'Marketing & Growth',
                'Sales & Business Development',
            ]);
        }

        return view('positions.create', [
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created job position in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'experience_level' => ['required', 'string', 'max:100'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'quota' => ['required', 'integer', 'min:1'],
            'deadline' => ['nullable', 'date'],
            'status' => ['required', 'in:active,draft,closed'],
            'is_internal' => ['nullable', 'boolean'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
        ], [
            'title.required' => 'Judul posisi lowongan wajib diisi.',
            'department.required' => 'Departemen wajib diisi.',
            'location.required' => 'Lokasi kerja wajib diisi.',
            'type.required' => 'Tipe pekerjaan wajib dipilih.',
            'experience_level.required' => 'Tingkat pengalaman wajib diisi.',
            'quota.required' => 'Kuota pelamar wajib diisi.',
            'quota.min' => 'Kuota minimal 1 orang.',
            'description.required' => 'Deskripsi pekerjaan wajib diisi.',
        ]);

        $validated['is_internal'] = $request->boolean('is_internal');

        $position = Position::create($validated);

        // Record activity log
        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin/HR',
            'action' => 'CREATE_POSITION',
            'module' => 'Job Vacancy',
            'description' => "Membuka lowongan baru: {$position->title} ({$position->department}).",
            'ip_address' => $request->ip(),
        ]);

        $userRole = Auth::user()?->role?->name;
        $targetRoute = ($userRole === 'admin') ? 'admin.dashboard' : 'hr.dashboard';

        return redirect()->route($targetRoute)
            ->with('success', "Lowongan kerja '{$position->title}' berhasil disimpan dan dipublikasikan!");
    }

    /**
     * Show the form for editing the specified job position.
     */
    public function edit(Position $position): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->pluck('name');
        if ($departments->isEmpty()) {
            $departments = collect([
                'Technology & Engineering',
                'Product & Design',
                'Human Resources',
                'Data & Analytics',
                'Finance & Accounting',
                'Marketing & Growth',
                'Sales & Business Development',
            ]);
        }

        return view('positions.edit', [
            'position' => $position,
            'departments' => $departments,
        ]);
    }

    /**
     * Update the specified job position in storage.
     */
    public function update(Request $request, Position $position): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:50'],
            'experience_level' => ['required', 'string', 'max:100'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'quota' => ['required', 'integer', 'min:1'],
            'deadline' => ['nullable', 'date'],
            'status' => ['required', 'in:active,draft,closed'],
            'is_internal' => ['nullable', 'boolean'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
        ], [
            'title.required' => 'Judul posisi lowongan wajib diisi.',
            'department.required' => 'Departemen wajib diisi.',
            'location.required' => 'Lokasi kerja wajib diisi.',
            'type.required' => 'Tipe pekerjaan wajib dipilih.',
            'experience_level.required' => 'Tingkat pengalaman wajib diisi.',
            'quota.required' => 'Kuota pelamar wajib diisi.',
            'quota.min' => 'Kuota minimal 1 orang.',
            'description.required' => 'Deskripsi pekerjaan wajib diisi.',
        ]);

        $validated['is_internal'] = $request->boolean('is_internal');

        $position->update($validated);

        // Record activity log
        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin/HR',
            'action' => 'UPDATE_POSITION',
            'module' => 'Job Vacancy',
            'description' => "Memperbarui lowongan: {$position->title} ({$position->department}).",
            'ip_address' => $request->ip(),
        ]);

        $userRole = Auth::user()?->role?->name;
        $targetRoute = ($userRole === 'admin') ? 'admin.positions.index' : 'hr.positions.index';

        return redirect()->route($targetRoute)
            ->with('success', "Lowongan kerja '{$position->title}' berhasil diperbarui!");
    }

    /**
     * Apply for a job vacancy (Candidate only).
     */
    public function apply(Request $request, Position $position): RedirectResponse
    {
        $user = Auth::user();

        // Ensure candidate record exists
        $candidate = Candidate::where('user_id', $user->id)->first();
        if (! $candidate) {
            $nameParts = explode(' ', trim($user->name), 2);
            $candidate = Candidate::create([
                'user_id' => $user->id,
                'first_name' => $nameParts[0] ?? 'Kandidat',
                'last_name' => $nameParts[1] ?? '',
                'email' => $user->email,
                'phone_number' => '081200000000',
                'is_active' => true,
            ]);
            CandidateProfile::create([
                'candidate_id' => $candidate->id,
            ]);
        }

        // Check if already applied
        $existing = CandidateApplication::where('candidate_id', $candidate->id)
            ->where('position_id', $position->id)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('info', 'Anda sudah pernah mengajukan lamaran untuk posisi ini sebelumnya.');
        }

        // 1. Validation: check if user is blacklisted
        if ($user->is_blacklisted) {
            return redirect()->back()
                ->with('error', 'Akun Anda berstatus blacklist sehingga tidak dapat mengajukan lamaran pekerjaan.');
        }

        // 2. Validation: candidate must have a valid 16-digit NIK
        $profile = $candidate->profile;
        $nik = trim($profile?->nik ?? '');

        if (empty($nik)) {
            return redirect()->route('kadidat.profile.edit')
                ->with('error', 'Silakan lengkapi Nomor Induk Kependudukan (NIK) 16 digit pada profil Anda terlebih dahulu sebelum mengajukan lamaran pekerjaan.');
        }

        if (strlen($nik) !== 16 || ! ctype_digit($nik)) {
            return redirect()->route('kadidat.profile.edit')
                ->with('error', 'Nomor Induk Kependudukan (NIK) Anda tidak valid (harus berjumlah 16 digit angka). Silakan perbarui profil Anda.');
        }

        // 3. Validation: ensure 1 candidate 1 account with 1 NIK (no duplicate NIK across accounts)
        $duplicateNik = CandidateProfile::where('nik', $nik)
            ->where('candidate_id', '!=', $candidate->id)
            ->exists();

        if ($duplicateNik) {
            return redirect()->back()
                ->with('error', 'NIK Anda terdeteksi sudah digunakan oleh akun kandidat lain. 1 NIK hanya dapat digunakan untuk 1 akun kandidat.');
        }

        // 4. Validation: jika sudah offering, tidak bisa apply lowongan kerja lagi, kecuali lowongan kerja internal
        if ($candidate->hasActiveOffering() && ! $position->is_internal) {
            return redirect()->back()
                ->with('error', 'Anda tidak dapat melamar lowongan kerja ini karena sudah berada pada tahap penawaran kerja (Offering). Anda hanya diperbolehkan melamar lowongan kerja internal.');
        }

        $request->validate([
            'cover_letter' => ['nullable', 'string', 'max:2000'],
        ]);

        $resumePath = $candidate->profile?->resume_path;

        CandidateApplication::create([
            'candidate_id' => $candidate->id,
            'position_id' => $position->id,
            'stage' => 'screening',
            'status' => 'pending',
            'match_score' => rand(85, 96),
            'resume_url' => $resumePath,
            'cover_letter' => $request->input('cover_letter'),
            'applied_at' => now(),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => 'SUBMIT_APPLICATION',
            'module' => 'Career Portal',
            'description' => "Pelamar {$user->name} melamar posisi {$position->title}.",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('kadidat.dashboard')
            ->with('success', "Lamaran Anda untuk posisi '{$position->title}' berhasil dikirim! Silakan pantau tahapan seleksi Anda.");
    }

    /**
     * Remove the specified job position from storage.
     */
    public function destroy(Position $position): RedirectResponse
    {
        $title = $position->title;
        $position->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name ?? 'Admin/HR',
            'action' => 'DELETE_POSITION',
            'module' => 'Job Vacancy',
            'description' => "Menghapus lowongan kerja: {$title}.",
            'ip_address' => request()->ip(),
        ]);

        $userRole = Auth::user()?->role?->name;
        $targetRoute = ($userRole === 'admin') ? 'admin.positions.index' : 'hr.positions.index';

        return redirect()->route($targetRoute)
            ->with('success', "Lowongan pekerjaan '{$title}' berhasil dihapus dari sistem.");
    }
}
