@extends('layouts.dashboard')

@php
    $userRole = auth()->user()?->role?->name ?? 'guest';
    $rolePrefix = match($userRole) {
        'admin' => 'admin',
        'hr' => 'hr',
        default => 'kadidat',
    };
    $roleLabel = match($userRole) {
        'admin' => 'Administrator',
        'hr' => 'HR Recruiter',
        default => 'Pelamar / Job Seeker',
    };
@endphp

@section('title', 'Detail Lowongan: ' . $position->title)
@section('role-label', $roleLabel)
@section('page-title', 'Detail Lowongan Pekerjaan')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Top Navigation Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route($rolePrefix . '.positions.index') }}"
               class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition shadow-sm"
               title="Kembali ke Daftar Lowongan">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-0.5">
                    <a href="{{ route($rolePrefix . '.positions.index') }}" class="hover:text-indigo-600 font-semibold">Lowongan Kerja</a>
                    <span>/</span>
                    <span class="text-slate-700 font-medium">{{ $position->department }}</span>
                </div>
                <h2 class="text-xl font-black text-slate-900 tracking-tight">{{ $position->title }}</h2>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if(in_array($userRole, ['admin', 'hr'], true))
                <a href="#applicants"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs border border-indigo-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Pelamar ({{ $position->applications->count() }})</span>
                </a>

                <a href="{{ route($rolePrefix . '.positions.edit', $position->id) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold text-xs border border-amber-200 transition shadow-sm"
                   title="Edit Data Lowongan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Edit Lowongan</span>
                </a>

                <!-- Delete Position Form Button -->
                <form action="{{ route($rolePrefix . '.positions.destroy', $position->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lowongan pekerjaan ini secara permanen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition shadow-sm" title="Hapus Lowongan Pekerjaan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span>Hapus Lowongan</span>
                    </button>
                </form>
            @elseif($userRole === 'kandidat' && !$hasApplied)
                @if($hasActiveOffering && !$position->is_internal)
                    <button disabled class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs cursor-not-allowed border border-slate-200">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                        <span>Sudah Tahap Offering</span>
                    </button>
                @else
                    <a href="#apply-section"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Lamar Sekarang</span>
                    </a>
                @endif
            @endif
        </div>
    </div>

    <!-- Vacancy Hero Card -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-2.5">
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-indigo-100 text-indigo-800">
                        {{ $position->department }}
                    </span>
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                        {{ $position->type }}
                    </span>
                    @if($position->is_internal)
                        <span class="text-xs font-extrabold px-3 py-1 rounded-full bg-purple-100 text-purple-800 border border-purple-200 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Lowongan Internal
                        </span>
                    @endif
                    @if(in_array($userRole, ['admin', 'hr'], true))
                        <span class="text-xs font-extrabold uppercase px-2.5 py-0.5 rounded
                            {{ $position->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                            Status: {{ $position->status }}
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ $position->title }}</h1>

                <div class="text-xs text-slate-500 mt-2.5 flex flex-wrap items-center gap-x-4 gap-y-1.5">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $position->location }}</span>
                    </span>
                    <span>&bull;</span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span>Pengalaman: {{ $position->experience_level }}</span>
                    </span>
                    <span>&bull;</span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Kuota: {{ $position->quota }} orang</span>
                    </span>
                    @if($position->deadline)
                        <span>&bull;</span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Batas: {{ \Carbon\Carbon::parse($position->deadline)->format('d M Y') }}</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Salary Range Card -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 shrink-0 text-left lg:text-right">
                <span class="text-[11px] font-semibold text-slate-400 block">Rentang Gaji Ditawarkan</span>
                <div class="text-xl font-black text-slate-900 mt-0.5">
                    Rp {{ number_format($position->salary_min, 0, ',', '.') }} - {{ number_format($position->salary_max, 0, ',', '.') }}
                </div>
                <span class="text-[10px] text-emerald-600 font-semibold mt-1 block">Termasuk Tunjangan & BPJS</span>
            </div>
        </div>

        <!-- Candidate Application Status Banner (if already applied) -->
        @if($userRole === 'kandidat' && $hasApplied)
            <div class="mt-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-emerald-900">Anda telah mengajukan lamaran untuk posisi ini</h4>
                        <p class="text-[11px] text-emerald-700 mt-0.5">
                            Dilamar pada: {{ \Carbon\Carbon::parse($userApplication->applied_at)->format('d M Y, H:i') }} &bull;
                            Tahap seleksi: <span class="uppercase font-bold">{{ $userApplication->stage }}</span>
                        </p>
                    </div>
                </div>
                <a href="{{ route('kadidat.dashboard') }}" class="px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shrink-0 text-center">
                    Lihat di Dashboard &rarr;
                </a>
            </div>
        @endif
    </div>

    <!-- 2 Column Layout: Content Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column (2 Cols): Description, Requirements, Apply Form -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Description -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                    Deskripsi Pekerjaan & Tanggung Jawab
                </h3>
                <div class="text-xs text-slate-700 leading-relaxed space-y-3 whitespace-pre-line">
                    {{ $position->description }}
                </div>
            </div>

            <!-- Requirements -->
            @if($position->requirements)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Persyaratan & Kualifikasi Pelamar
                    </h3>
                    <div class="text-xs text-slate-700 leading-relaxed space-y-3 whitespace-pre-line">
                        {{ $position->requirements }}
                    </div>
                </div>
            @endif

            <!-- Candidate Application Submission Form -->
            @if($userRole === 'kandidat')
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6" id="apply-section">
                    <div class="pb-4 border-b border-slate-100">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="p-1.5 rounded-lg bg-blue-50 text-blue-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </span>
                            Formulir Pengajuan Lamaran Pekerjaan
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pastikan profil dan resume CV Anda sudah lengkap sebelum mengirimkan lamaran.</p>
                    </div>

                    @if($hasApplied)
                        <div class="p-6 sm:p-8 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto shadow-xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h4 class="text-base font-extrabold text-emerald-950">Lamaran Anda Berhasil Terkirim!</h4>
                            <p class="text-xs text-emerald-800 max-w-md mx-auto leading-relaxed">
                                Anda sudah terdaftar sebagai pelamar untuk posisi ini. Berkas profil dan CV Anda sedang dalam peninjauan oleh tim rekrutmen.
                            </p>
                            <div class="pt-2">
                                <a href="{{ route('kadidat.dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 shadow-md shadow-emerald-600/30 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Pantau Progres di Portal Kandidat</span>
                                </a>
                            </div>
                        </div>
                    @elseif($hasActiveOffering && !$position->is_internal)
                        <!-- Offering Restriction Banner -->
                        <div class="p-6 sm:p-8 rounded-2xl bg-amber-50 border border-amber-200 text-center space-y-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto shadow-xs">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-base font-extrabold text-amber-950">Tidak Dapat Mengajukan Lamaran Baru</h4>
                                <p class="text-xs text-amber-800 max-w-md mx-auto leading-relaxed mt-1">
                                    Anda saat ini sudah berada pada tahap <strong>Penawaran Kerja (Offering)</strong> untuk posisi lain. Sesuai kebijakan rekrutmen perusahaan, pelamar yang telah menerima penawaran kerja tidak dapat melamar lowongan kerja lain, <strong>kecuali lowongan kerja internal</strong>.
                                </p>
                            </div>
                            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                                <a href="{{ route('kadidat.offerings.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 text-white font-bold text-xs hover:bg-amber-700 shadow-md shadow-amber-600/30 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span>Tinjau Surat Offering Anda</span>
                                </a>
                                <a href="{{ route('kadidat.positions.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white border border-amber-300 text-amber-800 font-bold text-xs hover:bg-amber-100 transition">
                                    <span>Lihat Lowongan Lain</span>
                                </a>
                            </div>
                        </div>
                    @else
                        @if($position->is_internal)
                            <!-- Internal Vacancy Notice -->
                            <div class="p-4 rounded-xl bg-purple-50/80 border border-purple-200 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                                    🏢
                                </div>
                                <div class="text-xs text-purple-900 leading-relaxed">
                                    <strong>Lowongan Kerja Internal:</strong> Posisi ini dikhususkan bagi karyawan internal / mutasi. Pelamar dengan status Offering diperbolehkan mengajukan lamaran pada posisi internal ini.
                                </div>
                            </div>
                        @endif

                        <!-- Simple 1-Click Apply Flow Indicator -->
                        <div class="p-4 rounded-xl bg-blue-50/70 border border-blue-200 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm">
                                💡
                            </div>
                            <div class="text-xs text-blue-900 leading-relaxed">
                                <strong>Proses Melamar Mudah:</strong> Cukup pastikan berkas CV Anda sudah benar di bawah, lalu klik <strong>"Kirim Lamaran Sekarang"</strong>. Tidak perlu isi data berulang-ulang!
                            </div>
                        </div>

                        <!-- CV Status check -->
                        @php
                            $resumePath = $candidate?->profile?->resume_path;
                        @endphp

                        <div class="p-4 rounded-xl {{ $resumePath ? 'bg-emerald-50/60 border border-emerald-200' : 'bg-amber-50 border border-amber-200' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <span class="p-2 rounded-lg {{ $resumePath ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} shrink-0">
                                    @if($resumePath)
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                    @endif
                                </span>
                                <div class="text-xs">
                                    <div class="font-bold text-slate-900">
                                        {{ $resumePath ? 'Berkas CV Terlampir: ' . basename($resumePath) : 'Anda belum mengunggah dokumen CV!' }}
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        {{ $resumePath ? 'CV aktif dari profil Anda otomatis dilampirkan.' : 'Harap lengkapi CV terlebih dahulu agar peluang seleksi optimal.' }}
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('kadidat.profile.edit') }}" class="px-3.5 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-blue-600 text-xs font-bold transition shrink-0 text-center">
                                {{ $resumePath ? 'Ganti / Perbarui CV' : 'Upload CV Sekarang' }}
                            </a>
                        </div>

                        <form action="{{ route('kadidat.positions.apply', $position->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">
                                    Surat Pengantar / Catatan untuk Tim HR (Opsional)
                                </label>
                                <textarea name="cover_letter" rows="3" placeholder="Ceritakan secara singkat mengapa Anda antusias melamar posisi ini..."
                                          class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">{{ old('cover_letter') }}</textarea>
                                @error('cover_letter')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                                <span class="text-[11px] text-slate-400">
                                    Dengan mengirim lamaran, Anda menyetujui evaluasi profil oleh tim recruitment.
                                </span>
                                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center justify-center gap-2 shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    <span>Kirim Lamaran Sekarang</span>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @endif

        </div>

        <!-- Right Column: Sidebar Specs & Summary -->
        <div class="space-y-6">

            <!-- Summary Specifications Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-100">
                    Ringkasan Lowongan
                </h4>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Kategori Lowongan</span>
                        <span class="font-bold {{ $position->is_internal ? 'text-purple-700' : 'text-slate-800' }}">
                            {{ $position->is_internal ? 'Internal Perusahaan' : 'Eksternal / Umum' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Departemen</span>
                        <span class="font-bold text-slate-900">{{ $position->department }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Lokasi Kerja</span>
                        <span class="font-semibold text-slate-800">{{ $position->location }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Tipe Kontrak</span>
                        <span class="font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded">{{ $position->type }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Tingkat Pengalaman</span>
                        <span class="font-semibold text-slate-800">{{ $position->experience_level }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Kuota Lowongan</span>
                        <span class="font-bold text-indigo-700">{{ $position->quota }} Orang</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Total Pelamar Masuk</span>
                        <span class="font-bold text-emerald-700">{{ $position->applications->count() }} Pelamar</span>
                    </div>
                    @if($position->deadline)
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Tenggat Lamaran</span>
                            <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($position->deadline)->format('d M Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recruitment Info Card -->
            <div class="bg-gradient-to-br from-slate-900 to-indigo-950 rounded-2xl p-6 text-white shadow-sm space-y-3">
                <div class="flex items-center gap-2 text-xs font-bold text-indigo-300">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Sistem Recruitment</span>
                </div>
                <h4 class="text-sm font-bold">Proses Seleksi Transparan</h4>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Setiap tahapan seleksi diproses secara bertahap mulai dari screening berkas, assessment online, wawancara teknis, hingga penawaran kerja.
                </p>
            </div>

        </div>

    </div>

    <!-- SECTION FOR ADMIN & HR: Applicants for this Position -->
    @if(in_array($userRole, ['admin', 'hr'], true))
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mt-8" id="applicants">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Daftar Pelamar untuk Posisi Ini</h3>
                    <p class="text-xs text-slate-500">Total {{ $position->applications->count() }} kandidat telah melamar posisi {{ $position->title }}</p>
                </div>
                @if($userRole === 'admin')
                    <a href="{{ route('admin.applications.index', ['position_id' => $position->id]) }}"
                       class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                        <span>Buka di Daftar Pelamar Penuh</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                            <th class="py-3 px-6">Kandidat</th>
                            <th class="py-3 px-4">Tanggal Lamar</th>
                            <th class="py-3 px-4 text-center">Tahapan Seleksi</th>
                            <th class="py-3 px-4 text-center">Kecocokan</th>
                            <th class="py-3 px-4 text-center">Dokumen CV</th>
                            <th class="py-3 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($position->applications as $app)
                            @php
                                $cvPath = $app->resume_url ?: $app->candidate?->profile?->resume_path;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-6">
                                    <div class="font-bold text-slate-900">{{ $app->candidate?->full_name }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $app->candidate?->email }} &bull; {{ $app->candidate?->phone_number ?? '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600">
                                    {{ $app->applied_at ? \Carbon\Carbon::parse($app->applied_at)->format('d M Y') : '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        {{ $app->stage }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-emerald-700">
                                    {{ $app->match_score ?? 85 }}%
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($cvPath)
                                        @php
                                            $cvLink = str_starts_with($cvPath, 'http') ? $cvPath : ($app->candidate ? route('documents.resume', ['candidate' => $app->candidate->id, 'view' => 1]) : '#');
                                        @endphp
                                        <a href="{{ $cvLink }}" target="_blank" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                            Unduh CV
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Belum upload</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-right">
                                    @if($userRole === 'admin')
                                        <a href="{{ route('admin.applications.show', $app->id) }}"
                                           class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 text-xs font-bold transition">
                                            Review
                                        </a>
                                    @else
                                        <a href="{{ route('hr.dashboard') }}#applications"
                                           class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-600 hover:text-white text-slate-700 text-xs font-bold transition">
                                            Review
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 px-4 text-center text-slate-400 italic">
                                    Belum ada pelamar yang mengajukan lamaran untuk posisi ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
