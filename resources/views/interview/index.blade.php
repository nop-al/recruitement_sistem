@extends('layouts.dashboard')

@section('title', 'Daftar Sesi Wawancara Online')
@section('role-label', 'Manajemen Wawancara Online')
@section('page-title', 'Sesi Wawancara & Ruang Virtual')

@php
    $currentRole = auth()->user()?->role?->name;
    $prefix = match($currentRole) {
        'admin' => 'admin',
        'hr' => 'hr',
        default => 'interview',
    };
    $createRoute = $prefix . '.interviews.create';
    $showRoute = $prefix . '.interviews.show';
    $destroyRoute = $prefix . '.interviews.destroy';
    $indexRoute = $prefix . '.interviews.index';
@endphp

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar & Header Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-slate-900 via-stone-900 to-amber-950 p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-semibold mb-2.5 border border-amber-400/30">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span>Ruang Wawancara Virtual</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Manajemen Sesi Wawancara Online</h2>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-2xl leading-relaxed">
                    Kelola jadwal evaluasi, atur tautan unik Zoom atau Google Meet per sesi, dan berikan penilaian kompetensi kandidat secara terpusat.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route($createRoute) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Jadwalkan Wawancara Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Total Sesi</span>
                <span class="text-2xl font-bold text-slate-900 mt-1 block">{{ $totalAssigned }}</span>
                <span class="text-[10px] text-slate-500">Semua riwayat jadwal</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Akan Datang (Terjadwal)</span>
                <span class="text-2xl font-bold text-amber-600 mt-1 block">{{ $totalScheduled }}</span>
                <span class="text-[10px] text-amber-600 font-medium">Perlu dihadiri</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Selesai Dievaluasi</span>
                <span class="text-2xl font-bold text-emerald-600 mt-1 block">{{ $totalCompleted }}</span>
                <span class="text-[10px] text-emerald-600 font-medium">Nilai tersimpan</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider block">Rata-rata Nilai</span>
                <span class="text-2xl font-bold text-indigo-600 mt-1 block">{{ $avgScore }}<span class="text-xs text-slate-400 font-normal"> / 100</span></span>
                <span class="text-[10px] text-slate-500">Standar lulus: 80</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Search & Filter Filter Form -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
        <form method="GET" action="{{ route($indexRoute) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Keyword search -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Cari Kandidat / Posisi</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $searchTerm }}"
                           placeholder="Ketik nama kandidat, email, atau posisi..."
                           class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Status filter -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Status Sesi</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    <option value="">Semua Status</option>
                    <option value="scheduled" {{ $selectedStatus === 'scheduled' ? 'selected' : '' }}>Terjadwal (Scheduled)</option>
                    <option value="completed" {{ $selectedStatus === 'completed' ? 'selected' : '' }}>Selesai Dinilai (Completed)</option>
                    <option value="cancelled" {{ $selectedStatus === 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                </select>
            </div>

            <!-- Platform filter -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Platform Virtual</label>
                <select name="platform" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                    <option value="">Semua Format</option>
                    <option value="meet" {{ $selectedPlatform === 'meet' ? 'selected' : '' }}>Google Meet</option>
                    <option value="zoom" {{ $selectedPlatform === 'zoom' ? 'selected' : '' }}>Zoom Meeting</option>
                    <option value="offline" {{ $selectedPlatform === 'offline' ? 'selected' : '' }}>Offline (Tatap Muka)</option>
                </select>
            </div>

            <!-- Submit buttons -->
            <div class="flex items-end gap-2">
                <button type="submit"
                        class="flex-1 py-2 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow transition flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <span>Filter</span>
                </button>
                @if($searchTerm || $selectedStatus || $selectedPlatform || $selectedPosition)
                    <a href="{{ route($indexRoute) }}"
                       class="py-2 px-3 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-100 text-xs font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Interviews List Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4 sm:px-6">Kandidat & Posisi</th>
                        <th class="py-3.5 px-4">Jadwal Pelaksanaan</th>
                        <th class="py-3.5 px-4">Pewawancara</th>
                        <th class="py-3.5 px-4">Ruang Virtual (Link Sesi)</th>
                        <th class="py-3.5 px-4 text-center">Status / Nilai</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($interviews as $interview)
                        @php
                            $isZoom = str_contains($interview->room_link ?? '', 'zoom');
                            $isMeet = str_contains($interview->room_link ?? '', 'meet.google');
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- Candidate & Position -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-amber-500 to-stone-700 text-white font-bold text-xs flex items-center justify-center shadow-inner shrink-0">
                                        {{ strtoupper(substr($interview->candidate?->first_name ?? 'K', 0, 1) . substr($interview->candidate?->last_name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">
                                            {{ $interview->candidate?->full_name ?? 'Kandidat' }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">
                                            {{ $interview->position?->title ?? '-' }} &bull; <span class="text-indigo-600 font-semibold">{{ $interview->interview_type }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Scheduled time -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-800">
                                    {{ $interview->scheduled_at ? $interview->scheduled_at->format('d M Y') : '-' }}
                                </div>
                                <div class="text-[11px] text-amber-700 font-bold mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>{{ $interview->scheduled_at ? $interview->scheduled_at->format('H:i') : '-' }} WIB</span>
                                </div>
                            </td>

                            <!-- Interviewer -->
                            <td class="py-4 px-4">
                                <div class="font-semibold text-slate-800">
                                    {{ $interview->interviewer?->name ?? 'User Interviewer' }}
                                </div>
                                <div class="text-[10px] text-slate-400 capitalize">
                                    {{ $interview->interviewer?->role?->name ?? 'Pewawancara' }}
                                </div>
                            </td>

                            <!-- Meeting link / Platform -->
                            <!-- Meeting link / Location -->
                            <td class="py-4 px-4">
                                @if($interview->interview_method === 'offline' || empty($interview->room_link))
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                            <svg class="w-3 h-3 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            Offline (Tatap Muka)
                                        </span>
                                        <div class="text-[11px] font-semibold text-slate-700 flex items-start gap-1 mt-0.5">
                                            <svg class="w-3.5 h-3.5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span class="line-clamp-2">{{ $interview->location ?? 'Kantor Pusat Perusahaan' }}</span>
                                        </div>
                                    </div>
                                @elseif($interview->room_link)
                                    <div class="space-y-1.5">
                                        <div class="flex items-center gap-1.5">
                                            @if($isZoom)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M4.5 5.5A2.5 2.5 0 002 8v8a2.5 2.5 0 002.5 2.5h9A2.5 2.5 0 0016 16v-1.5l4.5 3a1 1 0 001.5-.83V7.33a1 1 0 00-1.5-.83L16 9.5V8a2.5 2.5 0 00-2.5-2.5h-9z"></path>
                                                    </svg>
                                                    Zoom Meeting
                                                </span>
                                            @elseif($isMeet)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"></path>
                                                    </svg>
                                                    Google Meet
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                    Online Room
                                                </span>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ $interview->room_link }}" target="_blank"
                                               class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700 hover:text-amber-900 underline transition">
                                                <span>Buka Tautan Sesi</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- Status / Score -->
                            <td class="py-4 px-4 text-center">
                                @if($interview->status === 'completed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        Skor: {{ $interview->total_score ?? '-' }} / 100
                                    </span>
                                    <div class="text-[10px] font-bold uppercase mt-1 {{ $interview->recommendation === 'pass' ? 'text-emerald-700' : ($interview->recommendation === 'hold' ? 'text-amber-700' : 'text-rose-700') }}">
                                        Rekomendasi: {{ $interview->recommendation ?? '-' }}
                                    </div>
                                @elseif($interview->status === 'cancelled')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                                        Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                        Terjadwal
                                    </span>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Menunggu Evaluasi</div>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Scoring / View Detail button -->
                                    <a href="{{ route($showRoute, $interview->id) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-amber-500 hover:text-slate-950 text-slate-700 text-xs font-bold transition">
                                        <span>{{ $interview->status === 'completed' ? 'Lihat Hasil' : 'Beri Nilai' }}</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>

                                    <!-- Delete / Cancel button -->
                                    <form action="{{ route($destroyRoute, $interview->id) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus sesi wawancara ini?')"
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                title="Batalkan sesi wawancara"
                                                class="p-1.5 rounded-xl border border-slate-200 text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-4 text-center">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-sm font-bold text-slate-800">Tidak ada sesi wawancara yang cocok</h4>
                                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                    Belum ada sesi wawancara online yang dijadwalkan dengan filter ini. Silakan jadwalkan sesi baru.
                                </p>
                                <div class="mt-4">
                                    <a href="{{ route($createRoute) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        <span>Jadwalkan Wawancara Sekarang</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($interviews->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $interviews->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
