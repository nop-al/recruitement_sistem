@extends('layouts.dashboard')

@section('title', 'Portal Kandidat - Status Lamaran & Lowongan')
@section('role-label', 'Pelamar / Job Applicant')
@section('page-title', 'Portal Karir & Status Seleksi Pelamar')

@section('role-nav')
    <a href="#panduan" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition text-xs font-semibold">Cara Melamar</a>
    <a href="#status" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 font-semibold border border-blue-200 text-xs">Status Lamaran</a>
    <a href="#vacancies" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition text-xs font-semibold">Cari Lowongan</a>
    <a href="#profile-summary" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition text-xs font-semibold">Profil &amp; CV</a>
@endsection

@section('content')
<div class="space-y-8">

    <!-- Candidate Welcome & Hero Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-blue-900 via-indigo-950 to-slate-900 p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <div class="flex items-center gap-4">
                @if($candidate?->profile?->photo)
                    <img src="{{ asset('storage/' . $candidate->profile->photo) }}"
                         alt="{{ $candidate?->full_name }}"
                         class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover ring-4 ring-blue-400/30 shadow-xl shrink-0">
                @else
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center font-black text-xl sm:text-2xl text-white ring-4 ring-blue-400/30 shadow-xl shrink-0">
                        {{ strtoupper(substr($candidate?->first_name ?? 'A', 0, 1) . substr($candidate?->last_name ?? 'F', 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="flex items-center gap-2 flex-wrap mb-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold border border-blue-400/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Portal Resmi Rekrutmen
                        </span>
                        @php
                            $firstApp = $candidate?->applications->first();
                        @endphp
                        @if($firstApp)
                            <span class="inline-flex items-center gap-1 px-3 py-0.5 rounded-full bg-white/15 text-white text-xs font-mono font-bold border border-white/20">
                                ID Pelamar: 2026-{{ str_pad((string)$candidate?->id, 4, '0', STR_PAD_LEFT) }}
                            </span>
                        @endif
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Halo, {{ $candidate?->full_name ?? 'Kandidat' }}!</h2>
                    <p class="text-blue-100/80 text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
                        Selamat datang di portal karir. Proses seleksi di sini dirancang transparan, cepat, dan mudah dipahami.
                    </p>
                </div>
            </div>

            <!-- Profile Completion Progress Card -->
            <div class="p-4 rounded-xl bg-white/10 backdrop-blur border border-white/15 shrink-0 min-w-[260px]">
                <div class="flex items-center justify-between text-xs mb-2">
                    <span class="font-semibold text-slate-200">Kelengkapan Profil CV</span>
                    <span class="font-bold text-emerald-300">{{ $profileCompletion }}% Lengkap</span>
                </div>
                <div class="w-full bg-slate-800/80 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-400 to-emerald-400 h-2.5 rounded-full transition-all duration-500" style="width: {{ $profileCompletion }}%"></div>
                </div>
                <div class="flex items-center justify-between mt-2.5 text-[11px]">
                    @php
                        $hasResume = (bool) ($candidate?->profile?->resume_path);
                    @endphp
                    @if($hasResume)
                        <span class="text-emerald-300 font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                            CV Siap Digunakan
                        </span>
                    @else
                        <span class="text-amber-300 font-semibold flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Belum Upload CV
                        </span>
                    @endif
                    <a href="{{ route('kadidat.profile.edit') }}" class="text-blue-300 hover:text-white font-bold underline">Edit Profil &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 1: PANDUAN 3 LANGKAH MUDAH MELAMAR PEKERJAAN (SIMPLE APPLY GUIDE)  -->
    <!-- ========================================================================= -->
    <div id="panduan" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-5 border-b border-slate-100">
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200 mb-1">
                    Panduan Praktis Pelamar
                </span>
                <h3 class="text-lg font-bold text-slate-900">Cara Mudah Melamar Pekerjaan di Sistem Kami</h3>
                <p class="text-xs text-slate-500">Ikuti 3 tahapan sederhana ini untuk mengirimkan lamaran Anda secara instan</p>
            </div>
            <a href="{{ route('kadidat.positions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-800 shrink-0">
                <span>Lihat Semua Lowongan</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-6">
            <!-- Step 1 -->
            <div class="relative p-5 rounded-2xl border {{ $hasResume ? 'border-emerald-200 bg-emerald-50/40' : 'border-amber-200 bg-amber-50/40' }} transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl {{ $hasResume ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white' }} flex items-center justify-center font-extrabold text-sm shadow">
                        1
                    </div>
                    @if($hasResume)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                            CV Siap Digunakan
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            Wajib Diunggah
                        </span>
                    @endif
                </div>
                <h4 class="text-sm font-bold text-slate-900">1. Siapkan Profil &amp; CV</h4>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                    Unggah resume CV Anda (format PDF/DOCX, maks 10MB) sekali saja. CV ini akan otomatis dilampirkan pada setiap posisi yang Anda lamar.
                </p>
                <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs">
                    @if($hasResume)
                        <span class="text-[11px] text-slate-500 truncate max-w-[130px]">{{ basename($candidate->profile->resume_path) }}</span>
                        <a href="{{ route('kadidat.profile.edit') }}" class="font-bold text-emerald-700 hover:underline">Ganti CV &rarr;</a>
                    @else
                        <span class="text-[11px] text-amber-700 font-medium">CV belum ada</span>
                        <a href="{{ route('kadidat.profile.edit') }}" class="font-bold text-amber-800 underline">Upload Sekarang &rarr;</a>
                    @endif
                </div>
            </div>

            <!-- Step 2 -->
            <div class="relative p-5 rounded-2xl border border-blue-200 bg-blue-50/40 transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-extrabold text-sm shadow">
                        2
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                        {{ $activeVacanciesCount ?? 4 }} Posisi Terbuka
                    </span>
                </div>
                <h4 class="text-sm font-bold text-slate-900">2. Pilih Posisi yang Cocok</h4>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                    Jelajahi lowongan pekerjaan aktif berdasarkan bidang keahlian, departemen, tipe kerja (Full-Time, Remote, Kontrak), dan perkiraan gaji.
                </p>
                <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-500">Cari lowongan impian Anda</span>
                    <a href="#vacancies" class="font-bold text-blue-700 hover:underline">Pilih Lowongan &darr;</a>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="relative p-5 rounded-2xl border border-indigo-200 bg-indigo-50/40 transition">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-extrabold text-sm shadow">
                        3
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                        1-Klik Melamar
                    </span>
                </div>
                <h4 class="text-sm font-bold text-slate-900">3. Kirim Lamaran &amp; Pantau</h4>
                <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">
                    Cukup klik <strong>"Lamar Posisi"</strong>. Anda dapat memantau setiap progres seleksi (Review CV, Tes Online, Interview, hingga Offering) secara real-time.
                </p>
                <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-500">Transparan &amp; Tanpa Ribet</span>
                    <a href="#status" class="font-bold text-indigo-700 hover:underline">Cek Lamaran Saya &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 2: PELACAK STATUS LAMARAN KERJA AKTIF                             -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8" id="status">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 mb-1">
                    Status Seleksi Real-Time
                </span>
                <h3 class="text-lg font-bold text-slate-900">Pelacak Status Lamaran Kerja Aktif</h3>
                <p class="text-xs text-slate-500">Pantau proses tahapan rekrutmen Anda dari awal hingga penawaran kerja</p>
            </div>
            @php
                $appCount = $candidate?->applications->count() ?? 0;
            @endphp
            <div class="flex items-center gap-2 shrink-0">
                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-full border border-slate-200">
                    Total: {{ $appCount }} Lamaran Diajukan
                </span>
            </div>
        </div>

        @forelse($candidate?->applications ?? [] as $app)
            @php
                $stage = strtolower($app->stage ?? 'screening');
                $stageLabels = [
                    'applied' => '1. Pendaftaran Terkirim',
                    'screening' => '2. Seleksi & Screening CV',
                    'assessment' => '3. Ujian Online (Assessment)',
                    'interview' => '4. Wawancara / Interview',
                    'offering' => '5. Penawaran Kerja (Offering)',
                    'onboarding' => '6. Onboarding Karyawan',
                    'hired' => 'Lulus & Diterima Bekerja',
                    'rejected' => 'Tidak Lolos Seleksi',
                ];

                $currentStageIndex = match($stage) {
                    'applied' => 1,
                    'screening' => 2,
                    'assessment' => 3,
                    'interview' => 4,
                    'offering' => 5,
                    'onboarding', 'hired' => 6,
                    default => 2,
                };
            @endphp

            <div class="mt-6 p-6 rounded-2xl border border-slate-200 bg-slate-50/70 hover:bg-slate-50 transition shadow-xs">
                <!-- Application Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-200/80">
                    <div>
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                {{ $app->position?->department ?? 'Engineering' }}
                            </span>
                            <span class="text-[10px] font-mono font-semibold px-2 py-0.5 rounded bg-slate-200 text-slate-700">
                                No. Registrasi: APP-{{ str_pad((string)$app->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                        <h4 class="text-lg font-extrabold text-slate-900 mt-1">{{ $app->position?->title ?? 'Posisi Lowongan' }}</h4>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Dilamar pada: <strong class="text-slate-700">{{ $app->applied_at ? \Carbon\Carbon::parse($app->applied_at)->format('d M Y, H:i') : '-' }} WIB</strong> &bull; Lokasi: {{ $app->position?->location }} &bull; Tipe: {{ $app->position?->type }}
                        </p>
                    </div>

                    <div class="text-right shrink-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                            {{ $app->match_score >= 90 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                            Kecocokan Profil: {{ $app->match_score ?? 90 }}%
                        </span>
                    </div>
                </div>

                <!-- Step Progression Timeline (Visual Stepper) -->
                <div class="py-6 overflow-x-auto">
                    <div class="min-w-[650px]">
                        <div class="relative">
                            <!-- Connecting Line -->
                            <div class="absolute top-4 left-6 right-6 h-1 bg-slate-200 -z-0"></div>
                            <div class="absolute top-4 left-6 h-1 bg-emerald-500 -z-0 transition-all" style="width: {{ min(100, max(10, (($currentStageIndex - 1) / 5) * 100)) }}%"></div>

                            <div class="grid grid-cols-6 gap-2 relative z-10">
                                <!-- Step 1: Pendaftaran -->
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs ring-4 ring-white shadow">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-bold text-slate-900 mt-2">1. Pendaftaran</span>
                                    <span class="text-[10px] text-emerald-600 font-semibold">Terkirim</span>
                                </div>

                                <!-- Step 2: Screening CV -->
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-8 h-8 rounded-full {{ $currentStageIndex >= 2 ? 'bg-emerald-600 text-white ring-emerald-100' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold text-xs ring-4 ring-white shadow">
                                        @if($currentStageIndex > 2)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            2
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold {{ $currentStageIndex >= 2 ? 'text-slate-900' : 'text-slate-400' }} mt-2">2. Review CV</span>
                                    <span class="text-[10px] {{ $currentStageIndex > 2 ? 'text-emerald-600 font-semibold' : ($currentStageIndex == 2 ? 'text-blue-600 font-bold' : 'text-slate-400') }}">
                                        {{ $currentStageIndex > 2 ? 'Lolos Review' : ($currentStageIndex == 2 ? 'Sedang Ditinjau' : 'Antrean') }}
                                    </span>
                                </div>

                                <!-- Step 3: Online Assessment -->
                                <a href="{{ route('kadidat.assessments.index') }}" class="flex flex-col items-center text-center group hover:scale-105 transition">
                                    <div class="w-8 h-8 rounded-full {{ $currentStageIndex >= 3 ? 'bg-emerald-600 text-white ring-emerald-100' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold text-xs ring-4 ring-white shadow group-hover:ring-blue-200">
                                        @if($currentStageIndex > 3)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            3
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold {{ $currentStageIndex >= 3 ? 'text-slate-900 group-hover:text-blue-600' : 'text-slate-400' }} mt-2">3. Tes Online</span>
                                    <span class="text-[10px] {{ $currentStageIndex > 3 ? 'text-emerald-600 font-semibold' : ($currentStageIndex == 3 ? 'text-blue-600 font-bold' : 'text-slate-400') }}">
                                        {{ $currentStageIndex > 3 ? 'Selesai' : ($currentStageIndex == 3 ? 'Buka Tes &rarr;' : 'Menunggu') }}
                                    </span>
                                </a>

                                <!-- Step 4: User Interview -->
                                <div class="flex flex-col items-center text-center">
                                    <div class="w-8 h-8 rounded-full {{ $currentStageIndex >= 4 ? 'bg-blue-600 text-white ring-blue-200 animate-pulse' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold text-xs ring-4 ring-white shadow">
                                        @if($currentStageIndex > 4)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            4
                                        @endif
                                    </div>
                                    <span class="text-xs font-bold {{ $currentStageIndex >= 4 ? 'text-blue-700' : 'text-slate-400' }} mt-2">4. Wawancara</span>
                                    <span class="text-[10px] {{ $currentStageIndex > 4 ? 'text-emerald-600 font-semibold' : ($currentStageIndex == 4 ? 'text-blue-600 font-bold' : 'text-slate-400') }}">
                                        {{ $currentStageIndex > 4 ? 'Selesai' : ($currentStageIndex == 4 ? 'Tahap Aktif' : 'Menunggu') }}
                                    </span>
                                </div>

                                <!-- Step 5: Offering -->
                                <a href="{{ route('kadidat.offerings.index') }}" class="flex flex-col items-center text-center group hover:scale-105 transition">
                                    <div class="w-8 h-8 rounded-full {{ $currentStageIndex >= 5 ? 'bg-emerald-600 text-white ring-emerald-100' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold text-xs ring-4 ring-white shadow group-hover:ring-emerald-200">
                                        @if($currentStageIndex > 5)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            5
                                        @endif
                                    </div>
                                    <span class="text-xs font-semibold {{ $currentStageIndex >= 5 ? 'text-emerald-700 font-bold group-hover:text-emerald-600' : 'text-slate-400' }} mt-2">5. Offering</span>
                                    <span class="text-[10px] {{ $currentStageIndex >= 5 ? 'text-emerald-600 font-bold' : 'text-slate-400' }}">
                                        {{ $currentStageIndex >= 5 ? 'Tersedia' : 'Tahap Lanjutan' }}
                                    </span>
                                </a>

                                <!-- Step 6: Onboarding -->
                                <a href="{{ route('kadidat.onboarding.index') }}" class="flex flex-col items-center text-center group hover:scale-105 transition">
                                    <div class="w-8 h-8 rounded-full {{ $currentStageIndex >= 6 ? 'bg-cyan-600 text-white ring-cyan-100' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center font-bold text-xs ring-4 ring-white shadow group-hover:ring-cyan-200">
                                        6
                                    </div>
                                    <span class="text-xs font-semibold {{ $currentStageIndex >= 6 ? 'text-cyan-700 font-bold group-hover:text-cyan-600' : 'text-slate-400' }} mt-2">6. Onboarding</span>
                                    <span class="text-[10px] {{ $currentStageIndex >= 6 ? 'text-cyan-600 font-bold' : 'text-slate-400' }}">
                                        {{ $currentStageIndex >= 6 ? 'Tahap Akhir' : 'Tahap Akhir' }}
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Callout Box: What to do next (Actionable Instructions) -->
                @if($stage === 'screening')
                    <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-blue-600 text-white shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-slate-900">Berkas CV Anda Sedang Dalam Tahap Screening</h5>
                                <p class="text-xs text-slate-600 mt-0.5">Tim Talent Acquisition sedang meninjau kualifikasi dan CV Anda. Anda akan menerima notifikasi jika dinyatakan berhak mengikuti tes/interview.</p>
                            </div>
                        </div>
                        <a href="{{ route('kadidat.profile.edit') }}" class="px-4 py-2 rounded-xl bg-white border border-blue-300 hover:bg-blue-100 text-blue-800 text-xs font-bold transition shrink-0 text-center">
                            Periksa Berkas CV Saya
                        </a>
                    </div>
                @elseif($stage === 'assessment')
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-emerald-600 text-white shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-emerald-950">Tahap Online Assessment Tersedia!</h5>
                                <p class="text-xs text-emerald-800 mt-0.5">Selamat! Anda telah lolos screening berkas dan diundang mengikuti ujian tes teknis/logika secara online.</p>
                            </div>
                        </div>
                        <a href="{{ route('kadidat.assessments.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/30 transition shrink-0 text-center">
                            Mulai Kerjakan Ujian &rarr;
                        </a>
                    </div>
                @elseif($stage === 'interview')
                    <div class="p-4 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-blue-600 text-white shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-slate-900">
                                    @if($upcomingInterview)
                                        {{ $upcomingInterview->interview_method === 'offline' ? 'Jadwal Sesi Wawancara Tatap Muka (Offline)' : 'Jadwal Sesi Wawancara Online Anda' }}
                                    @else
                                        Tahap Wawancara (Menunggu Penjadwalan)
                                    @endif
                                </h5>
                                <p class="text-xs text-slate-600 mt-0.5">
                                    @if($upcomingInterview)
                                        {{ $upcomingInterview->interview_method === 'offline' ? 'Wawancara langsung tatap muka bersama tim penilai & HR di kantor.' : 'Wawancara Arsitektur & Keahlian Teknis bersama tim pewawancara ahli.' }}
                                    @else
                                        Selamat! Anda lolos penilaian tes online. Tim HR sedang menetapkan jadwal sesi wawancara untuk Anda.
                                    @endif
                                </p>
                                @if($upcomingInterview?->scheduled_at)
                                    <div class="text-[11px] text-blue-700 font-semibold mt-1">
                                        Waktu: {{ \Carbon\Carbon::parse($upcomingInterview->scheduled_at)->format('d M Y, H:i') }} WIB &bull; Durasi: 60 Menit
                                    </div>
                                    @if($upcomingInterview->interview_method === 'offline')
                                        <div class="text-[11px] text-amber-800 font-bold mt-1 flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span>Lokasi: {{ $upcomingInterview->location ?? 'Kantor Pusat Perusahaan' }}</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-[11px] text-amber-700 font-semibold mt-1">
                                        Status: Tim HR sedang mencocokkan jadwal interviewer. Notifikasi akan dikirimkan saat jadwal siap.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            @if($upcomingInterview && $upcomingInterview->interview_method === 'offline')
                                <button type="button" onclick="navigator.clipboard.writeText('{{ addslashes($upcomingInterview->location ?? 'Kantor Pusat') }}'); alert('Alamat wawancara disalin ke clipboard!');"
                                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold shadow-md shadow-amber-600/30 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                    </svg>
                                    <span>Salin Alamat Lokasi</span>
                                </button>
                            @elseif($upcomingInterview?->room_link)
                                <a href="{{ $upcomingInterview->room_link }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/30 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    <span>Buka Ruang Wawancara</span>
                                </a>
                            @else
                                <span class="px-3 py-1.5 rounded-lg bg-white border border-blue-200 text-slate-500 text-xs font-semibold">
                                    Menunggu Jadwal HR
                                </span>
                            @endif
                        </div>
                    </div>
                @elseif($stage === 'offering')
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-emerald-600 text-white shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-emerald-950">Surat Penawaran Kerja (Offering Letter) Telah Terbit!</h5>
                                <p class="text-xs text-emerald-800 mt-0.5">Selamat, Anda terpilih bergabung bersama kami! Silakan tinjau rincian gaji, tanggal mulai kerja, dan tanda tangani surat penawaran.</p>
                            </div>
                        </div>
                        <a href="{{ route('kadidat.offerings.index') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/30 transition shrink-0 text-center">
                            Tinjau Surat Penawaran &rarr;
                        </a>
                    </div>
                @elseif($stage === 'onboarding' || $stage === 'hired')
                    <div class="p-4 rounded-xl bg-cyan-50 border border-cyan-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-cyan-600 text-white shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-cyan-950">Proses Onboarding Karyawan Baru</h5>
                                <p class="text-xs text-cyan-800 mt-0.5">Lengkapi checklist dokumen Anda (KTP, NPWP, BPJS, Rekening Bank, Pas Foto, dan Kontrak) untuk penerbitan ID Card dan payroll.</p>
                            </div>
                        </div>
                        <a href="{{ route('kadidat.onboarding.index') }}" class="px-5 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white text-xs font-bold shadow-md shadow-cyan-600/30 transition shrink-0 text-center">
                            Buka Checklist Onboarding &rarr;
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="p-8 text-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 mt-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Anda Belum Memiliki Lamaran Aktif</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    Kirim lamaran pertama Anda sekarang! Pilih salah satu lowongan terbuka di bawah ini, proses apply hanya butuh 1 klik.
                </p>
                <a href="#vacancies" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition">
                    <span>Pilih Lowongan Pekerjaan</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </a>
            </div>
        @endforelse
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 3: LOWONGAN PEKERJAAN TERSEDIA & FITUR LAMAR CEPAT (QUICK APPLY)   -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8" id="vacancies">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 border border-indigo-200 mb-1">
                    Peluang Karir Terbuka
                </span>
                <h3 class="text-lg font-bold text-slate-900">Daftar Lowongan Kerja &amp; Lamar Cepat</h3>
                <p class="text-xs text-slate-500">Pilih posisi yang Anda minati dan kirimkan lamaran secara langsung menggunakan CV profil aktif Anda</p>
            </div>
            <a href="{{ route('kadidat.positions.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-slate-700 text-xs font-bold transition shrink-0">
                <span>Jelajahi Semua ({{ $activeVacanciesCount ?? 0 }})</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-6">
            @forelse($recommendedPositions as $recPos)
                @php
                    $isAlreadyApplied = in_array($recPos->id, $appliedPositionIds ?? []);
                @endphp
                <div class="p-5 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 hover:shadow-lg transition flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2.5">
                            <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $recPos->department }}
                            </span>
                            <span class="text-[10px] font-semibold text-slate-400">
                                Kuota: {{ $recPos->quota }} Orang
                            </span>
                        </div>

                        <h4 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition">
                            <a href="{{ route('kadidat.positions.show', $recPos->id) }}">{{ $recPos->title }}</a>
                        </h4>

                        <div class="flex items-center gap-3 text-xs text-slate-500 mt-2">
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $recPos->location }}
                            </span>
                            <span>&bull;</span>
                            <span class="font-medium text-slate-600">{{ $recPos->type }}</span>
                        </div>

                        <div class="mt-3.5 p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-slate-400 font-semibold block">Estimasi Gaji</span>
                                <span class="text-xs font-black text-emerald-700">
                                    Rp {{ number_format($recPos->salary_min, 0, ',', '.') }} - {{ number_format($recPos->salary_max, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        @if($recPos->description)
                            <p class="text-xs text-slate-500 mt-3 line-clamp-2 leading-relaxed">
                                {{ Str::limit(strip_tags($recPos->description), 110) }}
                            </p>
                        @endif
                    </div>

                    <div class="mt-5 pt-3.5 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="{{ route('kadidat.positions.show', $recPos->id) }}"
                           class="px-3 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition text-center">
                            Lihat Rincian
                        </a>

                        @if($isAlreadyApplied)
                            <span class="inline-flex items-center gap-1 px-3 py-2 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Sudah Dilamar
                            </span>
                        @else
                            <button type="button"
                                    onclick="openQuickApplyModal({{ $recPos->id }}, '{{ addslashes($recPos->title) }}', '{{ addslashes($recPos->department) }}')"
                                    class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center gap-1.5 shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                <span>Lamar Cepat</span>
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 p-8 text-center rounded-2xl bg-slate-50 text-slate-500 text-xs">
                    Belum ada rekomendasi lowongan baru saat ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- SECTION 4: RINGKASAN PROFIL & BERKAS CV SAYA                              -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" id="profile-summary">
        <!-- Left 2 Cols: Biodata & Experience -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Ringkasan Riwayat &amp; Resume</h3>
                    <p class="text-xs text-slate-500">Data profil yang digunakan tim HR dan interviewer untuk mengevaluasi Anda</p>
                </div>
                <a href="{{ route('kadidat.profile.edit') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">
                    Edit Profil Lengkap &rarr;
                </a>
            </div>

            <!-- Summary bio -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tentang Saya</h4>
                <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-200/60">
                    {{ $candidate?->profile?->summary ?? 'Belum mengisi ringkasan diri. Tambahkan keahlian utama dan ketertarikan karir Anda.' }}
                </p>
            </div>

            <!-- Education & Experience in compact grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Education Card -->
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Pendidikan</h5>
                        <a href="{{ route('kadidat.profile.edit') }}#pendidikan" class="text-[11px] text-blue-600 font-bold">+ Tambah</a>
                    </div>
                    @forelse($candidate?->education ?? [] as $edu)
                        <div class="mt-2 text-xs">
                            <div class="font-bold text-slate-800">{{ $edu->institution_name }}</div>
                            <div class="text-[11px] text-slate-500">{{ $edu->degree }} &bull; {{ $edu->field_of_study }} (IPK: {{ $edu->gpa }})</div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada riwayat pendidikan.</p>
                    @endforelse
                </div>

                <!-- Experience Card -->
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                    <div class="flex items-center justify-between mb-2">
                        <h5 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Pengalaman Terakhir</h5>
                        <a href="{{ route('kadidat.profile.edit') }}#pengalaman" class="text-[11px] text-blue-600 font-bold">+ Tambah</a>
                    </div>
                    @forelse($candidate?->experiences ?? [] as $exp)
                        <div class="mt-2 text-xs">
                            <div class="font-bold text-slate-800">{{ $exp->position }}</div>
                            <div class="text-[11px] text-blue-700 font-semibold">{{ $exp->company_name }} ({{ $exp->start_date?->format('Y') }} - {{ $exp->is_current ? 'Sekarang' : $exp->end_date?->format('Y') }})</div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada riwayat pengalaman kerja.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Col: Skills & Documents Card -->
        <div class="space-y-6">
            <!-- Uploaded Documents Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Berkas Resume CV Utama</h3>
                    <span class="w-2 h-2 rounded-full {{ $hasResume ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                </div>

                <div class="mt-4 space-y-2 text-xs">
                    @php
                        $uploadedResume = $candidate?->profile?->resume_path ?: $candidate?->applications?->firstWhere('resume_url', '!=', null)?->resume_url;
                    @endphp
                    @if($uploadedResume)
                        @php
                            $isUrl = str_starts_with($uploadedResume, 'http');
                            $resumeUrl = $isUrl ? $uploadedResume : ($candidate ? route('documents.resume', ['candidate' => $candidate->id, 'view' => 1]) : '#');
                        @endphp
                        <div class="p-3.5 rounded-xl border border-blue-200 flex items-center justify-between bg-blue-50/40">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="p-2 rounded-lg bg-rose-100 text-rose-600 shrink-0">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="truncate">
                                    <div class="font-bold text-slate-900 truncate">{{ basename($uploadedResume) }}</div>
                                    <div class="text-[10px] text-emerald-700 font-semibold flex items-center gap-1">
                                        <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Dokumen CV Aktif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 shrink-0 ml-2">
                                <a href="{{ $resumeUrl }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-blue-600 text-xs font-bold transition">
                                    Unduh
                                </a>
                                <a href="{{ route('kadidat.profile.edit') }}" class="px-2.5 py-1 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition">
                                    Ganti
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="p-3.5 rounded-xl border border-dashed border-amber-300 bg-amber-50 flex items-center justify-between">
                            <div class="text-[11px] text-amber-800">
                                <strong>CV belum diunggah.</strong> Silakan upload CV agar proses melamar berjalan lancar.
                            </div>
                            <a href="{{ route('kadidat.profile.edit') }}" class="px-3 py-1.5 rounded-lg bg-amber-600 text-white text-[11px] font-bold shrink-0 ml-2">
                                Upload CV &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Skills Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Keahlian &amp; Kemampuan</h3>
                    <a href="{{ route('kadidat.profile.edit') }}#skills" class="text-[11px] text-blue-600 font-semibold hover:underline">+ Tambah</a>
                </div>

                <div class="mt-4 flex flex-wrap gap-1.5">
                    @forelse($candidate?->skills ?? [] as $skill)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 text-xs font-medium border border-slate-200">
                            <strong>{{ $skill->skill_name }}</strong>
                            <span class="text-[10px] text-slate-500">({{ $skill->years_of_experience }} thn)</span>
                        </span>
                    @empty
                        <p class="text-xs text-slate-400 italic">Belum ada skill yang ditambahkan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- MODAL: QUICK APPLY (LAMAR CEPAT 1-KLIK)                                   -->
<!-- ========================================================================= -->
<div id="quickApplyModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 relative animate-in fade-in zoom-in-95 duration-200">
        <!-- Close Button -->
        <button type="button" onclick="closeQuickApplyModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Modal Header -->
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
            </div>
            <div>
                <span id="modalDeptBadge" class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                    Departemen
                </span>
                <h3 id="modalPositionTitle" class="text-base font-bold text-slate-900 mt-1">Konfirmasi Lamaran Pekerjaan</h3>
            </div>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed">
            Anda akan mengirimkan lamaran kerja untuk posisi di atas. Berkas profil dan resume CV Anda akan otomatis dievaluasi oleh tim recruitment.
        </p>

        <!-- CV Status Alert in Modal -->
        @if($hasResume)
            <div class="mt-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 truncate">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div class="truncate">
                        <div class="font-bold text-emerald-950 truncate">Lampiran: {{ basename($candidate->profile->resume_path) }}</div>
                        <div class="text-[11px] text-emerald-700">CV aktif dari profil Anda akan disertakan</div>
                    </div>
                </div>
                <a href="{{ route('kadidat.profile.edit') }}" class="text-[11px] text-emerald-800 font-bold underline shrink-0 ml-2">Ganti CV</a>
            </div>
        @else
            <div class="mt-4 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs">
                <div class="flex items-center gap-2 text-amber-900 font-bold mb-1">
                    <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <span>Anda belum mengunggah dokumen CV!</span>
                </div>
                <p class="text-[11px] text-amber-800">
                    Sangat disarankan melengkapi CV terlebih dahulu agar peluang seleksi optimal.
                </p>
                <a href="{{ route('kadidat.profile.edit') }}" class="inline-block mt-2 font-bold text-blue-700 underline">Upload CV di Sini &rarr;</a>
            </div>
        @endif

        <!-- Form Submission -->
        <form id="quickApplyForm" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            <div>
                <label for="modalCoverLetter" class="block text-xs font-bold text-slate-700 mb-1">
                    Catatan Tambahan / Cover Letter Singkat (Opsional)
                </label>
                <textarea id="modalCoverLetter" name="cover_letter" rows="3"
                          placeholder="Ceritakan secara singkat keahlian utama Anda dan mengapa Anda bersemangat bergabung..."
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeQuickApplyModal()"
                        class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <span>Kirim Lamaran Sekarang</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openQuickApplyModal(positionId, title, department) {
        const modal = document.getElementById('quickApplyModal');
        const form = document.getElementById('quickApplyForm');
        const titleEl = document.getElementById('modalPositionTitle');
        const deptEl = document.getElementById('modalDeptBadge');

        titleEl.textContent = title;
        deptEl.textContent = department;
        form.action = "{{ url('/kadidat/positions') }}/" + positionId + "/apply";

        modal.classList.remove('hidden');
    }

    function closeQuickApplyModal() {
        const modal = document.getElementById('quickApplyModal');
        modal.classList.add('hidden');
    }

    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeQuickApplyModal();
        }
    });
</script>
@endsection
