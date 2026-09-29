@extends('layouts.dashboard')

@section('title', 'Detail Sesi Wawancara & Evaluasi')
@section('role-label', 'Manajemen Wawancara Online')
@section('page-title', 'Detail Wawancara & Lembar Penilaian')

@php
    $currentRole = auth()->user()?->role?->name;
    $prefix = match($currentRole) {
        'admin' => 'admin',
        'hr' => 'hr',
        default => 'interview',
    };
    $indexRoute = $prefix . '.interviews.index';
    $evaluateRoute = $prefix . '.interviews.evaluate';
    $resumeFile = $application?->resume_url ?: $candidate?->profile?->resume_path;

    $isZoom = str_contains($interview->room_link ?? '', 'zoom');
    $isMeet = str_contains($interview->room_link ?? '', 'meet.google');
@endphp

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Back -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route($indexRoute) }}"
               class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-extrabold text-slate-900">Sesi Wawancara: {{ $candidate?->full_name }}</h2>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full {{ $interview->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }} uppercase">
                        {{ $interview->status }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">{{ $interview->position?->title }} &bull; {{ $interview->interview_type }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($interview->room_link)
                <a href="{{ $interview->room_link }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl {{ $isZoom ? 'bg-blue-600 hover:bg-blue-500' : 'bg-emerald-600 hover:bg-emerald-500' }} text-white font-bold text-xs shadow-md transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span>Buka Ruang {{ $isZoom ? 'Zoom' : ($isMeet ? 'Google Meet' : 'Virtual') }}</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            @endif
        </div>
    </div>

    <!-- Room / Location Banner Card (Online vs Offline) -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @if($interview->interview_method === 'offline' || empty($interview->room_link))
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 shadow-inner">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Metode Wawancara</span>
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                Offline (Tatap Muka di Kantor)
                            </span>
                        </div>
                        <div class="text-base font-bold text-slate-900 mt-1 flex items-start gap-1.5">
                            <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ $interview->location ?? 'Kantor Pusat Perusahaan' }}</span>
                        </div>
                        <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                            <span>Waktu: <strong>{{ $interview->scheduled_at ? $interview->scheduled_at->format('l, d M Y - H:i') : '-' }} WIB</strong></span>
                            <span>Pewawancara: <strong>{{ $interview->interviewer?->name ?? '-' }}</strong></span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button onclick="navigator.clipboard.writeText('{{ addslashes($interview->location ?? 'Kantor Pusat') }}'); alert('Alamat lokasi disalin ke clipboard!');"
                            class="px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                        </svg>
                        <span>Salin Alamat Lokasi</span>
                    </button>
                </div>
            </div>
        @else
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 rounded-2xl {{ $isZoom ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }} flex items-center justify-center shrink-0 shadow-inner">
                        @if($isZoom)
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4.5 5.5A2.5 2.5 0 002 8v8a2.5 2.5 0 002.5 2.5h9A2.5 2.5 0 0016 16v-1.5l4.5 3a1 1 0 001.5-.83V7.33a1 1 0 00-1.5-.83L16 9.5V8a2.5 2.5 0 00-2.5-2.5h-9z"></path>
                            </svg>
                        @else
                            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"></path>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tautan Unik Ruang Sesi</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $isZoom ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $isZoom ? 'Zoom Meeting' : ($isMeet ? 'Google Meet' : 'Virtual Link') }}
                            </span>
                        </div>
                        <div class="text-sm font-mono font-bold text-slate-900 mt-1 break-all select-all">
                            {{ $interview->room_link ?? 'Belum ada link virtual' }}
                        </div>
                        <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-x-4 gap-y-1">
                            <span>Waktu: <strong>{{ $interview->scheduled_at ? $interview->scheduled_at->format('l, d M Y - H:i') : '-' }} WIB</strong></span>
                            <span>Pewawancara: <strong>{{ $interview->interviewer?->name ?? '-' }}</strong></span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button onclick="navigator.clipboard.writeText('{{ $interview->room_link }}'); alert('Tautan meeting disalin ke clipboard!');"
                            class="px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                        </svg>
                        <span>Salin Link</span>
                    </button>
                    @if($interview->room_link)
                        <a href="{{ $interview->room_link }}" target="_blank"
                           class="px-4 py-2 rounded-xl {{ $isZoom ? 'bg-blue-600 hover:bg-blue-500' : 'bg-emerald-600 hover:bg-emerald-500' }} text-white text-xs font-bold shadow transition flex items-center gap-1.5">
                            <span>Gabung Sekarang</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        @endif

        @if($interview->notes)
            <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-600 bg-amber-50/60 p-3 rounded-xl border border-amber-200/60">
                <strong class="text-amber-900">Catatan & Fokus Pertanyaan:</strong>
                <p class="mt-0.5 leading-relaxed">{{ $interview->notes }}</p>
            </div>
        @endif
    </div>

    <!-- 2 Columns Grid: Candidate Info & Scoring Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column (1/3): Candidate Profile & Documents -->
        <div class="space-y-6">

            <!-- Candidate Identity Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center gap-3.5">
                    @if($candidate?->profile?->photo)
                        <img src="{{ asset('storage/' . $candidate->profile->photo) }}" alt="{{ $candidate->full_name }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-indigo-500/20 shadow">
                    @else
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-lg font-black shadow-inner shrink-0">
                            {{ strtoupper(substr($candidate?->first_name ?? 'K', 0, 1) . substr($candidate?->last_name ?? 'P', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <h3 class="text-base font-bold text-slate-900">{{ $candidate?->full_name }}</h3>
                        <p class="text-xs text-slate-500">{{ $candidate?->email }}</p>
                        <p class="text-xs text-slate-500">{{ $candidate?->phone_number ?? '-' }}</p>
                    </div>
                </div>

                @if($candidate?->profile?->city)
                    <div class="text-xs text-slate-600 flex items-center gap-1.5 pt-2 border-t border-slate-100">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $candidate->profile->city }}, {{ $candidate->profile->province }}</span>
                    </div>
                @endif

                <!-- Resume Download Box -->
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Berkas CV Kandidat</span>
                    @if($resumeFile)
                        @php
                            $isUrl = str_starts_with($resumeFile, 'http');
                            $fileLink = $isUrl ? $resumeFile : ($candidate ? route('documents.resume', ['candidate' => $candidate->id, 'view' => 1]) : '#');
                        @endphp
                        <a href="{{ $fileLink }}" target="_blank"
                           class="flex items-center justify-between p-3 rounded-xl border border-indigo-200 bg-indigo-50/50 hover:bg-indigo-100/60 text-indigo-800 transition">
                            <div class="flex items-center gap-2 truncate">
                                <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-xs font-bold truncate">Unduh / Buka Resume CV</span>
                            </div>
                            <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    @else
                        <div class="p-3 rounded-xl border border-dashed border-slate-200 text-center text-xs text-slate-400">
                            Kandidat belum mengunggah CV.
                        </div>
                    @endif
                </div>

                <!-- Skills Tag List -->
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Keahlian Terdata</span>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($candidate?->skills ?? [] as $skill)
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $skill->skill_name }}
                            </span>
                        @empty
                            <span class="text-xs text-slate-400 italic">Belum ada data keahlian.</span>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column (2/3): Scoring & Evaluation Form -->
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-7">
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Formulir Penilaian & Evaluasi Kompetensi</h3>
                        <p class="text-xs text-slate-500">Input skor penilaian wawancara, catatan objektif, dan rekomendasi hasil</p>
                    </div>
                    @if($interview->status === 'completed')
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block font-semibold">Total Nilai Saat Ini</span>
                            <span class="text-xl font-extrabold text-emerald-600">{{ $interview->total_score ?? '-' }} / 100</span>
                        </div>
                    @endif
                </div>

                <form action="{{ route($evaluateRoute, $interview->id) }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- 3 Metric Sliders / Number Inputs -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Score 1: Technical -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <label class="block text-xs font-bold text-slate-800">
                                Keahlian Teknis <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" min="0" max="100" name="technical_score" id="technical_score" required
                                       oninput="calculateAverageScore()"
                                       value="{{ old('technical_score', $interview->technical_score ?? 85) }}"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-300 text-base font-bold text-slate-900 bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                                <span class="text-xs font-semibold text-slate-400">/ 100</span>
                            </div>
                            <span class="text-[10px] text-slate-500 block leading-tight">Pemecahan masalah, arsitektur, & logika teknis</span>
                        </div>

                        <!-- Score 2: Communication -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <label class="block text-xs font-bold text-slate-800">
                                Komunikasi & Sikap <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" min="0" max="100" name="communication_score" id="communication_score" required
                                       oninput="calculateAverageScore()"
                                       value="{{ old('communication_score', $interview->communication_score ?? 85) }}"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-300 text-base font-bold text-slate-900 bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                                <span class="text-xs font-semibold text-slate-400">/ 100</span>
                            </div>
                            <span class="text-[10px] text-slate-500 block leading-tight">Artikulasi, mendengarkan, & penyampaian argumen</span>
                        </div>

                        <!-- Score 3: Culture Fit -->
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                            <label class="block text-xs font-bold text-slate-800">
                                Kesesuaian Budaya <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" min="0" max="100" name="culture_score" id="culture_score" required
                                       oninput="calculateAverageScore()"
                                       value="{{ old('culture_score', $interview->culture_score ?? 85) }}"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-300 text-base font-bold text-slate-900 bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                                <span class="text-xs font-semibold text-slate-400">/ 100</span>
                            </div>
                            <span class="text-[10px] text-slate-500 block leading-tight">Nilai organisasi, kerja tim, & adaptabilitas</span>
                        </div>
                    </div>

                    <!-- Live Calculated Average Score Display -->
                    <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs font-bold text-indigo-900">Rata-rata Skor Keseluruhan:</span>
                        </div>
                        <div class="text-base font-black text-indigo-700" id="averageScoreDisplay">
                            {{ $interview->total_score ?? 85 }} / 100
                        </div>
                    </div>

                    <!-- Recommendation Radios -->
                    <div>
                        <label class="block text-xs font-bold text-slate-800 mb-2">
                            Rekomendasi Hasil Wawancara <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-emerald-500 transition">
                                <input type="radio" name="recommendation" value="pass"
                                       {{ old('recommendation', $interview->recommendation ?? 'pass') === 'pass' ? 'checked' : '' }}
                                       class="text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <div class="text-xs font-bold text-emerald-700">Lolos (Pass)</div>
                                    <div class="text-[10px] text-slate-500">Maju ke offering</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-amber-500 transition">
                                <input type="radio" name="recommendation" value="hold"
                                       {{ old('recommendation', $interview->recommendation) === 'hold' ? 'checked' : '' }}
                                       class="text-amber-600 focus:ring-amber-500">
                                <div>
                                    <div class="text-xs font-bold text-amber-700">Pertimbangkan (Hold)</div>
                                    <div class="text-[10px] text-slate-500">Kandidat cadangan</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-rose-500 transition">
                                <input type="radio" name="recommendation" value="fail"
                                       {{ old('recommendation', $interview->recommendation) === 'fail' ? 'checked' : '' }}
                                       class="text-rose-600 focus:ring-rose-500">
                                <div>
                                    <div class="text-xs font-bold text-rose-700">Tidak Lolos (Fail)</div>
                                    <div class="text-[10px] text-slate-500">Belum sesuai kriteria</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Evaluation Notes -->
                    <div>
                        <label for="eval_notes" class="block text-xs font-bold text-slate-800 mb-1.5">
                            Catatan Evaluasi Mendalam & Feedback untuk HR
                        </label>
                        <textarea name="notes" id="eval_notes" rows="4"
                                  placeholder="Tuliskan catatan teknis kekuatan, kekurangan, dan alasan rekomendasi Anda..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">{{ old('notes', $interview->notes) }}</textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route($indexRoute) }}"
                           class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-xs transition">
                            Kembali
                        </a>
                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/30 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Simpan Hasil Penilaian Wawancara</span>
                        </button>
                    </div>

                </form>
            </div>

        </div>

    </div>

</div>

<script>
    function calculateAverageScore() {
        const tech = parseInt(document.getElementById('technical_score').value) || 0;
        const comm = parseInt(document.getElementById('communication_score').value) || 0;
        const cult = parseInt(document.getElementById('culture_score').value) || 0;

        const avg = Math.round((tech + comm + cult) / 3);
        document.getElementById('averageScoreDisplay').innerText = `${avg} / 100`;
    }
</script>
@endsection
