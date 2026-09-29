@extends('layouts.dashboard')

@section('title', 'Interviewer Dashboard')
@section('role-label', 'User & Technical Interviewer')
@section('page-title', 'Portal Wawancara & Evaluasi Kandidat')

@section('content')
<div class="space-y-8">

    <!-- Top Interviewer Welcome Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-amber-900 via-stone-900 to-slate-900 p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-semibold mb-3 border border-amber-400/30">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    User Interviewer Panel
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ $interviewer->name ?? 'Interviewer' }}!</h2>
                <p class="text-amber-100/80 text-sm mt-1 max-w-2xl leading-relaxed">
                    Kelola penilaian kapabilitas teknis dan kesesuaian tim kandidat pelamar. Terdapat <span class="font-bold text-white">{{ $upcomingInterviews->count() }} wawancara aktif</span> dan <span class="font-bold text-white">{{ $pendingEvaluations->count() }} sesi</span> menunggu input nilai Anda.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('interview.interviews.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Jadwalkan Wawancara Baru</span>
                </a>
                <a href="{{ route('interview.applications.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold border border-white/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Lihat Daftar Pelamar</span>
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Ditugaskan</span>
                <span class="p-2.5 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ $totalAssigned }}</span>
                <span class="text-xs font-medium text-slate-500">Sesi Kandidat</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Daftar dari HR Talent Acquisition</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Penilaian</span>
                <span class="p-2.5 rounded-xl bg-rose-50 text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-rose-600">{{ $pendingEvaluations->count() }}</span>
                <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">Perlu Aksi</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Segera input nilai setelah wawancara</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Selesai Dinilai</span>
                <span class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ $completedCount }}</span>
                <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Terkirim ke HR</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Rekomendasi telah terverifikasi</p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Rata-rata Skor</span>
                <span class="p-2.5 rounded-xl bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ $avgScore }}</span>
                <span class="text-xs font-medium text-slate-500">/ 100 poin</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Standar minimum lolos: 80 poin</p>
        </div>
    </div>

    <!-- Section: Jadwal Sesi Wawancara Mendatang (Active Schedule Cards) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">Jadwal Sesi Wawancara Mendatang</h3>
                <p class="text-xs text-slate-500">Informasi kandidat, tautan ruang virtual Zoom / Meet, dan lembar penilaian</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('interview.interviews.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    <span>Lihat Seluruh Jadwal</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div class="space-y-4 mt-5">
            @forelse($upcomingInterviews as $interview)
                @php
                    $isZoom = str_contains($interview->room_link ?? '', 'zoom');
                    $isMeet = str_contains($interview->room_link ?? '', 'meet.google');
                @endphp
                <div class="p-5 rounded-2xl border border-slate-200/80 bg-slate-50/40 hover:bg-white hover:border-amber-300 hover:shadow-md transition">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <!-- Left info -->
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 font-bold text-base flex items-center justify-center shrink-0 shadow-inner">
                                {{ strtoupper(substr($interview->candidate?->first_name ?? 'C', 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-sm font-bold text-slate-900">{{ $interview->candidate?->full_name }}</h4>
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">
                                        {{ $interview->position?->title }}
                                    </span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $isZoom ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $isZoom ? 'Zoom' : ($isMeet ? 'Google Meet' : 'Online Room') }}
                                    </span>
                                </div>

                                <p class="text-xs text-slate-500 mt-1">
                                    Tipe: <strong class="text-slate-700">{{ $interview->interview_type }}</strong> &bull;
                                    Waktu: <span class="text-amber-700 font-semibold">{{ $interview->scheduled_at ? $interview->scheduled_at->format('l, d M Y - H:i') : '-' }} WIB</span>
                                </p>

                                <!-- Candidate Skills Preview -->
                                <div class="flex items-center gap-1.5 flex-wrap mt-2.5">
                                    <span class="text-[10px] text-slate-400 font-semibold">Skills Terdata:</span>
                                    @foreach($interview->candidate?->skills ?? [] as $skill)
                                        <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200/60">
                                            {{ $skill->skill_name }} ({{ ucfirst($skill->proficiency_level) }})
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Right Actions -->
                        <div class="flex flex-wrap items-center gap-2.5 shrink-0 pt-3 lg:pt-0 border-t lg:border-t-0 border-slate-200/60">
                            @if($interview->room_link)
                                <a href="{{ $interview->room_link }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl {{ $isZoom ? 'bg-blue-600 hover:bg-blue-500' : 'bg-emerald-600 hover:bg-emerald-500' }} text-white text-xs font-bold shadow transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Masuk Ruang Virtual</span>
                                </a>
                            @endif

                            <a href="{{ route('interview.interviews.show', $interview->id) }}"
                               class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-sm transition">
                                <span>Buka Lembar Penilaian</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    @if($interview->notes)
                        <div class="mt-3 pt-3 border-t border-slate-200/50 text-xs text-slate-600 bg-amber-50/50 p-2.5 rounded-lg border border-amber-100">
                            <span class="font-bold text-amber-900">Fokus Interview:</span> {{ $interview->notes }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-8">
                    <p class="text-xs text-slate-400 mb-3">Tidak ada jadwal wawancara terdekat.</p>
                    <a href="{{ route('interview.interviews.create') }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-500 text-slate-950 text-xs font-bold hover:bg-amber-400 transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Jadwalkan Sekarang</span>
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Section: Past Evaluated Candidates History -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" id="history">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Riwayat Penilaian yang Telah Selesai</h3>
                <p class="text-xs text-slate-500">Rekap evaluasi dan keputusan yang telah Anda serahkan</p>
            </div>
            <a href="{{ route('interview.interviews.index', ['status' => 'completed']) }}"
               class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Kandidat</th>
                        <th class="px-6 py-3.5">Posisi</th>
                        <th class="px-6 py-3.5">Skor Teknis</th>
                        <th class="px-6 py-3.5">Total Skor</th>
                        <th class="px-6 py-3.5">Rekomendasi</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pastEvaluations as $eval)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $eval->candidate?->full_name }}</td>
                            <td class="px-6 py-4 font-medium text-slate-700">{{ $eval->position?->title }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $eval->technical_score ?? 90 }} / 100</td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                                    {{ $eval->total_score ?? 96 }} / 100
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold uppercase
                                    {{ $eval->recommendation === 'pass' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $eval->recommendation ?? 'pass' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('interview.interviews.show', $eval->id) }}"
                                   class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 font-bold text-xs">
                                    <span>Detail Nilai</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-slate-400">Belum ada riwayat penilaian tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
