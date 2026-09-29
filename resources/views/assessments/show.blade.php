@extends('layouts.dashboard')

@section('title', $assessment->title . ' - Kelola Bank Soal')

@section('content')
<div class="space-y-6">

    @php
        $routePrefix = auth()->user()?->role?->name === 'admin' ? 'admin' : 'hr';
    @endphp

    {{-- Breadcrumbs --}}
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route($routePrefix . '.assessments.index') }}" class="hover:text-indigo-600 transition">&larr; Kembali ke Daftar Assessment</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">{{ $assessment->title }}</span>
    </div>

    {{-- Test Overview Hero Card --}}
    <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm relative overflow-hidden">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-2">
                    @php
                        $typeBadgeClass = match($assessment->test_type) {
                            'Technical Test' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'Logic Test' => 'bg-purple-50 text-purple-700 border-purple-200',
                            'Psychological Test' => 'bg-pink-50 text-pink-700 border-pink-200',
                            'Personality Test' => 'bg-amber-50 text-amber-700 border-amber-200',
                            default => 'bg-emerald-50 text-emerald-700 border-emerald-200'
                        };
                    @endphp
                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold border {{ $typeBadgeClass }}">
                        {{ $assessment->test_type }}
                    </span>

                    @if($assessment->is_active)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Tes Aktif
                        </span>
                    @else
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                            Nonaktif
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $assessment->title }}</h1>
                <p class="text-xs text-slate-500 mt-1 max-w-2xl">
                    {{ $assessment->description ?: 'Tidak ada instruksi khusus untuk tes ini.' }}
                </p>

                <div class="flex items-center gap-4 text-xs mt-4 flex-wrap text-slate-600">
                    <span class="flex items-center gap-1.5 font-medium">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Durasi: <strong class="text-slate-900">{{ $assessment->duration }} Menit</strong>
                    </span>

                    <span class="flex items-center gap-1.5 font-medium">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Passing Score: <strong class="text-emerald-700">{{ $assessment->passing_score }}%</strong>
                    </span>

                    <span class="flex items-center gap-1.5 font-medium">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Posisi: <strong class="text-slate-900">{{ $assessment->position?->title ?? 'Semua Posisi (Umum)' }}</strong>
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route($routePrefix . '.assessments.edit', $assessment->id) }}"
                   class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                    Edit Modul
                </a>
            </div>
        </div>
    </div>

    {{-- Tabs or Section Layout: Question Bank vs Results --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- Left / Main Column: Question Bank (Bank Soal) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Add Question Form Card --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-black text-slate-900">Tambah Soal ke Bank Soal</h2>
                        <p class="text-xs text-slate-500">Buat pertanyaan pilihan ganda dan tentukan opsi jawaban yang benar.</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold">
                        Pilihan Ganda
                    </span>
                </div>

                <form method="POST" action="{{ route($routePrefix . '.assessments.questions.store', $assessment->id) }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="question_text" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Isi Pertanyaan Soal <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="question_text" id="question_text" rows="3" required
                                  placeholder="Tuliskan butir pertanyaan secara jelas..."
                                  class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="points" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Bobot Poin Soal <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="points" id="points" value="10" min="1" max="100" required
                                   class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="explanation" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Penjelasan / Pembahasan (Opsional)
                            </label>
                            <input type="text" name="explanation" id="explanation"
                                   placeholder="Alasan mengapa jawaban tersebut benar..."
                                   class="w-full px-3.5 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Options A, B, C, D --}}
                    <div class="space-y-2.5 pt-2">
                        <label class="block text-xs font-bold text-slate-700">
                            Opsi Pilihan Jawaban (Pilih Radio untuk Jawaban Benar) <span class="text-rose-500">*</span>
                        </label>

                        @php $labels = ['A', 'B', 'C', 'D']; @endphp
                        @foreach($labels as $i => $letter)
                            <div class="flex items-center gap-3 p-2.5 rounded-xl bg-slate-50 border border-slate-200 hover:bg-slate-100/60 transition">
                                <label class="flex items-center gap-2 cursor-pointer shrink-0">
                                    <input type="radio" name="correct_option" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }}
                                           class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                    <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">
                                        {{ $letter }}
                                    </span>
                                </label>
                                <input type="text" name="options[{{ $i }}]" required
                                       placeholder="Teks pilihan jawaban {{ $letter }}..."
                                       class="flex-1 px-3 py-1.5 text-xs rounded-lg bg-white border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-3 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition">
                            + Simpan Butir Soal ke Bank Soal
                        </button>
                    </div>
                </form>
            </div>

            {{-- Existing Questions List --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-black text-slate-900">
                        Daftar Butir Soal ({{ $questions->count() }})
                    </h2>
                    <span class="text-xs font-semibold text-slate-500">
                        Total Bobot: {{ $questions->sum('points') }} Poin
                    </span>
                </div>

                @if($questions->isEmpty())
                    <div class="p-8 rounded-2xl bg-white border border-slate-200 text-center text-xs text-slate-500">
                        Belum ada butir pertanyaan pada tes ini. Silakan gunakan form di atas untuk menambahkan soal.
                    </div>
                @else
                    @foreach($questions as $index => $q)
                        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3 relative group">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <span class="w-7 h-7 rounded-xl bg-slate-900 text-white font-black text-xs flex items-center justify-center shrink-0">
                                        {{ $index + 1 }}
                                    </span>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900 leading-relaxed">{{ $q->question_text }}</p>
                                        @if($q->explanation)
                                            <p class="text-[11px] text-slate-400 mt-1 italic">
                                                💡 Pembahasan: {{ $q->explanation }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-[10px] font-bold">
                                        {{ $q->points }} Poin
                                    </span>

                                    <form method="POST" action="{{ route($routePrefix . '.assessments.questions.destroy', [$assessment->id, $q->id]) }}"
                                          onsubmit="return confirm('Hapus butir pertanyaan ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Butir Soal">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Options List --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                                @foreach($q->answers as $optIndex => $ans)
                                    <div class="p-2.5 rounded-xl border text-xs flex items-center gap-2 {{ $ans->is_correct ? 'bg-emerald-50/80 border-emerald-300 text-emerald-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                                        @if($ans->is_correct)
                                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] shrink-0">
                                                ✓
                                            </span>
                                        @else
                                            <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[10px] shrink-0">
                                                {{ chr(65 + $optIndex) }}
                                            </span>
                                        @endif
                                        <span class="truncate">{{ $ans->answer_text }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

        </div>

        {{-- Right Column: Candidate Test Results Table --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="p-5 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
                <div>
                    <h2 class="text-base font-black text-slate-900">Riwayat Hasil Pelamar</h2>
                    <p class="text-xs text-slate-500">Daftar kandidat yang telah menyelesaikan tes ini.</p>
                </div>

                @if($results->isEmpty())
                    <div class="p-6 text-center text-xs text-slate-400 rounded-2xl bg-slate-50 border border-slate-100">
                        Belum ada kandidat yang mengerjakan tes ini.
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($results as $res)
                            <div class="p-3.5 rounded-2xl border {{ $res->result === 'passed' ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200 bg-slate-50/50' }} space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-900 truncate">
                                        {{ $res->candidate?->full_name ?? 'Kandidat' }}
                                    </span>
                                    @if($res->result === 'passed')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                            Lulus
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                            Tidak Lulus
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-xs text-slate-600">
                                    <span>Skor: <strong class="text-slate-900">{{ number_format($res->score, 1) }}%</strong></span>
                                    <span class="text-[11px] text-slate-400">{{ $res->correct_answers }}/{{ $res->total_questions }} Benar</span>
                                </div>

                                <div class="text-[10px] text-slate-400 flex items-center justify-between pt-1 border-t border-slate-100">
                                    <span>Waktu: {{ round($res->duration_spent / 60) }} mnt</span>
                                    <span>{{ $res->completed_at?->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($results->hasPages())
                        <div class="pt-2">
                            {{ $results->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
