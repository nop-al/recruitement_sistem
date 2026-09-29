@extends('layouts.dashboard')

@section('title', 'Sedang Mengerjakan: ' . $assessment->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Sticky Header with Timer & Progress --}}
    <div class="sticky top-20 z-20 p-4 sm:p-5 rounded-2xl bg-slate-900/95 backdrop-blur-xl border border-slate-800 text-white shadow-2xl flex items-center justify-between gap-4">
        <div class="min-w-0">
            <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider block">Ujian Online Berlangsung</span>
            <h1 class="text-sm sm:text-base font-extrabold truncate text-white">{{ $assessment->title }}</h1>
        </div>

        <div class="flex items-center gap-3 sm:gap-6 shrink-0">
            {{-- Answered Counter --}}
            <div class="text-right hidden sm:block">
                <span class="text-[10px] text-slate-400 block">Progres Soal:</span>
                <span id="answered-counter" class="text-xs font-bold text-slate-200">0 / {{ $questions->count() }} Terjawab</span>
            </div>

            {{-- Countdown Timer --}}
            <div class="px-3.5 py-2 rounded-xl bg-slate-800/90 border border-slate-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex flex-col">
                    <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold leading-none">Sisa Waktu</span>
                    <span id="timer-display" class="font-mono text-sm sm:text-base font-black text-amber-400 leading-tight">--:--</span>
                </div>
            </div>

            {{-- Quick Submit Top Button --}}
            <button type="button" onclick="confirmSubmit()"
                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-md shadow-emerald-600/30 transition">
                Kirim
            </button>
        </div>
    </div>

    {{-- Assessment Instructions Banner --}}
    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 text-blue-950 text-xs flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="space-y-1">
            <strong class="font-bold">Petunjuk Pengerjaan:</strong>
            <p class="text-blue-900/80 leading-relaxed">
                Pilihlah salah satu jawaban yang paling tepat untuk setiap soal. Jawaban Anda akan tersimpan secara otomatis saat memilih opsi. Jika waktu habis, ujian akan dikirimkan secara otomatis.
            </p>
        </div>
    </div>

    {{-- Questions Form --}}
    <form id="assessment-form" method="POST" action="{{ route('kadidat.assessments.submit', $assessment->id) }}" class="space-y-5">
        @csrf
        <input type="hidden" name="duration_spent" id="duration_spent" value="0">

        @foreach($questions as $index => $q)
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4 question-card" id="q-{{ $q->id }}">
                <div class="flex items-start justify-between gap-4 pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-2xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center shrink-0 shadow-sm shadow-indigo-600/30">
                            {{ $index + 1 }}
                        </span>
                        <span class="text-xs font-bold text-slate-400">Pertanyaan Ke-{{ $index + 1 }}</span>
                    </div>

                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">
                        Bobot: {{ $q->points }} Poin
                    </span>
                </div>

                {{-- Question Text --}}
                <div class="text-sm font-bold text-slate-900 leading-relaxed">
                    {{ $q->question_text }}
                </div>

                {{-- Answer Options --}}
                <div class="space-y-2.5 pt-2">
                    @foreach($q->answers as $optIndex => $opt)
                        <label class="flex items-center gap-3 p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-indigo-50/40 hover:border-indigo-300 transition cursor-pointer group">
                            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt->id }}"
                                   onchange="updateProgress()"
                                   class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                            <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0 group-hover:bg-indigo-600 group-hover:text-white transition">
                                {{ chr(65 + $optIndex) }}
                            </span>
                            <span class="text-xs text-slate-800 font-medium leading-normal flex-1">
                                {{ $opt->answer_text }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        {{-- Bottom Submit Card --}}
        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h4 class="text-sm font-bold text-slate-900">Periksa Kembali Jawaban Anda</h4>
                <p class="text-xs text-slate-500">Pastikan seluruh pertanyaan telah dijawab sebelum mengirimkan ujian.</p>
            </div>

            <button type="button" onclick="confirmSubmit()"
                    class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                <span>Selesaikan &amp; Kirim Ujian</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </button>
        </div>
    </form>

</div>

{{-- Timer & Progress Script --}}
<script>
    const totalQuestions = {{ $questions->count() }};
    const totalDurationSeconds = {{ $assessment->duration * 60 }};
    let remainingSeconds = totalDurationSeconds;
    let secondsSpent = 0;

    const timerDisplay = document.getElementById('timer-display');
    const counterDisplay = document.getElementById('answered-counter');
    const durationInput = document.getElementById('duration_spent');
    const form = document.getElementById('assessment-form');

    function updateTimer() {
        if (remainingSeconds <= 0) {
            alert('Waktu ujian telah habis! Jawaban Anda akan dikirimkan secara otomatis.');
            form.submit();
            return;
        }

        remainingSeconds--;
        secondsSpent++;
        durationInput.value = secondsSpent;

        const minutes = Math.floor(remainingSeconds / 60);
        const seconds = remainingSeconds % 60;
        timerDisplay.textContent =
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

        if (remainingSeconds < 300) { // < 5 minutes
            timerDisplay.classList.remove('text-amber-400');
            timerDisplay.classList.add('text-rose-500', 'animate-pulse');
        }
    }

    function updateProgress() {
        const checkedRadios = document.querySelectorAll('#assessment-form input[type="radio"]:checked');
        const count = checkedRadios.length;
        if (counterDisplay) {
            counterDisplay.textContent = count + ' / ' + totalQuestions + ' Terjawab';
        }
    }

    function confirmSubmit() {
        const checkedRadios = document.querySelectorAll('#assessment-form input[type="radio"]:checked');
        const count = checkedRadios.length;
        const unanswered = totalQuestions - count;

        let message = 'Apakah Anda yakin ingin menyelesaikan dan mengirimkan hasil ujian ini?';
        if (unanswered > 0) {
            message = 'Masih ada ' + unanswered + ' soal yang belum Anda jawab. Tetap ingin menyelesaikan ujian sekarang?';
        }

        if (confirm(message)) {
            form.submit();
        }
    }

    // Start timer interval
    setInterval(updateTimer, 1000);
    updateProgress();
</script>
@endsection
