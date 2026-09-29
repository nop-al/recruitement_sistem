@extends('layouts.dashboard')

@section('title', 'Hasil Evaluasi: ' . $assessment->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Breadcrumbs --}}
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('kadidat.assessments.index') }}" class="hover:text-blue-600 transition">&larr; Kembali ke Daftar Tes</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Hasil Ujian</span>
    </div>

    {{-- Result Score Hero Card --}}
    <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200 shadow-sm text-center relative overflow-hidden">
        @if($result->result === 'passed')
            <div class="w-20 h-20 rounded-3xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/10">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 mb-2">
                ✓ Lolos Ambang Batas (Passed)
            </span>
            <h1 class="text-2xl font-black text-slate-900">Selamat! Anda Memenuhi Kualifikasi</h1>
            <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                Hasil tes Anda telah berhasil tercatat pada sistem. Tim HR akan segera meninjau dan melanjutkan proses ke tahap berikutnya.
            </p>
        @else
            <div class="w-20 h-20 rounded-3xl bg-rose-50 text-rose-600 border border-rose-200 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-rose-500/10">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 mb-2">
                ✕ Belum Memenuhi Passing Score
            </span>
            <h1 class="text-2xl font-black text-slate-900">Hasil Evaluasi Belum Mencapai Target</h1>
            <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                Nilai Anda belum mencapai batas kelulusan {{ $assessment->passing_score }}%. Tetap semangat dan Anda dapat mencoba melamar posisi lainnya.
            </p>
        @endif

        {{-- Score Numbers Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-100 text-left">
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block">Skor Akhir</span>
                <span class="text-xl font-black text-slate-900">{{ number_format($result->score, 1) }}%</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block">Passing Score</span>
                <span class="text-xl font-black text-slate-900">{{ $assessment->passing_score }}%</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block">Soal Benar</span>
                <span class="text-xl font-black text-slate-900">{{ $result->correct_answers }} / {{ $result->total_questions }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block">Waktu Selesai</span>
                <span class="text-xl font-black text-slate-900">{{ round($result->duration_spent / 60) }} Menit</span>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('kadidat.dashboard') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-600/30 transition">
                Kembali ke Dashboard &rarr;
            </a>
            <a href="{{ route('kadidat.assessments.index') }}" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                Daftar Ujian Lainnya
            </a>
        </div>
    </div>

</div>
@endsection
