@extends('layouts.dashboard')

@section('title', 'Manajemen Onboarding Karyawan Baru')

@section('content')
<div class="space-y-6">

    @php
        $routePrefix = auth()->user()?->role?->name === 'admin' ? 'admin' : 'hr';
    @endphp

    {{-- Header Banner --}}
    <div class="p-6 rounded-3xl bg-gradient-to-r from-slate-900 via-cyan-950 to-slate-900 border border-slate-800 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 text-xs font-semibold border border-cyan-500/30 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Modul Onboarding Karyawan Baru</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-white">Onboarding &amp; Verifikasi Berkas</h1>
                <p class="text-xs text-slate-400 mt-1 max-w-2xl">
                    Pantau pemenuhan berkas 8 checklist (KTP, NPWP, BPJS, Rekening Bank, Ijazah, Pas Foto, Kontrak, Emergency Contact) untuk kandidat yang menerima offering.
                </p>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route($routePrefix . '.onboardings.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-7">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari nama karyawan onboarding atau posisi..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="sm:col-span-4">
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    <option value="">Semua Status Onboarding</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ $selectedStatus === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-1">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs transition flex items-center justify-center">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Onboarding Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($onboardings->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-cyan-50 text-cyan-500 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Karyawan dalam Tahap Onboarding</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                    Kandidat yang telah menerima penawaran kerja (offering) akan otomatis masuk ke daftar proses onboarding ini.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Calon Karyawan</th>
                            <th class="py-3 px-4">Posisi &amp; Divisi</th>
                            <th class="py-3 px-4">Tanggal Mulai</th>
                            <th class="py-3 px-4">Kelengkapan Checklist</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($onboardings as $onb)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $onb->candidate?->full_name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $onb->candidate?->email }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-slate-800">{{ $onb->position?->title }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $onb->position?->department }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 font-medium">
                                    {{ $onb->join_date ? \Carbon\Carbon::parse($onb->join_date)->format('d M Y') : ($onb->start_date ? \Carbon\Carbon::parse($onb->start_date)->format('d M Y') : '-') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="w-48 space-y-1">
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="font-bold text-slate-800">{{ $onb->checklist_percentage }}% Lengkap</span>
                                            <span class="text-[10px] text-slate-400">8 Dokumen</span>
                                        </div>
                                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full transition-all duration-300
                                                @if($onb->checklist_percentage >= 100) bg-emerald-500
                                                @elseif($onb->checklist_percentage >= 50) bg-cyan-500
                                                @else bg-amber-500 @endif"
                                                style="width: {{ $onb->checklist_percentage }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @php
                                        $badgeClass = match($onb->status) {
                                            'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            'in_progress' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                            default => 'bg-amber-100 text-amber-800 border-amber-200'
                                        };
                                        $label = \App\Models\Onboarding::STATUSES[$onb->status] ?? ucfirst($onb->status);
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeClass }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route($routePrefix . '.onboardings.show', $onb->id) }}"
                                       class="px-3 py-1.5 rounded-xl bg-cyan-50 text-cyan-700 hover:bg-cyan-100 text-xs font-bold transition inline-flex items-center gap-1.5">
                                        <span>Verifikasi Berkas</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($onboardings->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $onboardings->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
