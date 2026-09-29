@extends('layouts.dashboard')

@section('title', 'Surat Penawaran Kerja - ' . $offering->candidate?->full_name)

@push('styles')
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 15mm 18mm;
    }
    .no-print, [data-no-print] {
        display: none !important;
    }
    #printable-sheet {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
        border-radius: 0 !important;
        background: #ffffff !important;
    }
    body, html, main {
        background: #ffffff !important;
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @php
        $routePrefix = auth()->user()?->role?->name === 'admin' ? 'admin' : 'hr';
        $companyName = \App\Models\SystemSetting::get('company_name', 'PT Rekrutmen Indonesia Bersatu');
        $appName = \App\Models\SystemSetting::get('app_name', 'RecruitPro');
    @endphp

    {{-- Breadcrumbs & Top Action Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 no-print">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route($routePrefix . '.offerings.index') }}" class="hover:text-emerald-600 transition">&larr; Kembali ke Daftar Offering</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Surat Penawaran #OFF-{{ str_pad($offering->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($offering->status === 'draft')
                <form method="POST" action="{{ route($routePrefix . '.offerings.send', $offering->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>Kirimkan ke Kandidat</span>
                    </button>
                </form>
            @endif

            <a href="{{ route($routePrefix . '.offerings.print', $offering->id) }}" target="_blank"
               class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5"
               title="Buka dokumen siap cetak A4 / PDF resmi di jendela baru">
                <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak / PDF Resmi</span>
            </a>

            <button onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5"
                    title="Cetak cepat langsung dari halaman ini">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Print Cepat</span>
            </button>

            <a href="{{ route($routePrefix . '.offerings.edit', $offering->id) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                </svg>
                <span>Edit Offering</span>
            </a>
        </div>
    </div>

    {{-- Official Letter Printable Sheet --}}
    <div id="printable-sheet" class="bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-12 space-y-8 print:shadow-none print:border-none print:p-0">

        {{-- Letter Header / Kop Surat --}}
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-6">
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-900 uppercase">{{ $companyName }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">Departemen People &amp; Talent Acquisition &bull; Sistem {{ $appName }}</p>
                <p class="text-xs text-slate-400">Jakarta Selatan, DKI Jakarta, Indonesia &bull; contact@recruitment.test</p>
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                    @if($offering->status === 'accepted') bg-emerald-100 text-emerald-800 border border-emerald-200
                    @elseif($offering->status === 'rejected') bg-rose-100 text-rose-800 border border-rose-200
                    @elseif($offering->status === 'sent') bg-blue-100 text-blue-800 border border-blue-200
                    @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                    {{ \App\Models\Offering::STATUSES[$offering->status] ?? ucfirst($offering->status) }}
                </span>
                <div class="text-[11px] text-slate-400 font-mono mt-1.5">No: OFF/{{ date('Y') }}/{{ str_pad($offering->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="text-xs text-slate-600 mt-0.5">Tanggal: {{ $offering->created_at->format('d F Y') }}</div>
            </div>
        </div>

        {{-- Recipient Info --}}
        <div class="space-y-1 text-xs text-slate-700">
            <p class="text-slate-500 font-semibold">Kepada Yth,</p>
            <h3 class="text-base font-black text-slate-900">{{ $offering->candidate?->full_name }}</h3>
            <p>{{ $offering->candidate?->email }} &bull; {{ $offering->candidate?->phone_number }}</p>
            <p class="text-slate-500">Perihal: <strong class="text-slate-900">Surat Penawaran Kerja Resmi (Job Offer Letter)</strong></p>
        </div>

        {{-- Letter Opening --}}
        <div class="text-xs text-slate-700 leading-relaxed space-y-3">
            <p>
                Dengan hormat,
            </p>
            <p>
                Berdasarkan hasil rangkaian evaluasi, asesmen kompetensi, serta wawancara kerja yang telah Anda lalui, dengan ini manajemen <strong>{{ $companyName }}</strong> dengan bangga menawarkan kesempatan berkarir kepada Anda untuk posisi dan ketentuan sebagai berikut:
            </p>
        </div>

        {{-- Details Table Box --}}
        <div class="rounded-2xl border border-slate-200 overflow-hidden bg-slate-50/50">
            <table class="w-full text-xs text-slate-700">
                <tbody class="divide-y divide-slate-200">
                    <tr>
                        <td class="py-3 px-4 font-bold bg-slate-100/60 w-1/3">Posisi Jabatan</td>
                        <td class="py-3 px-4 font-extrabold text-slate-900">{{ $offering->position?->title }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-bold bg-slate-100/60">Departemen / Divisi</td>
                        <td class="py-3 px-4">{{ $offering->department ?: $offering->position?->department }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-bold bg-slate-100/60">Gaji Pokok yang Ditawarkan</td>
                        <td class="py-3 px-4">
                            <span class="text-base font-black text-emerald-700">
                                Rp {{ number_format($offering->effective_salary, 0, ',', '.') }}
                            </span>
                            <span class="text-slate-400 text-[11px] font-normal"> / Bulan (Gross)</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-bold bg-slate-100/60">Tipe Ikatan Kerja</td>
                        <td class="py-3 px-4 font-semibold">{{ $offering->employment_type ?: 'Full-time' }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-bold bg-slate-100/60">Tanggal Mulai Bekerja (Join Date)</td>
                        <td class="py-3 px-4 font-bold text-slate-900">
                            {{ $offering->effective_join_date ? \Carbon\Carbon::parse($offering->effective_join_date)->translatedFormat('d F Y') : '-' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Benefits Section --}}
        @if($offering->benefits)
            <div class="space-y-2">
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-800">Tunjangan, Fasilitas &amp; Benefit Kerja:</h4>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 whitespace-pre-line leading-relaxed">
                    {{ $offering->benefits }}
                </div>
            </div>
        @endif

        {{-- Notes / Terms Section --}}
        @if($offering->notes)
            <div class="space-y-2">
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-800">Ketentuan Tambahan &amp; Onboarding:</h4>
                <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-200/60 text-xs text-blue-900 whitespace-pre-line leading-relaxed">
                    {{ $offering->notes }}
                </div>
            </div>
        @endif

        {{-- Closing Paragraph --}}
        <div class="text-xs text-slate-700 leading-relaxed">
            <p>
                Jika Anda menyetujui seluruh ketentuan penawaran kerja di atas, silakan memberikan konfirmasi penerimaan melalui portal akun kandidat Anda. Setelah penawaran diterima, sistem akan otomatis membuka akses ke proses pengunggahan berkas <strong>Onboarding Karyawan</strong>.
            </p>
        </div>

        {{-- Signatures --}}
        <div class="pt-8 border-t border-slate-200 grid grid-cols-2 gap-8 text-xs text-slate-700">
            <div class="space-y-16">
                <p class="font-bold">Diterbitkan Oleh,</p>
                <div>
                    <div class="font-black text-slate-900 underline">HR &amp; Talent Acquisition Director</div>
                    <div class="text-slate-500">{{ $companyName }}</div>
                </div>
            </div>

            <div class="space-y-16 text-right">
                <p class="font-bold">Kandidat Pelamar,</p>
                <div>
                    <div class="font-black text-slate-900 underline">{{ $offering->candidate?->full_name }}</div>
                    <div class="text-slate-500">
                        @if($offering->status === 'accepted')
                            <span class="text-emerald-600 font-bold">✓ Diterima secara digital ({{ $offering->responded_at?->format('d M Y') }})</span>
                        @elseif($offering->status === 'rejected')
                            <span class="text-rose-600 font-bold">✕ Ditolak ({{ $offering->responded_at?->format('d M Y') }})</span>
                        @else
                            Menunggu Persetujuan
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
