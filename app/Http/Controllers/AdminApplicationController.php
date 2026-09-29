<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Candidate;
use App\Models\CandidateApplication;
use App\Models\Position;
use App\Services\SimpleXlsxService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminApplicationController extends Controller
{
    /**
     * Display a listing of all candidate applications with filters and pagination.
     */
    public function index(Request $request): View
    {
        $query = CandidateApplication::with(['candidate.profile', 'position'])->latest('applied_at');

        // Search by candidate name, email, or position title
        if ($request->filled('search')) {
            $searchTerm = trim($request->query('search'));
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('candidate', function ($candidateQuery) use ($searchTerm) {
                    $candidateQuery->where('first_name', 'like', "%{$searchTerm}%")
                        ->orWhere('last_name', 'like', "%{$searchTerm}%")
                        ->orWhere('email', 'like', "%{$searchTerm}%")
                        ->orWhere('phone_number', 'like', "%{$searchTerm}%");
                })->orWhereHas('position', function ($positionQuery) use ($searchTerm) {
                    $positionQuery->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('department', 'like', "%{$searchTerm}%");
                });
            });
        }

        // Filter by position
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->query('position_id'));
        }

        // Filter by stage
        if ($request->filled('stage') && in_array($request->query('stage'), ['screening', 'assessment', 'interview', 'offering', 'onboarding', 'hired', 'rejected'], true)) {
            $query->where('stage', $request->query('stage'));
        }

        // Filter by status
        if ($request->filled('status') && in_array($request->query('status'), ['pending', 'in_review', 'passed', 'rejected'], true)) {
            $query->where('status', $request->query('status'));
        }

        $applications = $query->paginate(10)->withQueryString();

        // Data for filters and badges
        $positions = Position::orderBy('title')->get();
        $totalCount = CandidateApplication::count();
        $screeningCount = CandidateApplication::where('stage', 'screening')->count();
        $interviewCount = CandidateApplication::where('stage', 'interview')->count();
        $offeringCount = CandidateApplication::where('stage', 'offering')->count();
        $hiredCount = CandidateApplication::where('stage', 'hired')->count();

        return view('admin.applications.index', [
            'applications' => $applications,
            'positions' => $positions,
            'totalCount' => $totalCount,
            'screeningCount' => $screeningCount,
            'interviewCount' => $interviewCount,
            'offeringCount' => $offeringCount,
            'hiredCount' => $hiredCount,
            'selectedPosition' => $request->query('position_id'),
            'selectedStage' => $request->query('stage'),
            'selectedStatus' => $request->query('status'),
            'searchTerm' => $request->query('search'),
        ]);
    }

    /**
     * Display the specified candidate application details.
     */
    public function show(CandidateApplication $application): View
    {
        $application->load([
            'candidate.profile',
            'candidate.education',
            'candidate.experiences',
            'candidate.skills',
            'candidate.user',
            'candidate.interviewAssessments.interviewer',
            'position',
            'testResults.assessment',
            'candidate.testResults.assessment',
            'offerings',
            'onboarding',
        ]);

        return view('admin.applications.show', [
            'application' => $application,
            'candidate' => $application->candidate,
        ]);
    }

    /**
     * Advance or change the candidate application stage and status (HR / Admin).
     */
    public function updateStage(Request $request, CandidateApplication $application): RedirectResponse
    {
        $validated = $request->validate([
            'stage' => ['required', 'in:screening,assessment,interview,offering,onboarding,hired,rejected'],
            'status' => ['required', 'in:pending,in_review,passed,rejected'],
            'hr_notes' => ['nullable', 'string', 'max:1000'],
            'schedule_interview' => ['nullable', 'boolean'],
            'create_offering' => ['nullable', 'boolean'],
        ]);

        $previousStage = $application->stage;
        $newStage = $validated['stage'];

        $application->update([
            'stage' => $newStage,
            'status' => $validated['status'],
            'hr_notes' => $validated['hr_notes'] ?? $application->hr_notes,
        ]);

        $stageNameLabels = [
            'screening' => 'Screening CV',
            'assessment' => 'Psikotes / Assessment Online',
            'interview' => 'Wawancara (Interview)',
            'offering' => 'Penawaran Kerja (Offering)',
            'onboarding' => 'Onboarding Karyawan',
            'hired' => 'Lulus & Diterima Bekerja',
            'rejected' => 'Tidak Lolos Seleksi',
        ];

        $candidateName = $application->candidate?->full_name ?? 'Kandidat';
        $targetLabel = $stageNameLabels[$newStage] ?? $newStage;

        ActivityLog::record(
            'UPDATE_APPLICATION_STAGE',
            'Recruitment Pipeline',
            "Memperbarui tahap lamaran {$candidateName} dari '{$previousStage}' ke '{$targetLabel}'.",
            Auth::user()
        );

        // If moving to interview, redirect directly to interview scheduling form
        if ($newStage === 'interview' && $request->boolean('schedule_interview')) {
            $userRole = Auth::user()?->role?->name;
            $prefix = in_array($userRole, ['admin', 'hr'], true) ? $userRole : 'admin';

            return redirect()->route($prefix.'.interviews.create', [
                'candidate_id' => $application->candidate_id,
                'position_id' => $application->position_id,
            ])->with('success', "Tahap lamaran {$candidateName} berhasil diloloskan ke Wawancara! Silakan atur jadwal wawancara.");
        }

        // If moving to offering, redirect directly to offering creation form
        if ($newStage === 'offering' && $request->boolean('create_offering')) {
            $userRole = Auth::user()?->role?->name;
            $prefix = in_array($userRole, ['admin', 'hr'], true) ? $userRole : 'admin';

            return redirect()->route($prefix.'.offerings.create', [
                'candidate_id' => $application->candidate_id,
                'position_id' => $application->position_id,
                'application_id' => $application->id,
            ])->with('success', "Tahap lamaran {$candidateName} berhasil diloloskan ke Offering! Silakan terbitkan surat penawaran.");
        }

        return redirect()->back()
            ->with('success', "Tahap seleksi untuk {$candidateName} berhasil diperbarui menjadi {$targetLabel}!");
    }

    /**
     * Export all candidate applications data to Microsoft Excel (.xlsx) file.
     */
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $query = CandidateApplication::with(['candidate.profile', 'position'])->latest('applied_at');

        if ($request->filled('search')) {
            $searchTerm = trim($request->query('search'));
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('candidate', function ($candidateQuery) use ($searchTerm) {
                    $candidateQuery->where('first_name', 'like', "%{$searchTerm}%")
                        ->orWhere('last_name', 'like', "%{$searchTerm}%")
                        ->orWhere('email', 'like', "%{$searchTerm}%")
                        ->orWhere('phone_number', 'like', "%{$searchTerm}%");
                })->orWhereHas('position', function ($positionQuery) use ($searchTerm) {
                    $positionQuery->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('department', 'like', "%{$searchTerm}%");
                });
            });
        }

        if ($request->filled('position_id')) {
            $query->where('position_id', $request->query('position_id'));
        }

        if ($request->filled('stage') && in_array($request->query('stage'), ['screening', 'assessment', 'interview', 'offering', 'onboarding', 'hired', 'rejected'], true)) {
            $query->where('stage', $request->query('stage'));
        }

        if ($request->filled('status') && in_array($request->query('status'), ['pending', 'in_review', 'passed', 'rejected'], true)) {
            $query->where('status', $request->query('status'));
        }

        $applications = $query->get();

        $headers = [
            'No',
            'Nama Lengkap Pelamar',
            'Email',
            'Nomor Telepon / WhatsApp',
            'Posisi Lowongan',
            'Departemen',
            'Skor Kecocokan (%)',
            'Tahapan Seleksi',
            'Status Hasil Seleksi',
            'Tanggal Melamar',
            'Catatan Evaluasi HR',
        ];

        $stageLabels = [
            'screening' => '1. Screening CV',
            'assessment' => '2. Psikotes / Tes Online',
            'interview' => '3. Wawancara (Interview)',
            'offering' => '4. Penawaran Kerja (Offering)',
            'onboarding' => '5. Onboarding Karyawan',
            'hired' => '6. Lulus & Diterima Kerja',
            'rejected' => '7. Tidak Lolos Seleksi',
        ];

        $statusLabels = [
            'pending' => 'Pending / Menunggu',
            'in_review' => 'Sedang Ditinjau',
            'passed' => 'Lolos Seleksi',
            'rejected' => 'Tidak Lolos',
        ];

        $rows = [];
        $no = 1;
        foreach ($applications as $app) {
            $candidate = $app->candidate;
            $position = $app->position;

            $rows[] = [
                $no++,
                $candidate?->full_name ?? '-',
                $candidate?->email ?? '-',
                $candidate?->phone_number ?? $candidate?->profile?->phone_number ?? '-',
                $position?->title ?? '-',
                $position?->department ?? '-',
                $app->match_score ? $app->match_score.'%' : '-',
                $stageLabels[$app->stage] ?? ucfirst($app->stage),
                $statusLabels[$app->status] ?? ucfirst($app->status),
                $app->applied_at ? $app->applied_at->format('d/m/Y H:i') : '-',
                $app->hr_notes ?? '-',
            ];
        }

        $filename = 'Data_Pelamar_Rekrutmen_'.date('Ymd_His').'.xlsx';

        return SimpleXlsxService::download($filename, $headers, $rows, 'Data Pelamar');
    }

    /**
     * Download the standard Excel (.xlsx) template for mass updating candidate stages.
     */
    public function downloadMassTemplate(): BinaryFileResponse
    {
        $templatePath = public_path('upload_mass_status.xlsx');
        if (! file_exists($templatePath)) {
            $headers = ['no', 'email', 'tahapan'];
            $sampleRows = [
                [1, 'kandidat1@gmail.com', '2'],
                [2, 'kandidat2@gmail.com', '3'],
            ];
            SimpleXlsxService::create($templatePath, $headers, $sampleRows, 'Template');
        }

        return response()->download($templatePath, 'Template_Upload_Massal_Status_Pelamar.xlsx');
    }

    /**
     * Mass update candidate application stages and statuses using uploaded Excel (.xlsx) file.
     */
    public function uploadMassStatus(Request $request): RedirectResponse
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'max:10240'],
        ], [
            'file_excel.required' => 'Silakan pilih berkas Excel (.xlsx) yang akan diunggah.',
            'file_excel.max' => 'Ukuran berkas maksimal 10MB.',
        ]);

        $uploadedFile = $request->file('file_excel');
        $extension = strtolower($uploadedFile->getClientOriginalExtension());

        if ($extension !== 'xlsx') {
            return redirect()->back()->with('error', 'Format berkas tidak valid. Harap mengunggah berkas Excel dengan format .xlsx (sesuai template).');
        }

        $parsedRows = SimpleXlsxService::parse($uploadedFile->getRealPath());

        if (empty($parsedRows)) {
            return redirect()->back()->with('error', 'Berkas Excel kosong atau tidak dapat dibaca.');
        }

        // 1. Identify header row (row 1)
        $headerRow = array_shift($parsedRows);
        $emailCol = null;
        $tahapanCol = null;

        foreach ($headerRow as $colLetter => $headerText) {
            $norm = strtolower(trim($headerText));
            if ($norm === 'email' || str_contains($norm, 'surel') || str_contains($norm, 'mail')) {
                $emailCol = $colLetter;
            } elseif ($norm === 'tahapan' || $norm === 'tahap' || $norm === 'stage' || $norm === 'status') {
                $tahapanCol = $colLetter;
            }
        }

        // Fallback default column letters matching public/upload_mass_status.xlsx (B = email, C = tahapan)
        $emailCol = $emailCol ?? 'B';
        $tahapanCol = $tahapanCol ?? 'C';

        $stageMapping = [
            '1' => ['stage' => 'screening', 'status' => 'in_review', 'label' => 'Screening CV'],
            'screening' => ['stage' => 'screening', 'status' => 'in_review', 'label' => 'Screening CV'],
            'screening cv' => ['stage' => 'screening', 'status' => 'in_review', 'label' => 'Screening CV'],
            'review' => ['stage' => 'screening', 'status' => 'in_review', 'label' => 'Screening CV'],
            'berkas' => ['stage' => 'screening', 'status' => 'in_review', 'label' => 'Screening CV'],

            '2' => ['stage' => 'assessment', 'status' => 'in_review', 'label' => 'Psikotes / Tes Online'],
            'assessment' => ['stage' => 'assessment', 'status' => 'in_review', 'label' => 'Psikotes / Tes Online'],
            'psikotes' => ['stage' => 'assessment', 'status' => 'in_review', 'label' => 'Psikotes / Tes Online'],
            'tes' => ['stage' => 'assessment', 'status' => 'in_review', 'label' => 'Psikotes / Tes Online'],
            'tes online' => ['stage' => 'assessment', 'status' => 'in_review', 'label' => 'Psikotes / Tes Online'],

            '3' => ['stage' => 'interview', 'status' => 'in_review', 'label' => 'Wawancara (Interview)'],
            'interview' => ['stage' => 'interview', 'status' => 'in_review', 'label' => 'Wawancara (Interview)'],
            'wawancara' => ['stage' => 'interview', 'status' => 'in_review', 'label' => 'Wawancara (Interview)'],

            '4' => ['stage' => 'offering', 'status' => 'in_review', 'label' => 'Penawaran Kerja (Offering)'],
            'offering' => ['stage' => 'offering', 'status' => 'in_review', 'label' => 'Penawaran Kerja (Offering)'],
            'penawaran' => ['stage' => 'offering', 'status' => 'in_review', 'label' => 'Penawaran Kerja (Offering)'],
            'offering letter' => ['stage' => 'offering', 'status' => 'in_review', 'label' => 'Penawaran Kerja (Offering)'],

            '5' => ['stage' => 'onboarding', 'status' => 'in_review', 'label' => 'Onboarding Karyawan'],
            'onboarding' => ['stage' => 'onboarding', 'status' => 'in_review', 'label' => 'Onboarding Karyawan'],

            '6' => ['stage' => 'hired', 'status' => 'passed', 'label' => 'Lulus & Diterima Kerja'],
            'hired' => ['stage' => 'hired', 'status' => 'passed', 'label' => 'Lulus & Diterima Kerja'],
            'lulus' => ['stage' => 'hired', 'status' => 'passed', 'label' => 'Lulus & Diterima Kerja'],
            'diterima' => ['stage' => 'hired', 'status' => 'passed', 'label' => 'Lulus & Diterima Kerja'],

            '7' => ['stage' => 'rejected', 'status' => 'rejected', 'label' => 'Tidak Lolos Seleksi'],
            'rejected' => ['stage' => 'rejected', 'status' => 'rejected', 'label' => 'Tidak Lolos Seleksi'],
            'ditolak' => ['stage' => 'rejected', 'status' => 'rejected', 'label' => 'Tidak Lolos Seleksi'],
            'gagal' => ['stage' => 'rejected', 'status' => 'rejected', 'label' => 'Tidak Lolos Seleksi'],
            'tidak lolos' => ['stage' => 'rejected', 'status' => 'rejected', 'label' => 'Tidak Lolos Seleksi'],
        ];

        $updatedCount = 0;
        $skippedCount = 0;
        $unrecognizedStageCount = 0;

        foreach ($parsedRows as $row) {
            $email = trim($row[$emailCol] ?? '');
            $rawStage = trim($row[$tahapanCol] ?? '');

            if (empty($email) || empty($rawStage)) {
                continue;
            }

            $normStage = strtolower($rawStage);
            if (! isset($stageMapping[$normStage])) {
                $unrecognizedStageCount++;

                continue;
            }

            $target = $stageMapping[$normStage];

            // Locate candidate
            $candidate = Candidate::where('email', $email)
                ->orWhereHas('user', fn ($q) => $q->where('email', $email))
                ->first();

            if (! $candidate) {
                $skippedCount++;

                continue;
            }

            /** @var CandidateApplication|null $application */
            $application = $candidate->applications()->latest('applied_at')->first();
            if (! $application) {
                $skippedCount++;

                continue;
            }

            $application->update([
                'stage' => $target['stage'],
                'status' => $target['status'],
                'hr_notes' => trim(($application->hr_notes ? $application->hr_notes."\n" : '').'[Mass Update '.now()->format('d/m/Y H:i').'] Status tahapan diperbarui menjadi '.$target['label'].' melalui Berkas Excel Massal.'),
            ]);

            $updatedCount++;
        }

        ActivityLog::record(
            'MASS_UPDATE_APPLICATION_STAGE',
            'Recruitment Pipeline',
            "Memperbarui tahapan status pelamar secara massal dari Excel: {$updatedCount} berhasil, {$skippedCount} email tidak ditemukan, {$unrecognizedStageCount} format tahapan tidak valid.",
            Auth::user()
        );

        $msg = "Upload massal selesai! Sebanyak {$updatedCount} status lamaran kandidat berhasil diperbarui.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} baris dilewati karena email kandidat tidak terdaftar).";
        }
        if ($unrecognizedStageCount > 0) {
            $msg .= " ({$unrecognizedStageCount} baris memiliki nilai tahapan tidak dikenali).";
        }

        return redirect()->back()->with('success', $msg);
    }
}
