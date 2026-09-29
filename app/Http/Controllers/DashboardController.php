<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Candidate;
use App\Models\CandidateApplication;
use App\Models\CandidateProfile;
use App\Models\Department;
use App\Models\InterviewAssessment;
use App\Models\Offering;
use App\Models\Onboarding;
use App\Models\Position;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Default entry point: Redirect to user role dashboard if logged in, or show home landing page if guest.
     */
    public function index(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();

            return match ($user->role?->name) {
                'admin' => redirect()->route('admin.dashboard'),
                'hr' => redirect()->route('hr.dashboard'),
                'interviewer' => redirect()->route('interview.dashboard'),
                'kandidat' => redirect()->route('kadidat.dashboard'),
                default => redirect()->route('kadidat.dashboard'),
            };
        }

        return $this->home();
    }

    /**
     * Display the public recruitment landing home page.
     */
    public function home(): View
    {
        $featuredPositions = Position::where('status', 'active')
            ->withCount('applications')
            ->latest()
            ->take(8)
            ->get();

        $departments = Department::where('is_active', true)
            ->withCount('positions')
            ->orderBy('name')
            ->get();

        if ($departments->isEmpty()) {
            $deptNames = Position::where('status', 'active')->distinct()->pluck('department');
            $departments = $deptNames->map(function ($name) {
                return (object) [
                    'name' => $name,
                    'positions_count' => Position::where('status', 'active')->where('department', $name)->count(),
                ];
            });
        }

        $stats = [
            'total_jobs' => Position::where('status', 'active')->count(),
            'total_departments' => Department::where('is_active', true)->count() ?: Position::distinct('department')->count(),
            'total_candidates' => Candidate::count(),
            'total_applications' => CandidateApplication::count(),
        ];

        $settings = SystemSetting::getAll();

        return view('home', [
            'featuredPositions' => $featuredPositions,
            'departments' => $departments,
            'stats' => $stats,
            'settings' => $settings,
        ]);
    }

    /**
     * Display the public list of all active vacancy positions.
     */
    public function lowonganIndex(Request $request): View
    {
        $query = Position::where('status', 'active')->withCount('applications')->latest();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($dept = $request->input('dept')) {
            $query->where('department', $dept);
        }

        $positions = $query->paginate(12)->withQueryString();

        $departments = Department::where('is_active', true)->orderBy('name')->get();
        if ($departments->isEmpty()) {
            $deptNames = Position::where('status', 'active')->distinct()->pluck('department');
            $departments = $deptNames->map(fn ($name) => (object) ['name' => $name]);
        }

        $settings = SystemSetting::getAll();

        return view('public.lowongan-index', compact('positions', 'departments', 'settings'));
    }

    /**
     * Display the public detail of a single vacancy using an encrypted position ID.
     */
    public function lowonganShow(string $encryptedId): View
    {
        try {
            $positionId = decrypt($encryptedId);
        } catch (DecryptException) {
            abort(404);
        }

        $position = Position::where('status', 'active')
            ->withCount('applications')
            ->findOrFail($positionId);

        $settings = SystemSetting::getAll();

        return view('public.lowongan-show', compact('position', 'settings'));
    }

    /**
     * Quick status check by candidate NIK without requiring login.
     */
    public function checkStatusByNik(Request $request): JsonResponse
    {
        $request->validate([
            'nik' => ['required', 'string'],
        ], [
            'nik.required' => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
        ]);

        $nik = trim($request->input('nik'));

        if (strlen($nik) !== 16 || ! ctype_digit($nik)) {
            return response()->json([
                'success' => false,
                'message' => 'Format NIK tidak valid. NIK harus terdiri dari 16 digit angka sesuai KTP Anda.',
            ], 422);
        }

        $profile = CandidateProfile::with(['candidate.applications.position', 'candidate.user'])
            ->where('nik', $nik)
            ->first();

        if (! $profile || ! $profile->candidate) {
            return response()->json([
                'success' => false,
                'message' => 'Data pelamar dengan NIK tersebut tidak ditemukan di sistem. Pastikan 16 digit NIK yang Anda masukkan sudah benar atau silakan daftar akun baru.',
            ], 404);
        }

        $candidate = $profile->candidate;
        $applications = $candidate->applications()->with('position')->latest('applied_at')->get();

        $candidateInitials = $candidate->first_name.' '.(strlen($candidate->last_name ?? '') > 0 ? substr($candidate->last_name, 0, 1).'.' : '');

        if ($applications->isEmpty()) {
            return response()->json([
                'success' => true,
                'candidate_name' => $candidateInitials,
                'has_applications' => false,
                'message' => 'Akun Anda telah terdaftar, namun Anda belum pernah mengajukan lamaran pekerjaan untuk posisi apapun.',
                'applications' => [],
            ]);
        }

        $appsData = $applications->map(function ($app) {
            $stageInfo = match ($app->stage) {
                'screening' => [
                    'label' => 'Seleksi Berkas (Screening)',
                    'badge_class' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'progress' => 25,
                    'note' => 'Berkas lamaran dan CV Anda sedang dalam tahap peninjauan oleh tim rekrutmen.',
                ],
                'assessment' => [
                    'label' => 'Asesmen Online',
                    'badge_class' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                    'progress' => 50,
                    'note' => 'Tahap tes online / asesmen kompetensi kerja.',
                ],
                'interview' => [
                    'label' => 'Wawancara (Interview)',
                    'badge_class' => 'bg-amber-100 text-amber-800 border-amber-200',
                    'progress' => 70,
                    'note' => 'Tahap wawancara kompetensi dengan interviewer / HR.',
                ],
                'offering' => [
                    'label' => 'Penawaran Kerja (Offering)',
                    'badge_class' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'progress' => 88,
                    'note' => 'Surat Offering resmi telah diterbitkan. Silakan masuk untuk meninjau dan merespon.',
                ],
                'onboarding' => [
                    'label' => 'Onboarding Karyawan',
                    'badge_class' => 'bg-teal-100 text-teal-800 border-teal-200',
                    'progress' => 95,
                    'note' => 'Tahap verifikasi dokumen dan pengisian berkas kepegawaian.',
                ],
                'hired' => [
                    'label' => 'Diterima Bekerja',
                    'badge_class' => 'bg-emerald-600 text-white border-emerald-600',
                    'progress' => 100,
                    'note' => 'Selamat! Anda telah resmi diterima bergabung sebagai karyawan.',
                ],
                'rejected' => [
                    'label' => 'Belum Lolos',
                    'badge_class' => 'bg-rose-100 text-rose-800 border-rose-200',
                    'progress' => 100,
                    'note' => 'Kualifikasi Anda saat ini belum sesuai dengan kriteria yang dibutuhkan posisi ini.',
                ],
                default => [
                    'label' => ucfirst($app->stage),
                    'badge_class' => 'bg-slate-100 text-slate-800 border-slate-200',
                    'progress' => 15,
                    'note' => 'Tahapan seleksi sedang diproses.',
                ],
            };

            return [
                'id' => $app->id,
                'position_title' => $app->position?->title ?? 'Posisi Tidak Diketahui',
                'department' => $app->position?->department ?? '-',
                'is_internal' => (bool) $app->position?->is_internal,
                'applied_at' => $app->applied_at ? $app->applied_at->format('d M Y') : $app->created_at->format('d M Y'),
                'stage' => $app->stage,
                'status' => $app->status,
                'stage_label' => $stageInfo['label'],
                'badge_class' => $stageInfo['badge_class'],
                'progress' => $stageInfo['progress'],
                'note' => $stageInfo['note'],
            ];
        });

        return response()->json([
            'success' => true,
            'candidate_name' => $candidateInitials,
            'has_applications' => true,
            'applications' => $appsData,
        ]);
    }

    /**
     * Display the Administrator dashboard.
     */
    public function admin(): View
    {
        $totalUsers = User::count();
        $totalPositions = Position::count();
        $activePositions = Position::where('status', 'active')->count();
        $totalCandidates = Candidate::count();
        $totalApplications = CandidateApplication::count();

        $rolesCount = Role::withCount('users')->get();

        $recentUsers = User::with('role')->latest()->take(6)->get();
        $recentActivities = ActivityLog::latest()->take(8)->get();
        $positions = Position::withCount('applications')->latest()->take(5)->get();

        $systemInfo = [
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
            'environment' => config('app.env'),
            'database' => config('database.default'),
            'timezone' => config('app.timezone', 'Asia/Jakarta'),
        ];

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalPositions' => $totalPositions,
            'activePositions' => $activePositions,
            'totalCandidates' => $totalCandidates,
            'totalApplications' => $totalApplications,
            'rolesCount' => $rolesCount,
            'recentUsers' => $recentUsers,
            'recentActivities' => $recentActivities,
            'positions' => $positions,
            'systemInfo' => $systemInfo,
        ]);
    }

    /**
     * Display the HR / Recruiter dashboard.
     */
    public function hr(): View
    {
        $totalApplications = CandidateApplication::count();
        $screeningCount = CandidateApplication::where('stage', 'screening')->count();
        $assessmentCount = CandidateApplication::where('stage', 'assessment')->count();
        $interviewCount = CandidateApplication::where('stage', 'interview')->count();
        $offeringCount = CandidateApplication::where('stage', 'offering')->count();
        $onboardingCount = CandidateApplication::where('stage', 'onboarding')->count();
        $hiredCount = CandidateApplication::where('stage', 'hired')->count();

        $activePositions = Position::withCount('applications')
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();

        $recentApplications = CandidateApplication::with(['candidate.profile', 'position'])
            ->latest()
            ->take(6)
            ->get();

        $upcomingInterviews = InterviewAssessment::with(['candidate', 'position', 'interviewer'])
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        $recentOfferings = Offering::with(['candidate', 'position'])
            ->latest()
            ->take(3)
            ->get();

        $recentOnboardings = Onboarding::with(['candidate', 'position'])
            ->latest()
            ->take(3)
            ->get();

        return view('hr.dashboard', [
            'totalApplications' => $totalApplications,
            'screeningCount' => $screeningCount,
            'assessmentCount' => $assessmentCount,
            'interviewCount' => $interviewCount,
            'offeringCount' => $offeringCount,
            'onboardingCount' => $onboardingCount,
            'hiredCount' => $hiredCount,
            'activePositions' => $activePositions,
            'recentApplications' => $recentApplications,
            'upcomingInterviews' => $upcomingInterviews,
            'recentOfferings' => $recentOfferings,
            'recentOnboardings' => $recentOnboardings,
        ]);
    }

    /**
     * Display the Interviewer / User dashboard.
     */
    public function interview(): View
    {
        $interviewer = User::whereHas('role', function ($query) {
            $query->where('name', 'interviewer');
        })->first();

        $upcomingInterviews = InterviewAssessment::with(['candidate.profile', 'candidate.skills', 'position'])
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $pendingEvaluations = InterviewAssessment::with(['candidate.profile', 'position'])
            ->where('status', 'scheduled')
            ->where('scheduled_at', '<=', Carbon::now()->addHours(24))
            ->get();

        $pastEvaluations = InterviewAssessment::with(['candidate.profile', 'position', 'interviewer'])
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        $totalAssigned = InterviewAssessment::count();
        $completedCount = InterviewAssessment::where('status', 'completed')->count();
        $avgScore = InterviewAssessment::where('status', 'completed')->avg('total_score') ?? 0;

        return view('interview.dashboard', [
            'interviewer' => $interviewer,
            'upcomingInterviews' => $upcomingInterviews,
            'pendingEvaluations' => $pendingEvaluations,
            'pastEvaluations' => $pastEvaluations,
            'totalAssigned' => $totalAssigned,
            'completedCount' => $completedCount,
            'avgScore' => round($avgScore, 1),
        ]);
    }

    /**
     * Display the Candidate / Pelamar dashboard.
     */
    public function kadidat(): View
    {
        $user = Auth::user();
        $candidate = null;

        if ($user) {
            $candidate = Candidate::with([
                'profile',
                'education',
                'experiences',
                'skills',
                'applications.position',
                'interviewAssessments.position',
                'offerings.position',
                'onboarding.position',
            ])->where('user_id', $user->id)->first();
        }

        if (! $candidate) {
            $candidate = Candidate::with([
                'profile',
                'education',
                'experiences',
                'skills',
                'applications.position',
                'interviewAssessments.position',
                'offerings.position',
                'onboarding.position',
            ])->first();
        }

        // Calculate profile completion percentage
        $completionPoints = 0;
        $maxPoints = 5;

        if ($candidate) {
            $completionPoints += 1; // Basic account info
            if ($candidate->profile && $candidate->profile->summary) {
                $completionPoints += 1;
            }
            if ($candidate->education->isNotEmpty()) {
                $completionPoints += 1;
            }
            if ($candidate->experiences->isNotEmpty()) {
                $completionPoints += 1;
            }
            if ($candidate->skills->isNotEmpty()) {
                $completionPoints += 1;
            }
        }

        $profileCompletion = $maxPoints > 0 ? (int) round(($completionPoints / $maxPoints) * 100) : 0;

        // Recommended active positions (up to 6)
        $appliedPositionIds = $candidate ? $candidate->applications->pluck('position_id')->toArray() : [];
        $recommendedPositions = Position::where('status', 'active')
            ->whereNotIn('id', $appliedPositionIds)
            ->latest()
            ->take(6)
            ->get();

        $activeVacanciesCount = Position::where('status', 'active')->count();

        $upcomingInterview = $candidate?->interviewAssessments
            ?->where('status', 'scheduled')
            ->sortBy('scheduled_at')
            ->first();

        return view('kadidat.dashboard', [
            'candidate' => $candidate,
            'profileCompletion' => $profileCompletion,
            'recommendedPositions' => $recommendedPositions,
            'activeVacanciesCount' => $activeVacanciesCount,
            'upcomingInterview' => $upcomingInterview,
            'appliedPositionIds' => $appliedPositionIds,
        ]);
    }
}
