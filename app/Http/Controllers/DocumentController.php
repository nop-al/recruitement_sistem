<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Candidate;
use App\Models\InterviewAssessment;
use App\Models\Onboarding;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DocumentController extends Controller
{
    /**
     * Download or view candidate CV / Resume with strict permission check.
     */
    public function downloadResume(Request $request, Candidate $candidate): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Silakan login terlebih dahulu untuk mengakses dokumen ini.');
        }

        // 1. Authorization check
        $isAuthorized = false;

        if ($user->hasRole('admin') || $user->can('documents.download_any')) {
            $isAuthorized = true;
        } elseif ($user->hasRole('interviewer') || $user->can('documents.download_assigned')) {
            // Check if interviewer is scheduled to interview this candidate
            $hasInterview = InterviewAssessment::where('interviewer_id', $user->id)
                ->where('candidate_id', $candidate->id)
                ->exists();
            if ($hasInterview) {
                $isAuthorized = true;
            }
        } elseif ($user->hasRole('kandidat') || $user->can('documents.download_own')) {
            // Candidate can only access their own resume
            if ((int) $candidate->user_id === (int) $user->id) {
                $isAuthorized = true;
            }
        }

        if (! $isAuthorized) {
            ActivityLog::record(
                'UNAUTHORIZED_DOCUMENT_ACCESS',
                'Security',
                "Upaya akses ilegal ke resume kandidat ID #{$candidate->id} ({$candidate->full_name}) oleh pengguna ID #{$user->id} ({$user->name}).",
                $user
            );

            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengunduh dokumen resume ini.');
        }

        // 2. Locate resume file path
        $candidate->load('profile');
        $resumeRelativePath = $candidate->profile?->resume_path
            ?? $candidate->applications()->whereNotNull('resume_url')->latest()->value('resume_url');

        if (! $resumeRelativePath) {
            abort(404, 'Dokumen CV / Resume tidak ditemukan.');
        }

        // Check if file is stored in private (local) or legacy public disk
        $disk = null;
        if (Storage::disk('local')->exists($resumeRelativePath)) {
            $disk = 'local';
        } elseif (Storage::disk('public')->exists($resumeRelativePath)) {
            $disk = 'public';
        }

        if (! $disk) {
            abort(404, 'Berkas fisik CV tidak ditemukan pada media penyimpanan.');
        }

        // 3. Record Audit Log
        ActivityLog::record(
            'DOWNLOAD_DOCUMENT',
            'Document Security',
            "Mengakses dokumen CV/Resume milik kandidat {$candidate->full_name}.",
            $user
        );

        // 4. Return file (inline view or download)
        $fileName = 'CV_'.str_replace(' ', '_', $candidate->full_name).'.'.pathinfo($resumeRelativePath, PATHINFO_EXTENSION);
        $fullPath = Storage::disk($disk)->path($resumeRelativePath);

        if ($request->boolean('view')) {
            return response()->file($fullPath, [
                'Content-Disposition' => 'inline; filename="'.$fileName.'"',
            ]);
        }

        return response()->download($fullPath, $fileName);
    }

    /**
     * Download or view candidate onboarding checklist document with strict permission check.
     */
    public function downloadOnboarding(Request $request, Onboarding $onboarding, string $type): Response
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            abort(401, 'Silakan login terlebih dahulu untuk mengakses dokumen ini.');
        }

        $fieldMap = [
            'ktp' => ['field' => 'ktp_file', 'label' => 'KTP'],
            'npwp' => ['field' => 'npwp_file', 'label' => 'NPWP'],
            'bpjs' => ['field' => 'bpjs_file', 'label' => 'Kartu BPJS'],
            'bank' => ['field' => 'bank_book_file', 'label' => 'Buku Tabungan / Rekening'],
            'ijazah' => ['field' => 'ijazah_file', 'label' => 'Ijazah'],
            'pas_foto' => ['field' => 'pas_foto_file', 'label' => 'Pas Foto Formal'],
            'contract' => ['field' => 'contract_file', 'label' => 'Kontrak Kerja'],
        ];

        if (! array_key_exists($type, $fieldMap)) {
            abort(400, 'Tipe dokumen onboarding tidak valid.');
        }

        $onboarding->load('candidate');
        $candidate = $onboarding->candidate;

        // 1. Authorization check
        $isAuthorized = false;

        if ($user->hasRole('admin') || $user->can('documents.download_any') || $user->can('onboarding.manage')) {
            $isAuthorized = true;
        } elseif ($candidate && (int) $candidate->user_id === (int) $user->id) {
            $isAuthorized = true;
        }

        if (! $isAuthorized) {
            ActivityLog::record(
                'UNAUTHORIZED_ONBOARDING_DOC_ACCESS',
                'Security',
                "Upaya akses ilegal ke berkas onboarding ({$type}) milik kandidat ID #{$onboarding->candidate_id} oleh pengguna ID #{$user->id} ({$user->name}).",
                $user
            );

            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengunduh dokumen onboarding ini.');
        }

        // 2. Locate document file path
        $targetField = $fieldMap[$type]['field'];
        $docRelativePath = $onboarding->{$targetField};

        if (! $docRelativePath) {
            abort(404, "Berkas {$fieldMap[$type]['label']} belum diunggah oleh kandidat.");
        }

        // Check private disk first, then legacy public disk
        $disk = null;
        if (Storage::disk('local')->exists($docRelativePath)) {
            $disk = 'local';
        } elseif (Storage::disk('public')->exists($docRelativePath)) {
            $disk = 'public';
        }

        if (! $disk) {
            abort(404, "Berkas fisik {$fieldMap[$type]['label']} tidak ditemukan pada server.");
        }

        // 3. Record Audit Log
        ActivityLog::record(
            'DOWNLOAD_DOCUMENT',
            'Onboarding Document Security',
            "Mengakses berkas {$fieldMap[$type]['label']} onboarding milik kandidat ".($candidate?->full_name ?? 'Kandidat').'.',
            $user
        );

        // 4. Return file (inline view or download)
        $ext = pathinfo($docRelativePath, PATHINFO_EXTENSION);
        $candidateName = $candidate ? str_replace(' ', '_', $candidate->full_name) : 'Kandidat';
        $downloadName = strtoupper($type).'_'.$candidateName.'.'.$ext;
        $fullPath = Storage::disk($disk)->path($docRelativePath);

        if ($request->boolean('view') || in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp', 'pdf'], true)) {
            return response()->file($fullPath, [
                'Content-Disposition' => 'inline; filename="'.$downloadName.'"',
            ]);
        }

        return response()->download($fullPath, $downloadName);
    }
}
