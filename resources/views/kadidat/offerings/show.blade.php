@extends('layouts.dashboard')

@section('title', 'Tinjau Penawaran Kerja - ' . $offering->position?->title)

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
    #printable-candidate-sheet {
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
        $companyName = \App\Models\SystemSetting::get('company_name', 'PT Rekrutmen Indonesia Bersatu');
        $appName = \App\Models\SystemSetting::get('app_name', 'RecruitPro');
    @endphp

    {{-- Breadcrumbs & Print Action Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 no-print">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('kadidat.offerings.index') }}" class="hover:text-blue-600 transition">&larr; Kembali ke Daftar Penawaran</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">{{ $offering->position?->title }}</span>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('kadidat.offerings.print', $offering->id) }}" target="_blank"
               class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5"
               title="Buka dokumen siap cetak A4 / PDF resmi di jendela baru">
                <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak / Simpan PDF</span>
            </a>
        </div>
    </div>

    {{-- Decision Alert / Status Banner --}}
    @if($offering->status === 'accepted')
        <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex flex-col sm:flex-row sm:items-center justify-between gap-4 no-print">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                    ✓
                </div>
                <div>
                    <h3 class="text-sm font-black text-emerald-950">Penawaran Kerja Telah Anda Terima!</h3>
                    <p class="text-xs text-emerald-800/80 mt-0.5">
                        Diterima pada {{ $offering->responded_at?->format('d M Y, H:i') }}. Silakan lanjutkan ke tahap pengunggahan dokumen onboarding.
                    </p>
                </div>
            </div>
            <a href="{{ route('kadidat.onboarding.index') }}"
               class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition shrink-0 text-center">
                Lengkapi Berkas Onboarding &rarr;
            </a>
        </div>
    @elseif($offering->status === 'rejected')
        <div class="p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 flex items-center gap-3 no-print">
            <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                ✕
            </div>
            <div>
                <h3 class="text-sm font-black text-rose-950">Penawaran Kerja Telah Anda Tolak</h3>
                <p class="text-xs text-rose-800/80 mt-0.5">
                    Keputusan Anda telah dicatat pada sistem rekrutmen. Terima kasih atas partisipasi Anda dalam proses seleksi.
                </p>
            </div>
        </div>
    @else
        <div class="p-5 rounded-2xl bg-gradient-to-r from-blue-50 via-indigo-50 to-emerald-50 border border-blue-200 text-blue-950 flex flex-col sm:flex-row sm:items-center justify-between gap-4 no-print">
            <div>
                <span class="text-[10px] uppercase font-bold text-blue-600 tracking-wider block">Tindakan Diperlukan:</span>
                <h3 class="text-sm font-black text-slate-900">Konfirmasi Penerimaan Penawaran Kerja</h3>
                <p class="text-xs text-slate-600 mt-0.5">
                    Mohon baca seksama rincian surat penawaran di bawah ini dan tentukan keputusan Anda.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="openRejectModal()"
                        class="px-4 py-2 rounded-xl bg-white hover:bg-rose-50 text-rose-600 border border-rose-200 font-bold text-xs transition">
                    Tolak Penawaran
                </button>
                <button type="button" onclick="openAcceptModal()"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition">
                    ✓ Terima Penawaran
                </button>
            </div>
        </div>
    @endif

    {{-- Official Offering Letter Document Sheet --}}
    <div id="printable-candidate-sheet" class="bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-12 space-y-8 print:shadow-none print:border-none print:p-0">

        {{-- Header / Kop Surat --}}
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-6">
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-900 uppercase">{{ $companyName }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">Departemen People &amp; Talent Acquisition &bull; {{ $appName }}</p>
                <p class="text-xs text-slate-400">Jakarta Selatan, Indonesia &bull; contact@recruitment.test</p>
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                    @if($offering->status === 'accepted') bg-emerald-100 text-emerald-800 border border-emerald-200
                    @elseif($offering->status === 'rejected') bg-rose-100 text-rose-800 border border-rose-200
                    @else bg-blue-100 text-blue-800 border border-blue-200 @endif">
                    {{ \App\Models\Offering::STATUSES[$offering->status] ?? ucfirst($offering->status) }}
                </span>
                <div class="text-[11px] text-slate-400 font-mono mt-1.5">No: OFF/{{ date('Y') }}/{{ str_pad($offering->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="text-xs text-slate-600 mt-0.5">{{ $offering->created_at->format('d F Y') }}</div>
            </div>
        </div>

        {{-- Recipient Info --}}
        <div class="space-y-1 text-xs text-slate-700">
            <p class="text-slate-500 font-semibold">Kepada Yth,</p>
            <h3 class="text-base font-black text-slate-900">{{ $offering->candidate?->full_name }}</h3>
            <p>{{ $offering->candidate?->email }} &bull; {{ $offering->candidate?->phone_number }}</p>
            <p class="text-slate-500">Perihal: <strong class="text-slate-900">Surat Penawaran Kerja Resmi (Job Offer Letter)</strong></p>
        </div>

        {{-- Body --}}
        <div class="text-xs text-slate-700 leading-relaxed space-y-3">
            <p>Dengan hormat,</p>
            <p>
                Berdasarkan hasil rangkaian proses evaluasi seleksi dan wawancara yang telah Anda ikuti dengan hasil yang sangat baik, dengan ini manajemen <strong>{{ $companyName }}</strong> menyampaikan penawaran kerja resmi untuk bergabung bersama tim kami dengan rincian sebagai berikut:
            </p>
        </div>

        {{-- Offering Details Box --}}
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

        {{-- Benefits --}}
        @if($offering->benefits)
            <div class="space-y-2">
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-800">Tunjangan, Fasilitas &amp; Benefit:</h4>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 whitespace-pre-line leading-relaxed">
                    {{ $offering->benefits }}
                </div>
            </div>
        @endif

        {{-- Notes --}}
        @if($offering->notes)
            <div class="space-y-2">
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-800">Ketentuan Tambahan &amp; Onboarding:</h4>
                <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-200/60 text-xs text-blue-900 whitespace-pre-line leading-relaxed">
                    {{ $offering->notes }}
                </div>
            </div>
        @endif

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
                            <span class="text-emerald-600 font-bold">✓ Telah Diterima Digital</span>
                        @elseif($offering->status === 'rejected')
                            <span class="text-rose-600 font-bold">✕ Ditolak</span>
                        @else
                            Menunggu Konfirmasi
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- Accept Modal --}}
<div id="accept-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-4">
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div class="text-center">
            <h3 class="text-base font-black text-slate-900">Terima Penawaran Kerja Ini?</h3>
            <p class="text-xs text-slate-500 mt-1">
                Dengan menerima penawaran, Anda menyatakan bersedia bergabung sesuai ketentuan di atas dan akan dialihkan ke tahap Onboarding untuk melengkapi berkas checklist.
            </p>
        </div>

        <form method="POST" action="{{ route('kadidat.offerings.respond', $offering->id) }}" class="space-y-3">
            @csrf
            <input type="hidden" name="action" value="accept">

            <div>
                <label for="accept-notes" class="block text-xs font-bold text-slate-700 mb-1">Catatan / Pesan untuk HR (Opsional):</label>
                <textarea name="notes" id="accept-notes" rows="2"
                          placeholder="Terima kasih atas penawaran ini, saya siap bergabung..."
                          class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button type="button" onclick="closeAcceptModal()" class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </button>
                <button type="submit" class="py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition">
                    Konfirmasi Terima
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="reject-modal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-200 space-y-4">
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <div class="text-center">
            <h3 class="text-base font-black text-slate-900">Tolak Penawaran Kerja?</h3>
            <p class="text-xs text-slate-500 mt-1">
                Apakah Anda yakin ingin menolak penawaran kerja ini? Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>

        <form method="POST" action="{{ route('kadidat.offerings.respond', $offering->id) }}" class="space-y-3">
            @csrf
            <input type="hidden" name="action" value="reject">

            <div>
                <label for="reject-notes" class="block text-xs font-bold text-slate-700 mb-1">Alasan Penolakan (Opsional):</label>
                <textarea name="notes" id="reject-notes" rows="2"
                          placeholder="Alasan penolakan penawaran..."
                          class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button type="button" onclick="closeRejectModal()" class="py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Kembali
                </button>
                <button type="submit" class="py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow-md shadow-rose-600/30 transition">
                    Ya, Tolak Penawaran
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAcceptModal() {
        const modal = document.getElementById('accept-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeAcceptModal() {
        const modal = document.getElementById('accept-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function openRejectModal() {
        const modal = document.getElementById('reject-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeRejectModal() {
        const modal = document.getElementById('reject-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>
@endsection
