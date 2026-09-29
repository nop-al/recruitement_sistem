@extends('layouts.dashboard')

@section('title', 'Detail Lamaran: ' . ($candidate?->full_name ?? 'Pelamar'))
@section('role-label', 'Manajemen Pelamar')
@section('page-title', 'Detail Pelamar & Dokumen CV')

@php
    $currentRole = auth()->user()?->role?->name;
    $prefix = match($currentRole) {
        'admin' => 'admin',
        'hr' => 'hr',
        default => 'interview',
    };
    $indexRoute = $prefix . '.applications.index';
    $interviewCreateRoute = $prefix . '.interviews.create';
    $updateStageUrl = route($prefix . '.applications.update-stage', $application->id);
    $resumeFile = $application->resume_url ?: $candidate?->profile?->resume_path;

    $appTests = $application->testResults->isNotEmpty() ? $application->testResults : ($candidate?->testResults ?? collect());
    $appInterviews = $candidate?->interviewAssessments ? $candidate->interviewAssessments->where('position_id', $application->position_id) : collect();
    if ($appInterviews->isEmpty() && $candidate?->interviewAssessments) {
        $appInterviews = $candidate->interviewAssessments;
    }
    $latestOffering = $application->offering ?? $application->offerings?->first();
    $onboarding = $application->onboarding ?? $candidate?->onboarding;

    $isHrOrAdmin = in_array($currentRole, ['admin', 'hr'], true);
    $adminHrPrefix = $currentRole === 'hr' ? 'hr' : 'admin';

    $currentStage = $application->stage ?? 'screening';
    $stageOrder = ['screening' => 1, 'assessment' => 2, 'interview' => 3, 'offering' => 4, 'onboarding' => 5, 'hired' => 6, 'rejected' => 0];
    $currentStageNum = $stageOrder[$currentStage] ?? 1;
@endphp

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Top Action & Navigation Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route($indexRoute) }}"
               class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition shadow-sm"
               title="Kembali ke Daftar Pelamar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-black text-slate-900 tracking-tight">Detail Lamaran & Riwayat Kandidat</h2>
                <p class="text-xs text-slate-500">ID Lamaran: #APP-{{ str_pad($application->id, 5, '0', STR_PAD_LEFT) }} &bull; Dilamar {{ $application->applied_at ? \Carbon\Carbon::parse($application->applied_at)->format('d M Y, H:i') : '-' }} WIB</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($currentStage === 'screening')
                <a href="#action-panel"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Review &amp; Approve CV</span>
                </a>
            @elseif($currentStage === 'assessment')
                <a href="#action-panel"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <span>Cek Nilai Ujian</span>
                </a>
            @elseif($currentStage === 'interview')
                <a href="{{ route($interviewCreateRoute, ['candidate_id' => $candidate->id, 'position_id' => $application->position_id]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-md shadow-amber-500/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span>Jadwalkan Wawancara</span>
                </a>
            @elseif($currentStage === 'offering' && $isHrOrAdmin)
                <a href="{{ route($adminHrPrefix . '.offerings.create', ['candidate_id' => $candidate->id, 'position_id' => $application->position_id, 'application_id' => $application->id]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md shadow-purple-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>+ Buat Offering</span>
                </a>
            @elseif($currentStage === 'onboarding' && $isHrOrAdmin)
                <a href="{{ route($adminHrPrefix . '.onboardings.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/30 transition">
                    <span>Berkas Onboarding</span>
                </a>
            @endif

            @if($resumeFile)
                @php
                    $isUrl = str_starts_with($resumeFile, 'http');
                    $fileLink = $isUrl ? $resumeFile : ($candidate ? route('documents.resume', ['candidate' => $candidate->id, 'view' => 1]) : '#');
                @endphp
                <a href="{{ $fileLink }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Unduh Dokumen CV</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Candidate Header Card -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                @if($candidate?->profile?->photo)
                    <img src="{{ asset('storage/' . $candidate->profile->photo) }}" alt="{{ $candidate->full_name }}" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-indigo-500/20 shadow-md">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 via-purple-600 to-pink-500 flex items-center justify-center text-white text-xl font-black shadow-md shadow-indigo-500/20">
                        {{ strtoupper(substr($candidate?->first_name ?? 'K', 0, 1) . substr($candidate?->last_name ?? 'P', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-xl font-bold text-slate-900">{{ $candidate?->full_name }}</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">
                            Kandidat Terdaftar
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                            No: 2026-{{ str_pad((string)$candidate?->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            {{ $candidate?->email }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            {{ $candidate?->phone_number ?? '-' }}
                        </span>
                        @if($candidate?->profile?->city)
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $candidate->profile->city }}, {{ $candidate->profile->province }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Match Score Box -->
            <div class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <div class="text-right">
                    <span class="text-[11px] text-slate-400 font-semibold block">Skor Kecocokan</span>
                    <span class="text-xl font-black {{ $application->match_score >= 90 ? 'text-emerald-600' : 'text-indigo-600' }}">
                        {{ $application->match_score ?? 85 }}%
                    </span>
                </div>
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs {{ $application->match_score >= 90 ? 'bg-emerald-100 text-emerald-800' : 'bg-indigo-100 text-indigo-800' }}">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- PIPELINE STAGE STEPPER (TAHAPAN REKRUTMEN)                                -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200">
                    Alur Seleksi Lengkap
                </span>
                <h3 class="text-base font-bold text-slate-900 mt-1">Tahapan Rekrutmen &amp; Seleksi Pelamar</h3>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400 font-semibold block">Tahap Saat Ini</span>
                <span class="text-xs font-black uppercase px-3 py-1 rounded-full
                    {{ $currentStage === 'hired' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($currentStage === 'rejected' ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-indigo-100 text-indigo-800 border border-indigo-200') }}">
                    {{ str_replace('_', ' ', $currentStage) }} ({{ $application->status }})
                </span>
            </div>
        </div>

        <!-- Visual Stepper Progress -->
        <div class="overflow-x-auto py-2">
            <div class="min-w-[700px] relative">
                <!-- Line -->
                <div class="absolute top-4 left-8 right-8 h-1 bg-slate-200 -z-0"></div>
                @php
                    $stepperProgress = match($currentStage) {
                        'screening' => 10,
                        'assessment' => 32,
                        'interview' => 55,
                        'offering' => 78,
                        'onboarding', 'hired' => 100,
                        default => 10,
                    };
                @endphp
                <div class="absolute top-4 left-8 h-1 bg-emerald-500 -z-0 transition-all duration-500" style="width: {{ $stepperProgress }}%"></div>

                <div class="grid grid-cols-5 gap-2 relative z-10">
                    <!-- Step 1: Screening CV -->
                    <div class="flex flex-col items-center text-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow ring-4 ring-white
                            {{ $currentStageNum > 1 ? 'bg-emerald-600 text-white' : ($currentStageNum === 1 ? 'bg-blue-600 text-white ring-blue-100' : 'bg-slate-200 text-slate-500') }}">
                            @if($currentStageNum > 1)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                1
                            @endif
                        </div>
                        <span class="text-xs font-bold text-slate-900 mt-2">1. Screening CV</span>
                        <span class="text-[10px] {{ $currentStageNum > 1 ? 'text-emerald-600 font-bold' : ($currentStageNum === 1 ? 'text-blue-600 font-bold' : 'text-slate-400') }}">
                            {{ $currentStageNum > 1 ? 'Lolos Review' : ($currentStageNum === 1 ? 'Sedang Ditinjau' : 'Antrean') }}
                        </span>
                    </div>

                    <!-- Step 2: Psikotes / Online Test -->
                    <div class="flex flex-col items-center text-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow ring-4 ring-white
                            {{ $currentStageNum > 2 ? 'bg-emerald-600 text-white' : ($currentStageNum === 2 ? 'bg-indigo-600 text-white ring-indigo-100 animate-pulse' : 'bg-slate-200 text-slate-500') }}">
                            @if($currentStageNum > 2)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                2
                            @endif
                        </div>
                        <span class="text-xs font-bold text-slate-900 mt-2">2. Psikotes Online</span>
                        <span class="text-[10px] {{ $currentStageNum > 2 ? 'text-emerald-600 font-bold' : ($currentStageNum === 2 ? 'text-indigo-600 font-bold' : 'text-slate-400') }}">
                            {{ $currentStageNum > 2 ? 'Nilai Lolos' : ($currentStageNum === 2 ? 'Tahap Ujian' : 'Menunggu') }}
                        </span>
                    </div>

                    <!-- Step 3: Wawancara / Interview -->
                    <div class="flex flex-col items-center text-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow ring-4 ring-white
                            {{ $currentStageNum > 3 ? 'bg-emerald-600 text-white' : ($currentStageNum === 3 ? 'bg-amber-500 text-slate-950 ring-amber-100 animate-pulse' : 'bg-slate-200 text-slate-500') }}">
                            @if($currentStageNum > 3)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                3
                            @endif
                        </div>
                        <span class="text-xs font-bold text-slate-900 mt-2">3. Wawancara</span>
                        <span class="text-[10px] {{ $currentStageNum > 3 ? 'text-emerald-600 font-bold' : ($currentStageNum === 3 ? 'text-amber-700 font-bold' : 'text-slate-400') }}">
                            {{ $currentStageNum > 3 ? 'Lolos Interview' : ($currentStageNum === 3 ? 'Sesi Tanya Jawab' : 'Menunggu') }}
                        </span>
                    </div>

                    <!-- Step 4: Offering Letter -->
                    <div class="flex flex-col items-center text-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow ring-4 ring-white
                            {{ $currentStageNum > 4 ? 'bg-emerald-600 text-white' : ($currentStageNum === 4 ? 'bg-purple-600 text-white ring-purple-100 animate-pulse' : 'bg-slate-200 text-slate-500') }}">
                            @if($currentStageNum > 4)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                4
                            @endif
                        </div>
                        <span class="text-xs font-bold text-slate-900 mt-2">4. Offering Letter</span>
                        <span class="text-[10px] {{ $currentStageNum > 4 ? 'text-emerald-600 font-bold' : ($currentStageNum === 4 ? 'text-purple-600 font-bold' : 'text-slate-400') }}">
                            {{ $currentStageNum > 4 ? 'Disetujui' : ($currentStageNum === 4 ? 'Penawaran Gaji' : 'Menunggu') }}
                        </span>
                    </div>

                    <!-- Step 5: Onboarding & Hired -->
                    <div class="flex flex-col items-center text-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow ring-4 ring-white
                            {{ $currentStage === 'hired' ? 'bg-emerald-600 text-white' : ($currentStage === 'onboarding' ? 'bg-teal-600 text-white ring-teal-100 animate-pulse' : 'bg-slate-200 text-slate-500') }}">
                            @if($currentStage === 'hired')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                5
                            @endif
                        </div>
                        <span class="text-xs font-bold text-slate-900 mt-2">5. Onboarding &amp; Hired</span>
                        <span class="text-[10px] {{ $currentStage === 'hired' ? 'text-emerald-600 font-bold' : ($currentStage === 'onboarding' ? 'text-teal-600 font-bold' : 'text-slate-400') }}">
                            {{ $currentStage === 'hired' ? 'Resmi Bergabung' : ($currentStage === 'onboarding' ? 'Pemberkasan' : 'Tahap Akhir') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- INTERACTIVE HR STAGE ACTION CONTROL PANEL                                  -->
    <!-- ========================================================================= -->
    <div id="action-panel">

        @if($currentStage === 'screening')
            <!-- STAGE 1: SCREENING CV PANEL -->
            <div class="p-6 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50/50 border-2 border-blue-200 shadow-sm space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-blue-200/80">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-ping"></span>
                            <span class="text-xs font-black uppercase tracking-wider text-blue-800">Tahap 1: Screening Dokumen &amp; Berkas CV</span>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mt-1">Evaluasi Kualifikasi &amp; Review Dokumen CV Pelamar</h4>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Pelamar baru saja mengirimkan lamaran. Silakan teliti berkas CV, riwayat kerja, dan pendidikan. Jika disetujui (Approve), kandidat akan mendapatkan hak akses untuk mengikuti <strong>Psikotes &amp; Online Assessment</strong>.
                        </p>
                    </div>
                    <div class="shrink-0">
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                            Menunggu Review HRD
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ $updateStageUrl }}" class="space-y-4 pt-2">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Evaluasi Screening CV (Opsional):</label>
                        <input type="text" name="hr_notes" placeholder="Contoh: Kualifikasi sesuai kriteria, pengalaman relevan, lolos screening dan lanjut ke Psikotes Online..."
                               value="{{ $application->hr_notes }}"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                        <div class="text-xs text-slate-500 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Setelah di-approve, status berlanjut ke Tahap 2 (Psikotes / Online Assessment).</span>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <!-- Reject Button triggers modal -->
                            <button type="button" onclick="openRejectModal('screening')"
                                    class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                                ✕ Tolak Berkas CV
                            </button>

                            <!-- Approve Button -->
                            <input type="hidden" name="stage" value="assessment">
                            <input type="hidden" name="status" value="in_review">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Approve CV &amp; Loloskan ke Psikotes &rarr;</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        @elseif($currentStage === 'assessment')
            <!-- STAGE 2: ASSESSMENT / PSIKOTES PANEL -->
            <div class="p-6 rounded-2xl bg-gradient-to-br from-indigo-50 to-purple-50/50 border-2 border-indigo-200 shadow-sm space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-indigo-200/80">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-ping"></span>
                            <span class="text-xs font-black uppercase tracking-wider text-indigo-800">Tahap 2: Psikotes / Assessment Online</span>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mt-1">Perolehan Nilai &amp; Hasil Evaluasi Asesmen Online</h4>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Kandidat telah lolos screening berkas dan berhak mengikuti tes online. Tinjau nilai hasil ujian di bawah ini sebelum meloloskan kandidat ke sesi wawancara.
                        </p>
                    </div>
                    <div class="shrink-0">
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                            Tahap Ujian Aktif
                        </span>
                    </div>
                </div>

                <!-- Test Results Display -->
                <div class="mt-4">
                    @if($appTests->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($appTests as $test)
                                <div class="p-4 rounded-xl bg-white border border-indigo-100 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-12 h-12 rounded-xl {{ $test->result === 'passed' ? 'bg-emerald-100 text-emerald-800' : 'bg-indigo-100 text-indigo-800' }} flex flex-col items-center justify-center font-black shadow-inner">
                                            <span class="text-xs text-slate-400 font-semibold leading-none">Skor</span>
                                            <span class="text-sm font-black">{{ round($test->score) }}</span>
                                        </div>
                                        <div>
                                            <h5 class="text-xs font-bold text-slate-900">{{ $test->assessment?->title ?? 'Online Assessment' }}</h5>
                                            <div class="text-[11px] text-slate-500 mt-0.5">
                                                Jenis: <span class="font-semibold text-indigo-700">{{ $test->assessment?->type ?? 'Technical & Logic Test' }}</span> &bull;
                                                Benar: <strong class="text-emerald-600">{{ $test->correct_answers }}</strong> / {{ $test->total_questions }} soal &bull;
                                                Durasi: {{ round($test->duration_spent / 60) }} menit
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $test->result === 'passed' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                            {{ $test->result === 'passed' ? 'Lolos Passing Grade' : 'Perlu Evaluasi' }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-mono">
                                            {{ $test->completed_at ? \Carbon\Carbon::parse($test->completed_at)->format('d M Y, H:i') : '-' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-white border border-dashed border-indigo-200 text-center">
                            <p class="text-xs font-bold text-slate-800">Kandidat belum menyelesaikan ujian online atau sedang mengerjakan.</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Kandidat dapat mengakses menu Psikotes &amp; Assessment di Portal Kandidat. Anda juga dapat meloloskan langsung jika penilaian dilakukan secara offline/observasi.</p>
                        </div>
                    @endif
                </div>

                <!-- Form to Advance to Interview -->
                <form method="POST" action="{{ $updateStageUrl }}" class="pt-4 border-t border-indigo-200/80 space-y-4">
                    @csrf
                    <input type="hidden" name="stage" value="interview">
                    <input type="hidden" name="status" value="in_review">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Evaluasi Hasil Ujian / Psikotes:</label>
                        <input type="text" name="hr_notes" placeholder="Contoh: Nilai asesmen memenuhi passing grade perusahaan (85/100). Direkomendasikan untuk wawancara teknis."
                               value="{{ $application->hr_notes }}"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" name="schedule_interview" value="1" checked class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500">
                            <span>Langsung buka formulir Penjadwalan Wawancara</span>
                        </label>

                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" onclick="openRejectModal('assessment')"
                                    class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                                ✕ Nilai Tidak Lolos
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Nilai Lolos &amp; Lanjut ke Wawancara &rarr;</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        @elseif($currentStage === 'interview')
            <!-- STAGE 3: INTERVIEW PANEL -->
            <div class="p-6 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50/50 border-2 border-amber-200 shadow-sm space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-amber-200/80">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                            <span class="text-xs font-black uppercase tracking-wider text-amber-800">Tahap 3: Wawancara (Interview)</span>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mt-1">Jadwal Sesi Tanya Jawab &amp; Penilaian Interview</h4>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Kandidat sedang atau telah menyelesaikan wawancara. Tinjau nilai skor teknis, komunikasi, serta rekomendasi pewawancara sebelum menerbitkan Surat Offering.
                        </p>
                    </div>
                    <div class="shrink-0 flex items-center gap-2">
                        <a href="{{ route($interviewCreateRoute, ['candidate_id' => $candidate->id, 'position_id' => $application->position_id]) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Atur Jadwal / Input Nilai</span>
                        </a>
                    </div>
                </div>

                <!-- Interview History / List -->
                <div class="mt-4">
                    @if($appInterviews->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($appInterviews as $iv)
                                <div class="p-4 rounded-xl bg-white border border-amber-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 uppercase">{{ $iv->status }}</span>
                                            <h5 class="text-xs font-bold text-slate-900">{{ $iv->interview_type ?? 'Wawancara Teknis' }}</h5>
                                            @if($iv->room_link)
                                                <a href="{{ $iv->room_link }}" target="_blank" class="text-[11px] text-blue-600 underline font-semibold flex items-center gap-1">
                                                    <span>Ruang Meeting</span> &rarr;
                                                </a>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-1">
                                            Pewawancara: <strong class="text-slate-800">{{ $iv->interviewer?->name ?? 'Tim Interview' }}</strong> &bull;
                                            Jadwal: <strong>{{ $iv->scheduled_at ? \Carbon\Carbon::parse($iv->scheduled_at)->format('d M Y, H:i') : '-' }} WIB</strong>
                                        </div>
                                        @if($iv->notes)
                                            <p class="text-xs text-slate-600 mt-2 bg-slate-50 p-2.5 rounded-lg border border-slate-100 leading-relaxed">{{ $iv->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3 shrink-0">
                                        @if($iv->total_score)
                                            <div class="text-right">
                                                <div class="text-[10px] text-slate-400">Total Skor</div>
                                                <span class="text-base font-black text-emerald-700">{{ $iv->total_score }}/100</span>
                                            </div>
                                        @endif
                                        @if($iv->recommendation)
                                            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                Rekomendasi: {{ ucfirst($iv->recommendation) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-white border border-dashed border-amber-200 text-center">
                            <p class="text-xs font-bold text-slate-800">Belum ada sesi wawancara yang dijadwalkan.</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Klik tombol "Atur Jadwal / Input Nilai" di atas untuk menetapkan pewawancara dan waktu wawancara.</p>
                        </div>
                    @endif
                </div>

                <!-- Form to Advance to Offering -->
                <form method="POST" action="{{ $updateStageUrl }}" class="pt-4 border-t border-amber-200/80 space-y-4">
                    @csrf
                    <input type="hidden" name="stage" value="offering">
                    <input type="hidden" name="status" value="in_review">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Kelulusan Wawancara:</label>
                        <input type="text" name="hr_notes" placeholder="Contoh: Kandidat sangat memuaskan pada sesi wawancara arsitektur dan cultural fit. Lanjut ke pembuatan Offering Letter."
                               value="{{ $application->hr_notes }}"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                        <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                            <input type="checkbox" name="create_offering" value="1" checked class="w-4 h-4 rounded text-purple-600 border-slate-300 focus:ring-purple-500">
                            <span>Langsung buka formulir Penerbitan Surat Offering (Penawaran Gaji)</span>
                        </label>

                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" onclick="openRejectModal('interview')"
                                    class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                                ✕ Wawancara Tidak Lolos
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md shadow-purple-600/30 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Wawancara Lolos &amp; Terbitkan Offering &rarr;</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        @elseif($currentStage === 'offering')
            <!-- STAGE 4: OFFERING PANEL -->
            <div class="p-6 rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50/50 border-2 border-purple-200 shadow-sm space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-purple-200/80">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-600 animate-ping"></span>
                            <span class="text-xs font-black uppercase tracking-wider text-purple-800">Tahap 4: Penawaran Kerja (Offering Letter)</span>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mt-1">Penerbitan &amp; Persetujuan Surat Penawaran</h4>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Surat offering mencakup nominal gaji, penempatan departemen, tunjangan, dan tanggal masuk kerja. Jika kandidat menyetujui, lanjut ke proses Onboarding Karyawan.
                        </p>
                    </div>
                    @if($isHrOrAdmin)
                    <div class="shrink-0 flex items-center gap-2">
                        <a href="{{ route($adminHrPrefix . '.offerings.create', ['candidate_id' => $candidate->id, 'position_id' => $application->position_id, 'application_id' => $application->id]) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span>Buat / Perbarui Offering</span>
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Offering Status Display -->
                <div class="mt-4">
                    @if($latestOffering)
                        <div class="p-4 rounded-xl bg-white border border-purple-100 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                        {{ $latestOffering->status === 'accepted' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($latestOffering->status === 'sent' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-slate-100 text-slate-800') }}">
                                        Status: {{ $latestOffering->status }}
                                    </span>
                                    <h5 class="text-xs font-bold text-slate-900">{{ $latestOffering->position?->title ?? $application->position?->title }}</h5>
                                </div>
                                <div class="text-xs text-slate-600 mt-1">
                                    Gaji Ditawarkan: <strong class="text-emerald-700">Rp {{ number_format($latestOffering->salary_offered, 0, ',', '.') }}</strong> &bull;
                                    Tipe: {{ $latestOffering->employment_type }} &bull;
                                    Mulai Kerja: <strong>{{ $latestOffering->join_date ? \Carbon\Carbon::parse($latestOffering->join_date)->format('d M Y') : '-' }}</strong>
                                </div>
                                @if($latestOffering->benefits)
                                    <div class="text-[11px] text-slate-500 mt-1">Tunjangan: {{ $latestOffering->benefits }}</div>
                                @endif
                            </div>
                            @if($isHrOrAdmin)
                            <div class="flex items-center gap-2 shrink-0">
                                <a href="{{ route($adminHrPrefix . '.offerings.show', $latestOffering->id) }}"
                                   class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-purple-100 text-purple-700 text-xs font-bold transition">
                                    Lihat Dokumen Offering &rarr;
                                </a>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-white border border-dashed border-purple-200 text-center">
                            <p class="text-xs font-bold text-slate-800">Belum ada surat offering resmi yang dibuat.</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Klik tombol "Buat / Perbarui Offering" di atas untuk mengisi detail gaji dan benefit penawaran kerja.</p>
                        </div>
                    @endif
                </div>

                <!-- Form to Advance to Onboarding -->
                <form method="POST" action="{{ $updateStageUrl }}" class="pt-4 border-t border-purple-200/80 space-y-4">
                    @csrf
                    <input type="hidden" name="stage" value="onboarding">
                    <input type="hidden" name="status" value="passed">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Persetujuan Offering:</label>
                        <input type="text" name="hr_notes" placeholder="Contoh: Kandidat telah menandatangani persetujuan penawaran kerja. Masuk ke tahap Onboarding Pemberkasan."
                               value="{{ $application->hr_notes }}"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                        <div class="text-xs text-slate-500">
                            Setelah disetujui, kandidat diwajibkan mengunggah KTP, NPWP, BPJS, Rekening Bank, dan Pas Foto.
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" onclick="openRejectModal('offering')"
                                    class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition">
                                ✕ Batalkan / Tolak
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-600/30 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Offering Disetujui &amp; Lanjut ke Onboarding &rarr;</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        @elseif($currentStage === 'onboarding')
            <!-- STAGE 5: ONBOARDING PANEL -->
            <div class="p-6 rounded-2xl bg-gradient-to-br from-teal-50 to-emerald-50/50 border-2 border-teal-200 shadow-sm space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-teal-200/80">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-teal-600 animate-ping"></span>
                            <span class="text-xs font-black uppercase tracking-wider text-teal-800">Tahap 5: Onboarding Karyawan</span>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mt-1">Verifikasi Dokumen Administrasi &amp; Kontrak Kerja</h4>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Kandidat mengunggah 8 berkas resmi (KTP, NPWP, BPJS, Rekening Bank, Ijazah, Pas Foto, Kontrak Kerja, Kontak Darurat). Verifikasi berkas untuk meresmikan status sebagai Karyawan Resmi (Hired).
                        </p>
                    </div>
                    @if($isHrOrAdmin)
                    <div class="shrink-0 flex items-center gap-2">
                        <a href="{{ route($adminHrPrefix . '.onboardings.index') }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow transition">
                            <span>Portal Onboarding HR &rarr;</span>
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Form to Finalize to Hired -->
                <form method="POST" action="{{ $updateStageUrl }}" class="space-y-4 pt-2">
                    @csrf
                    <input type="hidden" name="stage" value="hired">
                    <input type="hidden" name="status" value="passed">

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Verifikasi Onboarding:</label>
                        <input type="text" name="hr_notes" placeholder="Contoh: Seluruh dokumen identitas, nomor rekening, dan kontrak kerja telah diverifikasi sah. Selamat bergabung!"
                               value="{{ $application->hr_notes }}"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-300 bg-white focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500">
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                        <div class="text-xs text-slate-500">
                            Menetapkan status pelamar menjadi Karyawan Resmi (Hired) setelah verifikasi selesai.
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/30 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span>Selesaikan Onboarding &amp; Diterima Bekerja (Hired) 🎉</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        @elseif($currentStage === 'hired')
            <!-- STAGE: HIRED (SUCCESS) -->
            <div class="p-6 rounded-2xl bg-emerald-50 border-2 border-emerald-300 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black text-xl shadow-md shadow-emerald-600/30">
                        ✓
                    </div>
                    <div>
                        <h4 class="text-base font-extrabold text-emerald-950">Kandidat Telah Resmi Diterima Bekerja (Hired)!</h4>
                        <p class="text-xs text-emerald-800 mt-0.5">Kandidat telah berhasil menyelesaikan seluruh alur rekrutmen dari Screening CV, Tes Online, Wawancara, Offering, hingga Onboarding Karyawan.</p>
                        @if($application->hr_notes)
                            <p class="text-xs text-emerald-700 font-semibold mt-1">Catatan: {{ $application->hr_notes }}</p>
                        @endif
                    </div>
                </div>
                <span class="px-4 py-1.5 rounded-full text-xs font-black bg-emerald-600 text-white shadow shrink-0 text-center">
                    Karyawan Aktif
                </span>
            </div>

        @elseif($currentStage === 'rejected')
            <!-- STAGE: REJECTED -->
            <div class="p-6 rounded-2xl bg-rose-50 border-2 border-rose-300 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center font-black text-xl shadow-md shadow-rose-600/30 shrink-0">
                        ✕
                    </div>
                    <div>
                        <h4 class="text-base font-extrabold text-rose-950">Lamaran Tidak Lolos Seleksi (Rejected)</h4>
                        <p class="text-xs text-rose-800 mt-0.5">Catatan Evaluasi: {{ $application->hr_notes ?? 'Kualifikasi kandidat belum sesuai dengan kriteria yang dibutuhkan.' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ $updateStageUrl }}" class="shrink-0">
                    @csrf
                    <input type="hidden" name="stage" value="screening">
                    <input type="hidden" name="status" value="pending">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-white border border-rose-300 hover:bg-rose-100 text-rose-800 font-bold text-xs transition">
                        Buka Kembali ke Screening CV
                    </button>
                </form>
            </div>
        @endif

        <!-- Collapsible Manual Stage Override (For HR Flexibility) -->
        <div class="mt-3">
            <details class="group bg-white rounded-xl border border-slate-200 p-4 transition shadow-xs">
                <summary class="flex items-center justify-between cursor-pointer font-bold text-xs text-slate-600 hover:text-slate-900 select-none">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                        Opsi Lanjutan: Ubah Tahapan Seleksi Secara Manual
                    </span>
                    <span class="text-slate-400 group-open:rotate-180 transition-transform">&darr;</span>
                </summary>
                <form method="POST" action="{{ $updateStageUrl }}" class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Pilih Tahap</label>
                        <select name="stage" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white">
                            <option value="screening" {{ $application->stage === 'screening' ? 'selected' : '' }}>1. Screening CV</option>
                            <option value="assessment" {{ $application->stage === 'assessment' ? 'selected' : '' }}>2. Psikotes / Assessment</option>
                            <option value="interview" {{ $application->stage === 'interview' ? 'selected' : '' }}>3. Wawancara</option>
                            <option value="offering" {{ $application->stage === 'offering' ? 'selected' : '' }}>4. Penawaran (Offering)</option>
                            <option value="onboarding" {{ $application->stage === 'onboarding' ? 'selected' : '' }}>5. Onboarding</option>
                            <option value="hired" {{ $application->stage === 'hired' ? 'selected' : '' }}>6. Diterima (Hired)</option>
                            <option value="rejected" {{ $application->stage === 'rejected' ? 'selected' : '' }}>Tidak Lolos (Rejected)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold uppercase text-slate-500 mb-1">Pilih Status</label>
                        <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white">
                            <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_review" {{ $application->status === 'in_review' ? 'selected' : '' }}>In Review</option>
                            <option value="passed" {{ $application->status === 'passed' ? 'selected' : '' }}>Passed</option>
                            <option value="rejected" {{ $application->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition">
                            Perbarui Status Manual
                        </button>
                    </div>
                </form>
            </details>
        </div>

    </div>

    <!-- 2 Column Layout: Details & Sidebar -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Position Applied, Summary, Education, Experience, Skills -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Applied Position Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Informasi Posisi yang Dilamar</h4>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800">
                                {{ $application->position?->department ?? 'General' }}
                            </span>
                            <h5 class="text-base font-bold text-slate-900 mt-1.5">{{ $application->position?->title }}</h5>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $application->position?->location }} &bull; {{ $application->position?->type }} &bull; Level: {{ $application->position?->experience_level }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-slate-800">
                                Rp {{ number_format($application->position?->salary_min ?? 0, 0, ',', '.') }} - {{ number_format($application->position?->salary_max ?? 0, 0, ',', '.') }}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-0.5">Rentang Gaji Ditawarkan</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bio Summary -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Tentang / Ringkasan Diri</h4>
                <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-200">
                    {{ $candidate?->profile?->summary ?? 'Pelamar belum mengisi ringkasan biodata diri.' }}
                </p>
            </div>

            <!-- Cover Letter (Surat Lamaran) -->
            @if($application->cover_letter)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Surat Lamaran (Cover Letter)</h4>
                    <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-200 whitespace-pre-line">
                        {{ $application->cover_letter }}
                    </p>
                </div>
            @endif

            <!-- Work Experiences -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Riwayat Pengalaman Kerja</h4>
                <div class="space-y-3">
                    @forelse($candidate?->experiences ?? [] as $exp)
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h5 class="text-xs font-bold text-slate-900">{{ $exp->position }}</h5>
                                    <div class="text-xs text-indigo-700 font-semibold mt-0.5">{{ $exp->company_name }}</div>
                                </div>
                                <span class="text-[10px] text-slate-500 font-medium bg-white px-2 py-0.5 rounded border border-slate-200">
                                    {{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('M Y') : '' }} -
                                    {{ $exp->is_current ? 'Sekarang' : ($exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : '') }}
                                </span>
                            </div>
                            @if($exp->description)
                                <p class="text-xs text-slate-600 mt-2">{{ $exp->description }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada riwayat pengalaman kerja yang dicatat.</p>
                    @endforelse
                </div>
            </div>

            <!-- Education History -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Riwayat Pendidikan Terakhir</h4>
                <div class="space-y-3">
                    @forelse($candidate?->education ?? [] as $edu)
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex items-center justify-between">
                            <div>
                                <h5 class="text-xs font-bold text-slate-900">{{ $edu->institution_name }}</h5>
                                <div class="text-xs text-slate-600 mt-0.5">{{ $edu->degree }} &bull; {{ $edu->field_of_study }}</div>
                            </div>
                            <div class="text-right">
                                @if($edu->gpa)
                                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                        IPK: {{ $edu->gpa }}
                                    </span>
                                @endif
                                <div class="text-[10px] text-slate-400 mt-1">Lulus {{ $edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('Y') : '-' }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada riwayat pendidikan yang dicatat.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Column: Status Pipeline, CV Document, Skills & Links -->
        <div class="space-y-6">

            <!-- CV Document Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Dokumen Resume / CV Pelamar</h4>

                @if($resumeFile)
                    @php
                        $isUrl = str_starts_with($resumeFile, 'http');
                        $fileLink = $isUrl ? $resumeFile : ($candidate ? route('documents.resume', ['candidate' => $candidate->id, 'view' => 1]) : '#');
                        $filename = basename($resumeFile);
                    @endphp
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="p-2 rounded-lg bg-rose-100 text-rose-600 shrink-0">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="truncate">
                                <div class="text-xs font-bold text-slate-800 truncate">{{ $filename }}</div>
                                <div class="text-[10px] text-slate-400">Berkas Curriculum Vitae</div>
                            </div>
                        </div>
                        <a href="{{ $fileLink }}" target="_blank"
                           class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white font-bold text-xs hover:bg-indigo-700 transition shrink-0 ml-2">
                            Buka / Unduh
                        </a>
                    </div>
                @else
                    <div class="p-4 rounded-xl border border-dashed border-slate-200 text-center">
                        <p class="text-xs text-slate-400">Pelamar belum mengunggah file resume/CV.</p>
                    </div>
                @endif
            </div>

            <!-- Pipeline Status Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Status Tahapan Rekrutmen</h4>

                <div class="space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-semibold">Tahap Saat Ini:</span>
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-800 uppercase">
                            {{ $application->stage }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-semibold">Status Seleksi:</span>
                        <span class="font-bold capitalize text-slate-800">
                            {{ str_replace('_', ' ', $application->status) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-semibold">Waktu Melamar:</span>
                        <span class="text-slate-600 font-medium">
                            {{ $application->applied_at ? \Carbon\Carbon::parse($application->applied_at)->format('d M Y, H:i') : '-' }} WIB
                        </span>
                    </div>
                </div>

                @if($application->hr_notes)
                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-[11px] font-bold text-slate-600 block mb-1">Catatan Evaluasi HR:</span>
                        <p class="text-xs text-slate-700 bg-amber-50 p-3 rounded-xl border border-amber-200 leading-relaxed">
                            {{ $application->hr_notes }}
                        </p>
                    </div>
                @endif
            </div>

            <!-- Skills List -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Daftar Keahlian</h4>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($candidate?->skills ?? [] as $skill)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                            {{ $skill->skill_name }}
                            @if($skill->proficiency_level)
                                <span class="ml-1 text-[10px] font-bold text-indigo-600">({{ $skill->proficiency_level }})</span>
                            @endif
                        </span>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada data keahlian.</p>
                    @endforelse
                </div>
            </div>

            <!-- Online Links (LinkedIn, Portfolio) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Tautan & Portofolio Online</h4>
                <div class="space-y-2 text-xs">
                    @if($candidate?->profile?->linkedin_url)
                        <a href="{{ $candidate->profile->linkedin_url }}" target="_blank"
                           class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 hover:bg-blue-50/50 hover:border-blue-200 text-blue-700 font-semibold transition">
                            <span>LinkedIn Profile</span>
                            <span class="text-xs">&rarr;</span>
                        </a>
                    @endif

                    @if($candidate?->profile?->portfolio_url)
                        <a href="{{ $candidate->profile->portfolio_url }}" target="_blank"
                           class="flex items-center justify-between p-2.5 rounded-xl border border-slate-200 hover:bg-purple-50/50 hover:border-purple-200 text-purple-700 font-semibold transition">
                            <span>Website / Portofolio</span>
                            <span class="text-xs">&rarr;</span>
                        </a>
                    @endif

                    @if(!$candidate?->profile?->linkedin_url && !$candidate?->profile?->portfolio_url)
                        <p class="text-xs text-slate-400 italic">Belum ada tautan profil yang dicantumkan.</p>
                    @endif
                </div>
            </div>

        </div>

    </div>

</div>

<!-- REJECTION REASON MODAL -->
<div id="rejectModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-200">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h4 class="text-sm font-bold text-rose-600 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Tolak Lamaran Kandidat
            </h4>
            <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-600">&times;</button>
        </div>
        <p class="text-xs text-slate-600">
            Apakah Anda yakin ingin menggugurkan lamaran ini? Kandidat akan diberi status <strong>Tidak Lolos (Rejected)</strong>.
        </p>
        <form method="POST" action="{{ $updateStageUrl }}" class="space-y-4">
            @csrf
            <input type="hidden" name="stage" value="rejected">
            <input type="hidden" name="status" value="rejected">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Penolakan / Catatan HR:</label>
                <textarea name="hr_notes" rows="3" required
                          class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                          placeholder="Tuliskan alasan penolakan untuk arsip evaluasi HR..."></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-600/30">
                    Konfirmasi Tolak Lamaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>
@endsection
