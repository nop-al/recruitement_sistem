@extends('layouts.dashboard')

@section('title', 'Manajemen Surat Penawaran Kerja (Offering)')

@section('content')
<div class="space-y-6">

    @php
        $routePrefix = auth()->user()?->role?->name === 'admin' ? 'admin' : 'hr';
    @endphp

    {{-- Header Banner --}}
    <div class="p-6 rounded-3xl bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 border border-slate-800 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold border border-emerald-500/30 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Modul Surat Penawaran &amp; Kompensasi</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-white">Surat Penawaran Kerja (Offering)</h1>
                <p class="text-xs text-slate-400 mt-1 max-w-2xl">
                    Kelola penerbitan offering letter bagi kandidat yang lolos seleksi: penetapan gaji, divisi kerja, tipe kontrak, tanggal mulai kerja, serta fasilitas/benefit.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route($routePrefix . '.offerings.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Terbitkan Offering Baru</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route($routePrefix . '.offerings.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-5">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari nama kandidat atau posisi..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="sm:col-span-3">
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Status Offering</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ $selectedStatus === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3">
                <select name="position_id" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Posisi</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ (string)$selectedPosition === (string)$pos->id ? 'selected' : '' }}>{{ $pos->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-1">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center justify-center">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Offerings Table Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($offerings->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Surat Penawaran</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Silakan buat offering letter baru untuk kandidat yang telah lolos tahapan wawancara.</p>
                <a href="{{ route($routePrefix . '.offerings.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-500">
                    + Terbitkan Offering Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Kandidat</th>
                            <th class="py-3 px-4">Posisi &amp; Divisi</th>
                            <th class="py-3 px-4">Gaji Ditawarkan</th>
                            <th class="py-3 px-4">Tipe &amp; Tgl Mulai</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($offerings as $off)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $off->candidate?->full_name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $off->candidate?->email }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-800">{{ $off->position?->title }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $off->department ?: $off->position?->department }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-slate-900 text-sm">
                                        Rp {{ number_format($off->effective_salary, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-semibold">
                                        {{ $off->employment_type ?: 'Full-time' }}
                                    </span>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        Mulai: {{ $off->effective_join_date ? \Carbon\Carbon::parse($off->effective_join_date)->format('d M Y') : '-' }}
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @php
                                        $badgeClass = match($off->status) {
                                            'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                                            'sent' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'accepted' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
                                            'expired' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            default => 'bg-slate-100 text-slate-700'
                                        };
                                        $displayStatus = \App\Models\Offering::STATUSES[$off->status] ?? ucfirst($off->status);
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeClass }}">
                                        {{ $displayStatus }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route($routePrefix . '.offerings.show', $off->id) }}"
                                           class="px-2.5 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold transition"
                                           title="Lihat Offering Letter">
                                            Surat Penawaran
                                        </a>

                                        <a href="{{ route($routePrefix . '.offerings.edit', $off->id) }}"
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-slate-100 transition"
                                           title="Edit Offering">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </a>

                                        <form method="POST" action="{{ route($routePrefix . '.offerings.destroy', $off->id) }}"
                                              onsubmit="return confirm('Hapus surat penawaran ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Offering">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($offerings->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $offerings->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
