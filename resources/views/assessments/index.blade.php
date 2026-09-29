@extends('layouts.dashboard')

@section('title', 'Manajemen Assessment & Bank Soal')

@section('content')
<div class="space-y-6">

    {{-- Header Banner --}}
    <div class="p-6 rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-semibold border border-indigo-500/30 mb-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span>Modul Online Assessment & Bank Soal</span>
                </div>
                <h1 class="text-2xl font-black tracking-tight text-white">Assessment &amp; Tes Online</h1>
                <p class="text-xs text-slate-400 mt-1 max-w-2xl">
                    Kelola tes evaluasi kompetensi kandidat (Technical, Logic, Administrative, Psychological, Personality Test) beserta butir pertanyaan dan kunci jawaban.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @php
                    $routePrefix = auth()->user()?->role?->name === 'admin' ? 'admin' : 'hr';
                @endphp
                <a href="{{ route($routePrefix . '.assessments.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Buat Tes Baru</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route($routePrefix . '.assessments.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-5">
                <div class="relative">
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari judul tes assessment..."
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="sm:col-span-3">
                <select name="test_type" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Jenis Tes</option>
                    @foreach($testTypes as $type)
                        <option value="{{ $type }}" {{ $selectedType === $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3">
                <select name="position_id" class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Posisi Lowongan</option>
                    @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ (string)$selectedPosition === (string)$pos->id ? 'selected' : '' }}>{{ $pos->title }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-1 flex gap-1">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition flex items-center justify-center">
                    Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Assessment List Cards / Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($assessments->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Tes Assessment</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">Silakan buat modul tes baru untuk mulai menyusun bank soal dan menguji kompetensi pelamar.</p>
                <a href="{{ route($routePrefix . '.assessments.create') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-bold hover:bg-indigo-500">
                    + Buat Tes Pertama
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 uppercase font-bold text-[10px] tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4">Judul &amp; Jenis Tes</th>
                            <th class="py-3 px-4">Posisi Sasaran</th>
                            <th class="py-3 px-4 text-center">Durasi &amp; Passing Score</th>
                            <th class="py-3 px-4 text-center">Bank Soal</th>
                            <th class="py-3 px-4 text-center">Peserta</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($assessments as $test)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $test->title }}</div>
                                    <div class="mt-1 flex items-center gap-2">
                                        @php
                                            $typeBadgeClass = match($test->test_type) {
                                                'Technical Test' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'Logic Test' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                'Psychological Test' => 'bg-pink-50 text-pink-700 border-pink-200',
                                                'Personality Test' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                default => 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                            };
                                        @endphp
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $typeBadgeClass }}">
                                            {{ $test->test_type }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700">
                                    @if($test->position)
                                        <span class="font-semibold">{{ $test->position->title }}</span>
                                        <div class="text-[10px] text-slate-400">{{ $test->position->department }}</div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-medium">Umum / Semua Posisi</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <div class="font-semibold text-slate-800">{{ $test->duration }} Menit</div>
                                    <div class="text-[10px] text-emerald-600 font-bold">Min: {{ $test->passing_score }}%</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold">
                                        {{ $test->questions_count }} Butir Soal
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="text-xs font-bold text-slate-700">{{ $test->results_count }} Pelamar</span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if($test->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route($routePrefix . '.assessments.show', $test->id) }}"
                                           class="px-2.5 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold transition"
                                           title="Kelola Bank Soal & Hasil">
                                            Bank Soal
                                        </a>
                                        <a href="{{ route($routePrefix . '.assessments.edit', $test->id) }}"
                                           class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-slate-100 transition"
                                           title="Edit Tes">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route($routePrefix . '.assessments.destroy', $test->id) }}" onsubmit="return confirm('Hapus tes ini beserta seluruh butir soalnya?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Tes">
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

            @if($assessments->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $assessments->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
