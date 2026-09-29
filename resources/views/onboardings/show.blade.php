@extends('layouts.dashboard')

@section('title', 'Verifikasi Dokumen Onboarding - ' . $onboarding->candidate?->full_name)

@section('content')
<div class="space-y-6">

    @php
        $routePrefix = auth()->user()?->role?->name === 'admin' ? 'admin' : 'hr';
    @endphp

    {{-- Breadcrumbs & Back --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route($routePrefix . '.onboardings.index') }}" class="hover:text-cyan-600 transition">&larr; Kembali ke Daftar Onboarding</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">{{ $onboarding->candidate?->full_name }}</span>
        </div>

        <div class="flex items-center gap-2">
            @if($onboarding->status !== 'completed')
                <form method="POST" action="{{ route($routePrefix . '.onboardings.status.update', $onboarding->id) }}"
                      onsubmit="return confirm('Selesaikan proses onboarding dan resmi angkat kandidat sebagai Karyawan (Hired)?');">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Selesaikan Onboarding (Resmi Hired)</span>
                    </button>
                </form>
            @else
                <span class="px-4 py-2 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Karyawan Resmi Bergabung (Hired)</span>
                </span>
            @endif
        </div>
    </div>

    {{-- Candidate & Onboarding Progress Header Card --}}
    <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-cyan-600 text-white font-black text-xl flex items-center justify-center shrink-0 shadow-lg shadow-cyan-600/20">
                    {{ strtoupper(substr($onboarding->candidate?->full_name ?? 'OK', 0, 2)) }}
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-700 bg-cyan-50 px-2.5 py-0.5 rounded-full border border-cyan-200">
                        Proses Onboarding Karyawan
                    </span>
                    <h1 class="text-xl font-black text-slate-900 mt-1">{{ $onboarding->candidate?->full_name }}</h1>
                    <p class="text-xs text-slate-500">{{ $onboarding->position?->title }} &bull; {{ $onboarding->position?->department }}</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Tanggal Mulai Kerja: <strong class="text-slate-700">{{ $onboarding->join_date ? \Carbon\Carbon::parse($onboarding->join_date)->translatedFormat('d F Y') : '-' }}</strong>
                    </p>
                </div>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 min-w-[280px] space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-700">Kelengkapan 8 Dokumen:</span>
                    <span class="font-black text-cyan-600 text-sm">{{ $onboarding->checklist_percentage }}%</span>
                </div>
                <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 rounded-full transition-all duration-300
                        @if($onboarding->checklist_percentage >= 100) bg-emerald-500
                        @elseif($onboarding->checklist_percentage >= 50) bg-cyan-500
                        @else bg-amber-500 @endif"
                        style="width: {{ $onboarding->checklist_percentage }}%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-400 pt-1">
                    <span>Status: <strong class="uppercase text-slate-700">{{ $onboarding->status }}</strong></span>
                    @if($onboarding->completed_at)
                        <span>Selesai: {{ $onboarding->completed_at->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 8 Checklist Verification Cards Grid --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-black text-slate-900">8 Dokumen &amp; Data Checklist Onboarding</h2>
            <span class="text-xs text-slate-400">Verifikasi berkas yang telah dikirimkan oleh kandidat</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- 1. KTP --}}
            <div class="p-5 rounded-2xl bg-white border {{ $onboarding->ktp_verified ? 'border-emerald-200' : 'border-slate-200' }} shadow-sm flex flex-col justify-between space-y-3">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center text-[10px]">1</span>
                            Kartu Tanda Penduduk (KTP)
                        </span>
                        @if($onboarding->ktp_verified)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi</span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">Belum Verifikasi</span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-600 space-y-1">
                        <p>Nomor KTP: <strong class="text-slate-900 font-mono">{{ $onboarding->ktp_number ?: 'Belum diisi' }}</strong></p>
                        @if($onboarding->ktp_file)
                            <a href="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'ktp', 'view' => 1]) }}" target="_blank" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                                <span>Lihat Berkas KTP</span> &rarr;
                            </a>
                        @else
                            <span class="text-slate-400 text-[11px]">Berkas belum diunggah</span>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route($routePrefix . '.onboardings.verify', $onboarding->id) }}" class="pt-2 border-t border-slate-100 flex justify-end">
                    @csrf
                    <input type="hidden" name="item" value="ktp">
                    <input type="hidden" name="verified" value="{{ $onboarding->ktp_verified ? '0' : '1' }}">
                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $onboarding->ktp_verified ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}">
                        {{ $onboarding->ktp_verified ? 'Batalkan Verifikasi' : 'Verifikasi KTP' }}
                    </button>
                </form>
            </div>

            {{-- 2. NPWP --}}
            <div class="p-5 rounded-2xl bg-white border {{ $onboarding->npwp_verified ? 'border-emerald-200' : 'border-slate-200' }} shadow-sm flex flex-col justify-between space-y-3">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center text-[10px]">2</span>
                            NPWP (Nomor Pokok Wajib Pajak)
                        </span>
                        @if($onboarding->npwp_verified)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi</span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">Belum Verifikasi</span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-600 space-y-1">
                        <p>Nomor NPWP: <strong class="text-slate-900 font-mono">{{ $onboarding->npwp_number ?: 'Belum diisi' }}</strong></p>
                        @if($onboarding->npwp_file)
                            <a href="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'npwp', 'view' => 1]) }}" target="_blank" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                                <span>Lihat Berkas NPWP</span> &rarr;
                            </a>
                        @else
                            <span class="text-slate-400 text-[11px]">Berkas belum diunggah</span>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route($routePrefix . '.onboardings.verify', $onboarding->id) }}" class="pt-2 border-t border-slate-100 flex justify-end">
                    @csrf
                    <input type="hidden" name="item" value="npwp">
                    <input type="hidden" name="verified" value="{{ $onboarding->npwp_verified ? '0' : '1' }}">
                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $onboarding->npwp_verified ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}">
                        {{ $onboarding->npwp_verified ? 'Batalkan Verifikasi' : 'Verifikasi NPWP' }}
                    </button>
                </form>
            </div>

            {{-- 3. BPJS --}}
            <div class="p-5 rounded-2xl bg-white border {{ $onboarding->bpjs_verified ? 'border-emerald-200' : 'border-slate-200' }} shadow-sm flex flex-col justify-between space-y-3">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center text-[10px]">3</span>
                            BPJS (Kesehatan / Ketenagakerjaan)
                        </span>
                        @if($onboarding->bpjs_verified)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi</span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">Belum Verifikasi</span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-600 space-y-1">
                        <p>No. Kartu BPJS: <strong class="text-slate-900 font-mono">{{ $onboarding->bpjs_number ?: 'Belum diisi' }}</strong></p>
                        @if($onboarding->bpjs_file)
                            <a href="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'bpjs', 'view' => 1]) }}" target="_blank" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                                <span>Lihat Kartu BPJS</span> &rarr;
                            </a>
                        @else
                            <span class="text-slate-400 text-[11px]">Berkas belum diunggah</span>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route($routePrefix . '.onboardings.verify', $onboarding->id) }}" class="pt-2 border-t border-slate-100 flex justify-end">
                    @csrf
                    <input type="hidden" name="item" value="bpjs">
                    <input type="hidden" name="verified" value="{{ $onboarding->bpjs_verified ? '0' : '1' }}">
                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $onboarding->bpjs_verified ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}">
                        {{ $onboarding->bpjs_verified ? 'Batalkan Verifikasi' : 'Verifikasi BPJS' }}
                    </button>
                </form>
            </div>

            {{-- 4. Rekening Bank --}}
            <div class="p-5 rounded-2xl bg-white border {{ $onboarding->bank_verified ? 'border-emerald-200' : 'border-slate-200' }} shadow-sm flex flex-col justify-between space-y-3">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center text-[10px]">4</span>
                            Rekening Bank (Payroll Gaji)
                        </span>
                        @if($onboarding->bank_verified)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi</span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">Belum Verifikasi</span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-600 space-y-1">
                        <p>Bank: <strong class="text-slate-900">{{ $onboarding->bank_name ?: '-' }}</strong></p>
                        <p>No. Rekening: <strong class="text-slate-900 font-mono">{{ $onboarding->bank_account_number ?: '-' }}</strong></p>
                        <p>Atas Nama: <strong class="text-slate-900">{{ $onboarding->bank_account_holder ?: '-' }}</strong></p>
                        @if($onboarding->bank_book_file)
                            <a href="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'bank', 'view' => 1]) }}" target="_blank" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                                <span>Lihat Buku Tabungan</span> &rarr;
                            </a>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route($routePrefix . '.onboardings.verify', $onboarding->id) }}" class="pt-2 border-t border-slate-100 flex justify-end">
                    @csrf
                    <input type="hidden" name="item" value="bank">
                    <input type="hidden" name="verified" value="{{ $onboarding->bank_verified ? '0' : '1' }}">
                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $onboarding->bank_verified ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}">
                        {{ $onboarding->bank_verified ? 'Batalkan Verifikasi' : 'Verifikasi Rekening' }}
                    </button>
                </form>
            </div>

            {{-- 5. Ijazah --}}
            <div class="p-5 rounded-2xl bg-white border {{ $onboarding->ijazah_verified ? 'border-emerald-200' : 'border-slate-200' }} shadow-sm flex flex-col justify-between space-y-3">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center text-[10px]">5</span>
                            Ijazah &amp; Transkrip Nilai Terakhir
                        </span>
                        @if($onboarding->ijazah_verified)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi</span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">Belum Verifikasi</span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-600 space-y-1">
                        @if($onboarding->ijazah_file)
                            <p class="text-emerald-700 font-semibold">Berkas ijazah telah diunggah.</p>
                            <a href="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'ijazah', 'view' => 1]) }}" target="_blank" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                                <span>Buka Dokumen Ijazah</span> &rarr;
                            </a>
                        @else
                            <span class="text-slate-400 text-[11px]">Berkas belum diunggah oleh kandidat</span>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route($routePrefix . '.onboardings.verify', $onboarding->id) }}" class="pt-2 border-t border-slate-100 flex justify-end">
                    @csrf
                    <input type="hidden" name="item" value="ijazah">
                    <input type="hidden" name="verified" value="{{ $onboarding->ijazah_verified ? '0' : '1' }}">
                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $onboarding->ijazah_verified ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}">
                        {{ $onboarding->ijazah_verified ? 'Batalkan Verifikasi' : 'Verifikasi Ijazah' }}
                    </button>
                </form>
            </div>

            {{-- 6. Pas Foto --}}
            <div class="p-5 rounded-2xl bg-white border {{ $onboarding->pas_foto_verified ? 'border-emerald-200' : 'border-slate-200' }} shadow-sm flex flex-col justify-between space-y-3">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center text-[10px]">6</span>
                            Pas Foto Formal (Background Merah/Biru)
                        </span>
                        @if($onboarding->pas_foto_verified)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi</span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">Belum Verifikasi</span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-600 flex items-center gap-3">
                        @if($onboarding->pas_foto_file)
                            <img src="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'pas_foto', 'view' => 1]) }}" alt="Pas Foto" class="w-12 h-14 object-cover rounded-lg border border-slate-300">
                            <div>
                                <p class="text-emerald-700 font-semibold">Foto formal terunggah</p>
                                <a href="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'pas_foto', 'view' => 1]) }}" target="_blank" class="text-blue-600 font-bold hover:underline text-[11px]">
                                    Lihat Resolusi Penuh &rarr;
                                </a>
                            </div>
                        @else
                            <span class="text-slate-400 text-[11px]">Pas foto belum diunggah</span>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route($routePrefix . '.onboardings.verify', $onboarding->id) }}" class="pt-2 border-t border-slate-100 flex justify-end">
                    @csrf
                    <input type="hidden" name="item" value="pas_foto">
                    <input type="hidden" name="verified" value="{{ $onboarding->pas_foto_verified ? '0' : '1' }}">
                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $onboarding->pas_foto_verified ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}">
                        {{ $onboarding->pas_foto_verified ? 'Batalkan Verifikasi' : 'Verifikasi Pas Foto' }}
                    </button>
                </form>
            </div>

            {{-- 7. Kontrak Kerja --}}
            <div class="p-5 rounded-2xl bg-white border {{ $onboarding->contract_signed ? 'border-emerald-200' : 'border-slate-200' }} shadow-sm flex flex-col justify-between space-y-3">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center text-[10px]">7</span>
                            Kontrak Kerja (Bertandatangan)
                        </span>
                        @if($onboarding->contract_signed)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Ditandatangani</span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">Belum Bertandatangan</span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-600 space-y-1">
                        @if($onboarding->contract_file)
                            <p class="text-emerald-700 font-semibold">Dokumen kontrak bertandatangan telah diunggah.</p>
                            <a href="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'contract', 'view' => 1]) }}" target="_blank" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-1">
                                <span>Unduh / Tinjau Kontrak</span> &rarr;
                            </a>
                        @else
                            <span class="text-slate-400 text-[11px]">Berkas kontrak bertandatangan belum diunggah</span>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route($routePrefix . '.onboardings.verify', $onboarding->id) }}" class="pt-2 border-t border-slate-100 flex justify-end">
                    @csrf
                    <input type="hidden" name="item" value="contract">
                    <input type="hidden" name="verified" value="{{ $onboarding->contract_signed ? '0' : '1' }}">
                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $onboarding->contract_signed ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}">
                        {{ $onboarding->contract_signed ? 'Batalkan Verifikasi' : 'Verifikasi Kontrak' }}
                    </button>
                </form>
            </div>

            {{-- 8. Emergency Contact --}}
            <div class="p-5 rounded-2xl bg-white border {{ $onboarding->emergency_verified ? 'border-emerald-200' : 'border-slate-200' }} shadow-sm flex flex-col justify-between space-y-3">
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-900 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-slate-900 text-white flex items-center justify-center text-[10px]">8</span>
                            Emergency Contact (Kontak Darurat)
                        </span>
                        @if($onboarding->emergency_verified)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi</span>
                        @else
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">Belum Verifikasi</span>
                        @endif
                    </div>

                    <div class="text-xs text-slate-600 space-y-1">
                        <p>Nama Kontak: <strong class="text-slate-900">{{ $onboarding->emergency_contact_name ?: '-' }}</strong></p>
                        <p>Hubungan: <strong class="text-slate-900">{{ $onboarding->emergency_contact_relation ?: '-' }}</strong></p>
                        <p>No. Telepon: <strong class="text-slate-900 font-mono">{{ $onboarding->emergency_contact_phone ?: '-' }}</strong></p>
                    </div>
                </div>

                <form method="POST" action="{{ route($routePrefix . '.onboardings.verify', $onboarding->id) }}" class="pt-2 border-t border-slate-100 flex justify-end">
                    @csrf
                    <input type="hidden" name="item" value="emergency">
                    <input type="hidden" name="verified" value="{{ $onboarding->emergency_verified ? '0' : '1' }}">
                    <button type="submit" class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $onboarding->emergency_verified ? 'bg-slate-100 hover:bg-slate-200 text-slate-700' : 'bg-emerald-600 hover:bg-emerald-500 text-white' }}">
                        {{ $onboarding->emergency_verified ? 'Batalkan Verifikasi' : 'Verifikasi Kontak' }}
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
