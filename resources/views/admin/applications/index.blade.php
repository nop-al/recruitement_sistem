@extends('layouts.dashboard')

@section('title', 'Daftar Pelamar Kerja')
@section('role-label', 'Manajemen Pelamar')
@section('page-title', 'Daftar Seluruh Pelamar Kerja (Applications)')

@php
    $currentRole = auth()->user()?->role?->name;
    $prefix = match($currentRole) {
        'admin' => 'admin',
        'hr' => 'hr',
        default => 'interview',
    };
    $indexRoute = $prefix . '.applications.index';
    $showRoute = $prefix . '.applications.show';
    $interviewCreateRoute = $prefix . '.interviews.create';
    $exportRoute = $prefix . '.applications.export';
    $uploadMassRoute = $prefix . '.applications.upload-mass';
    $templateRoute = $prefix . '.applications.template';
@endphp

@section('content')
<div class="space-y-6">

    <!-- Header & Summary Cards -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Manajemen Seluruh Pelamar</h2>
            <p class="text-xs text-slate-500 mt-1">Pantau seluruh kandidat pelamar, berkas CV, skor kecocokan, dan kelola tahapan seleksi.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <!-- Tarik Data ke Excel (.xlsx) -->
            <a href="{{ route($exportRoute, request()->query()) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition"
               title="Unduh seluruh data pelamar aktif ke berkas Microsoft Excel (.xlsx)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Tarik Data ke Excel (.xlsx)</span>
            </a>

            @if(in_array($currentRole, ['admin', 'hr']))
                <!-- Upload Massal Status -->
                <button type="button" onclick="openUploadMassModal()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition"
                        title="Perbarui tahapan status pelamar secara massal menggunakan file Excel (.xlsx)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span>Upload Massal Status</span>
                </button>
            @endif

            <a href="{{ route($interviewCreateRoute) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-md transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <span>+ Jadwalkan Wawancara</span>
            </a>
            @if(in_array($currentRole, ['admin', 'hr']))
                <a href="{{ route($prefix . '.positions.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Buka Lowongan Baru</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Quick Stats Metric Pills -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-slate-500">Total Pelamar</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ $totalCount }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-blue-600">Screening Berkas</span>
            <div class="text-2xl font-black text-blue-700 mt-1">{{ $screeningCount }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-amber-600">Sesi Interview</span>
            <div class="text-2xl font-black text-amber-700 mt-1">{{ $interviewCount }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-semibold text-purple-600">Tahap Offering</span>
            <div class="text-2xl font-black text-purple-700 mt-1">{{ $offeringCount }}</div>
        </div>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col justify-between col-span-2 sm:col-span-1">
            <span class="text-[11px] font-semibold text-emerald-600">Lolos Diterima</span>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ $hiredCount }}</div>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
        <form method="GET" action="{{ route($indexRoute) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- Search Keyword -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Pelamar / Posisi</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $searchTerm }}" placeholder="Nama, email, telepon, atau posisi..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Position Filter -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Lowongan Pekerjaan</label>
                <select name="position_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <option value="">Semua Posisi ({{ $positions->count() }})</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ $selectedPosition == $pos->id ? 'selected' : '' }}>
                            {{ Str::limit($pos->title, 26) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Stage Filter -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tahapan Seleksi</label>
                <select name="stage" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <option value="">Semua Tahapan</option>
                    <option value="screening" {{ $selectedStage === 'screening' ? 'selected' : '' }}>1. Screening Berkas</option>
                    <option value="assessment" {{ $selectedStage === 'assessment' ? 'selected' : '' }}>2. Assessment Online</option>
                    <option value="interview" {{ $selectedStage === 'interview' ? 'selected' : '' }}>3. Wawancara (Interview)</option>
                    <option value="offering" {{ $selectedStage === 'offering' ? 'selected' : '' }}>4. Penawaran (Offering)</option>
                    <option value="onboarding" {{ $selectedStage === 'onboarding' ? 'selected' : '' }}>5. Onboarding</option>
                    <option value="hired" {{ $selectedStage === 'hired' ? 'selected' : '' }}>6. Diterima (Hired)</option>
                    <option value="rejected" {{ $selectedStage === 'rejected' ? 'selected' : '' }}>Gugur (Rejected)</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow transition text-center">
                    Filter Data
                </button>
                @if($searchTerm || $selectedPosition || $selectedStage || $selectedStatus)
                    <a href="{{ route($indexRoute) }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs transition" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Applications Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4 sm:px-6">Kandidat Pelamar</th>
                        <th class="py-3.5 px-4">Posisi Dilamar</th>
                        <th class="py-3.5 px-4">Tanggal Lamar</th>
                        <th class="py-3.5 px-4 text-center">Tahapan & Status</th>
                        <th class="py-3.5 px-4 text-center">Kecocokan</th>
                        <th class="py-3.5 px-4 text-center">Dokumen CV</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($applications as $app)
                        @php
                            $resumeFile = $app->resume_url ?: $app->candidate?->profile?->resume_path;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Candidate info -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-xs flex items-center justify-center shadow-inner shrink-0">
                                        {{ strtoupper(substr($app->candidate?->first_name ?? 'P', 0, 1) . substr($app->candidate?->last_name ?? 'L', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">
                                            {{ $app->candidate?->full_name ?? 'Tanpa Nama' }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">
                                            {{ $app->candidate?->email }} &bull; {{ $app->candidate?->phone_number ?? '-' }}
                                        </div>
                                        @if($app->candidate?->profile?->city)
                                            <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                                <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <span>{{ $app->candidate->profile->city }}, {{ $app->candidate->profile->province }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Position info -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-800">
                                    {{ $app->position?->title ?? 'Posisi Tidak Ditemukan' }}
                                </div>
                                <div class="text-[11px] text-indigo-600 font-medium mt-0.5">
                                    {{ $app->position?->department ?? 'Umum' }}
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $app->position?->location }} &bull; {{ $app->position?->type }}
                                </div>
                            </td>

                            <!-- Applied At -->
                            <td class="py-4 px-4 text-slate-600">
                                <div class="font-semibold">{{ $app->applied_at ? \Carbon\Carbon::parse($app->applied_at)->format('d M Y') : '-' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $app->applied_at ? \Carbon\Carbon::parse($app->applied_at)->diffForHumans() : '' }}</div>
                            </td>

                            <!-- Stage & Status Badge -->
                            <td class="py-4 px-4 text-center">
                                @php
                                    $stageColors = [
                                        'screening' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'assessment' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                        'interview' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'offering' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'onboarding' => 'bg-teal-50 text-teal-700 border-teal-200',
                                        'hired' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    ];
                                    $stageColor = $stageColors[$app->stage] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold border uppercase tracking-wider {{ $stageColor }}">
                                    {{ $app->stage }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-1 capitalize">
                                    Status: {{ str_replace('_', ' ', $app->status) }}
                                </div>
                            </td>

                            <!-- Match Score -->
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold
                                    {{ $app->match_score >= 90 ? 'bg-emerald-100 text-emerald-800' : ($app->match_score >= 80 ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $app->match_score ?? 80 }}%
                                </span>
                            </td>

                            <!-- Resume / CV Download -->
                            <td class="py-4 px-4 text-center">
                                @if($resumeFile)
                                    @php
                                        $isUrl = str_starts_with($resumeFile, 'http');
                                        $fileLink = $isUrl ? $resumeFile : ($app->candidate ? route('documents.resume', ['candidate' => $app->candidate->id, 'view' => 1]) : '#');
                                    @endphp
                                    <a href="{{ $fileLink }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold border border-indigo-200 transition"
                                       title="Lihat atau unduh CV pelamar">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span>Unduh CV</span>
                                    </a>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Belum upload CV</span>
                                @endif
                            </td>

                            <!-- Contextual Stage Action & Detail -->
                            <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($app->stage === 'screening')
                                        <a href="{{ route($showRoute, $app->id) }}"
                                           title="Periksa CV dan berkas pelamar"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 text-xs font-bold transition">
                                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span>Review CV</span>
                                        </a>
                                    @elseif($app->stage === 'assessment')
                                        <a href="{{ route($showRoute, $app->id) }}"
                                           title="Lihat hasil dan nilai tes online"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-800 border border-indigo-200 text-xs font-bold transition">
                                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <span>Cek Nilai</span>
                                        </a>
                                    @elseif($app->stage === 'interview')
                                        <a href="{{ route($interviewCreateRoute, ['candidate_id' => $app->candidate_id, 'position_id' => $app->position_id]) }}"
                                           title="Jadwalkan sesi wawancara online untuk kandidat ini"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 text-xs font-bold transition">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                            <span>+ Wawancara</span>
                                        </a>
                                    @elseif($app->stage === 'offering' && in_array($currentRole, ['admin', 'hr'], true))
                                        <a href="{{ route($prefix . '.offerings.create', ['candidate_id' => $app->candidate_id, 'position_id' => $app->position_id, 'application_id' => $app->id]) }}"
                                           title="Buat penawaran kerja (Offering Letter)"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-800 border border-purple-200 text-xs font-bold transition">
                                            <span>+ Offering</span>
                                        </a>
                                    @elseif($app->stage === 'onboarding' && in_array($currentRole, ['admin', 'hr'], true))
                                        <a href="{{ route($prefix . '.onboardings.index') }}"
                                           title="Kelola berkas onboarding karyawan"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 text-xs font-bold transition">
                                            <span>Onboarding</span>
                                        </a>
                                    @endif

                                    <a href="{{ route($showRoute, $app->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-indigo-600 hover:text-white text-slate-700 text-xs font-bold transition">
                                        <span>Detail</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800">Tidak ada data pelamar yang sesuai</h3>
                                <p class="text-xs text-slate-500 mt-1">Coba sesuaikan filter pencarian atau pastikan sudah ada kandidat yang mengajukan lamaran.</p>
                                @if($searchTerm || $selectedPosition || $selectedStage || $selectedStatus)
                                    <a href="{{ route($indexRoute) }}" class="inline-block mt-3 px-3.5 py-1.5 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100 transition">
                                        Reset Filter
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($applications->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $applications->links() }}
            </div>
        @endif
    </div>

</div>

@if(in_array($currentRole, ['admin', 'hr']))
<!-- Modal Upload Massal Status Pelamar -->
<div id="uploadMassModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden items-center justify-center p-4 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Click backdrop outside to close -->
    <div class="fixed inset-0 -z-10" onclick="closeUploadMassModal()"></div>

    <!-- Modal Dialog -->
    <div class="relative z-10 bg-white rounded-3xl max-w-lg w-full text-left overflow-hidden shadow-2xl border border-slate-100 my-auto">
        <form action="{{ route($uploadMassRoute) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 sm:p-7">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900" id="modal-title">Upload Massal Status Pelamar</h3>
                            <p class="text-xs text-slate-500">Perbarui tahapan status pelamar secara massal dengan Excel</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeUploadMassModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mt-5 space-y-4">
                    <!-- Petunjuk dan Download Template -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="text-xs font-bold text-slate-800">Petunjuk Format Berkas:</div>
                                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                                    Gunakan berkas Excel (.xlsx) dengan kolom: <code class="px-1 py-0.5 bg-white border border-slate-200 rounded text-slate-800 font-mono font-bold">no</code>, <code class="px-1 py-0.5 bg-white border border-slate-200 rounded text-slate-800 font-mono font-bold">email</code>, dan <code class="px-1 py-0.5 bg-white border border-slate-200 rounded text-slate-800 font-mono font-bold">tahapan</code>.
                                </p>
                            </div>
                            <a href="{{ route($templateRoute) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shrink-0 shadow-xs transition"
                               title="Unduh berkas template upload_mass_status.xlsx">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span>Unduh Template</span>
                            </a>
                        </div>

                        <div class="mt-3 pt-3 border-t border-slate-200/60">
                            <div class="text-[11px] font-bold text-slate-700 mb-1.5">Kode Angka / Nama Tahapan:</div>
                            <div class="grid grid-cols-2 gap-1.5 text-[11px] text-slate-600">
                                <div><span class="font-bold text-blue-600">1</span> = Screening</div>
                                <div><span class="font-bold text-blue-600">2</span> = Assessment</div>
                                <div><span class="font-bold text-blue-600">3</span> = Interview</div>
                                <div><span class="font-bold text-blue-600">4</span> = Offering</div>
                                <div><span class="font-bold text-blue-600">5</span> = Onboarding</div>
                                <div><span class="font-bold text-emerald-600">6</span> = Hired (Lolos)</div>
                                <div><span class="font-bold text-rose-600">7</span> = Rejected (Gagal)</div>
                            </div>
                        </div>
                    </div>

                    <!-- Input File Upload -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            Pilih Berkas Excel (.xlsx) <span class="text-rose-500">*</span>
                        </label>
                        <input type="file"
                               name="file_excel"
                               id="file_excel"
                               accept=".xlsx,.xls"
                               required
                               class="block w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-2xl p-2 cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        <p class="text-[11px] text-slate-400 mt-1.5">Maksimal ukuran file: 10 MB.</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeUploadMassModal()" class="px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition">
                    Batal
                </button>
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-md shadow-blue-600/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Proses & Perbarui Status</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUploadMassModal() {
        const modal = document.getElementById('uploadMassModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeUploadMassModal() {
        const modal = document.getElementById('uploadMassModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeUploadMassModal();
        }
    });
</script>
@endif
@endsection
