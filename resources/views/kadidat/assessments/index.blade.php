@extends('layouts.dashboard')

@section('title', 'Tes Online & Assessment Kompetensi')

@section('content')
<div class="space-y-6">

    {{-- Hero Header --}}
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-blue-900 via-indigo-950 to-slate-900 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-3xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold border border-blue-400/30 mb-3">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                Portal Ujian Online Resmi
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Assessment &amp; Ujian Online</h1>
            <p class="text-xs sm:text-sm text-blue-100/80 mt-1.5 leading-relaxed">
                Uji kemampuan teknis, logika, kepribadian, dan psikologi Anda. Pastikan koneksi internet stabil sebelum menekan tombol mulai ujian.
            </p>
        </div>
    </div>

    {{-- Available Tests Grid --}}
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-black text-slate-900">Daftar Tes yang Tersedia</h2>
                <p class="text-xs text-slate-500">Tes yang disesuaikan dengan posisi lowongan yang Anda lamar.</p>
            </div>
            <span class="text-xs font-bold text-slate-400">{{ $assessments->count() }} Tes Terbuka</span>
        </div>

        @if($assessments->isEmpty())
            <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-xs text-slate-500">
                Saat ini belum ada jadwal tes online aktif untuk lamaran Anda. Tim rekruter akan menginformasikan jika tes telah dibuka.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($assessments as $test)
                    @php
                        $myAttempt = $myResults->firstWhere('assessment_id', $test->id);
                        $typeBadgeClass = match($test->test_type) {
                            'Technical Test' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'Logic Test' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'Psychological Test' => 'bg-pink-50 text-pink-700 border-pink-200',
                            'Personality Test' => 'bg-amber-50 text-amber-700 border-amber-200',
                            default => 'bg-emerald-50 text-emerald-700 border-emerald-200'
                        };
                    @endphp

                    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold border {{ $typeBadgeClass }}">
                                    {{ $test->test_type }}
                                </span>
                                <span class="text-[11px] font-medium text-slate-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $test->duration }} Menit
                                </span>
                            </div>

                            <h3 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition leading-snug">
                                {{ $test->title }}
                            </h3>

                            <p class="text-xs text-slate-500 line-clamp-2">
                                {{ $test->description ?: 'Tes online untuk mengukur kompetensi dan kualifikasi kerja.' }}
                            </p>

                            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-600">
                                <span>Jumlah: <strong>{{ $test->questions_count }} Soal</strong></span>
                                <span>Passing: <strong class="text-emerald-600">{{ $test->passing_score }}%</strong></span>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-t border-slate-100">
                            @if($myAttempt)
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] text-slate-400 block">Status Anda:</span>
                                        @if($myAttempt->result === 'passed')
                                            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                                ✓ Lolos ({{ number_format($myAttempt->score, 0) }}%)
                                            </span>
                                        @else
                                            <span class="text-xs font-bold text-rose-600 flex items-center gap-1">
                                                ✕ Belum Lolos ({{ number_format($myAttempt->score, 0) }}%)
                                            </span>
                                        @endif
                                    </div>

                                    <a href="{{ route('kadidat.assessments.result', $myAttempt->id) }}"
                                       class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                                        Lihat Hasil &rarr;
                                    </a>
                                </div>
                            @else
                                @if($test->questions_count > 0)
                                    <a href="{{ route('kadidat.assessments.take', $test->id) }}"
                                       onclick="return confirm('Apakah Anda yakin ingin memulai ujian sekarang? Timer pengerjaan {{ $test->duration }} menit akan langsung berjalan.');"
                                       class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center justify-center gap-2">
                                        <span>Mulai Kerjakan Tes</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </a>
                                @else
                                    <button disabled class="w-full py-2 px-4 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs cursor-not-allowed">
                                        Soal Sedang Disiapkan
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- My Past Results History --}}
    @if($myResults->isNotEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h2 class="text-base font-black text-slate-900">Riwayat Nilai &amp; Hasil Tes Anda</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Nama Tes Assessment</th>
                            <th class="py-3 px-4 text-center">Waktu Pengerjaan</th>
                            <th class="py-3 px-4 text-center">Soal Benar</th>
                            <th class="py-3 px-4 text-center">Nilai Akhir</th>
                            <th class="py-3 px-4 text-center">Status Kelulusan</th>
                            <th class="py-3 px-4 text-right">Rincian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($myResults as $res)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-900">{{ $res->assessment?->title }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $res->assessment?->test_type }}</div>
                                </td>
                                <td class="py-3 px-4 text-center text-slate-600">
                                    {{ round($res->duration_spent / 60) }} menit
                                </td>
                                <td class="py-3 px-4 text-center text-slate-600">
                                    {{ $res->correct_answers }} dari {{ $res->total_questions }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="font-extrabold text-sm {{ $res->score >= ($res->assessment?->passing_score ?? 70) ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ number_format($res->score, 1) }}%
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($res->result === 'passed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            ✓ Lolos Passing Score
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                            ✕ Belum Memenuhi
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('kadidat.assessments.result', $res->id) }}"
                                       class="text-blue-600 hover:text-blue-800 font-bold hover:underline">
                                        Lihat &rarr;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
