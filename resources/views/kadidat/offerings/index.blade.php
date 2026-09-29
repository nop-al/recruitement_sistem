@extends('layouts.dashboard')

@section('title', 'Surat Penawaran Kerja (Offering Letter)')

@section('content')
<div class="space-y-6">

    {{-- Hero Header --}}
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-emerald-900 via-teal-950 to-slate-900 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-3xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold border border-emerald-400/30 mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Tahap Penawaran Kerja (Job Offering)
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Surat Penawaran Kerja Resmi</h1>
            <p class="text-xs sm:text-sm text-emerald-100/80 mt-1.5 leading-relaxed">
                Tinjau rincian gaji, posisi jabatan, departemen penempatan, tanggal mulai kerja, dan paket benefit. Anda dapat memberikan konfirmasi penerimaan untuk langsung melanjutkan ke tahap Onboarding.
            </p>
        </div>
    </div>

    {{-- Offerings List --}}
    <div class="space-y-4">
        @if($offerings->isEmpty())
            <div class="p-12 rounded-3xl bg-white border border-slate-200 text-center text-xs text-slate-500 shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Penawaran Kerja Aktif</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                    Jika Anda telah menyelesaikan wawancara akhir dan dinyatakan lolos, tim rekrutmen akan menerbitkan surat penawaran kerja resmi di halaman ini.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($offerings as $off)
                    @php
                        $badgeClass = match($off->status) {
                            'accepted' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'rejected' => 'bg-rose-100 text-rose-800 border-rose-200',
                            'sent' => 'bg-blue-100 text-blue-800 border-blue-200 animate-pulse',
                            default => 'bg-slate-100 text-slate-700 border-slate-200'
                        };
                        $statusLabel = \App\Models\Offering::STATUSES[$off->status] ?? ucfirst($off->status);
                    @endphp

                    <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                                <span class="text-[11px] font-mono text-slate-400">
                                    #OFF-{{ str_pad($off->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>

                            <div>
                                <h3 class="text-base font-black text-slate-900">{{ $off->position?->title }}</h3>
                                <p class="text-xs text-slate-500">{{ $off->department ?: $off->position?->department }} &bull; {{ $off->employment_type ?: 'Full-time' }}</p>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500 font-medium">Gaji Ditawarkan:</span>
                                    <span class="text-base font-black text-emerald-600">
                                        Rp {{ number_format($off->effective_salary, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">/ bln</span>
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-xs pt-1.5 border-t border-slate-200/60">
                                    <span class="text-slate-500 font-medium">Tanggal Mulai (Join Date):</span>
                                    <span class="font-bold text-slate-800">
                                        {{ $off->effective_join_date ? \Carbon\Carbon::parse($off->effective_join_date)->translatedFormat('d F Y') : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">
                                Diterbitkan: {{ $off->created_at->format('d M Y') }}
                            </span>

                            <a href="{{ route('kadidat.offerings.show', $off->id) }}"
                               class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition flex items-center gap-1.5">
                                <span>Buka Surat Penawaran</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
