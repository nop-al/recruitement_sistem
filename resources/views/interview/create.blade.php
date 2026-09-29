@extends('layouts.dashboard')

@section('title', 'Jadwalkan Wawancara Online Baru')
@section('role-label', 'Manajemen Wawancara Online')
@section('page-title', 'Jadwalkan Wawancara & Generate Link')

@php
    $currentRole = auth()->user()?->role?->name;
    $prefix = match($currentRole) {
        'admin' => 'admin',
        'hr' => 'hr',
        default => 'interview',
    };
    $indexRoute = $prefix . '.interviews.index';
    $storeRoute = $prefix . '.interviews.store';
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header & Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route($indexRoute) }}"
               class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Jadwalkan Wawancara Online Baru</h2>
                <p class="text-xs text-slate-500">Pilih pelamar yang lolos dan generate ruang meeting virtual unik (Zoom / Google Meet)</p>
            </div>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 border border-amber-200">
            Sesi Baru
        </span>
    </div>

    <!-- Main Schedule Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route($storeRoute) }}" method="POST" class="space-y-6" id="interviewForm">
            @csrf

            <!-- Section 1: Pemilihan Kandidat Pelamar -->
            <div>
                <div class="pb-3 mb-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">1. Pilih Kandidat Pelamar yang Akan Diwawancarai</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pilih dari pelamar aktif yang masuk dalam pipeline rekrutmen</p>
                </div>

                <!-- Quick Selector from Active Applications -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Pilih Cepat dari Berkas Pelamar Masuk
                    </label>
                    <select id="quickApplicationSelector" onchange="syncFromApplication(this)"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        <option value="">-- Pilih Pelamar dari Daftar Lamaran --</option>
                        @foreach($applications as $app)
                            <option value="{{ $app->candidate_id }}"
                                    data-position-id="{{ $app->position_id }}"
                                    data-candidate-id="{{ $app->candidate_id }}"
                                    {{ ($selectedCandidateId == $app->candidate_id && $selectedPositionId == $app->position_id) ? 'selected' : '' }}>
                                {{ $app->candidate?->full_name }} — Posisi: {{ $app->position?->title }} (Tahap: {{ strtoupper($app->stage) }})
                            </option>
                        @endforeach
                    </select>
                    <span class="text-[10px] text-slate-400 mt-1 block">
                        Memilih pelamar di atas akan otomatis mengisikan kandidat dan posisi yang dilamar di bawah.
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Candidate Select -->
                    <div>
                        <label for="candidate_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Kandidat <span class="text-rose-500">*</span>
                        </label>
                        <select name="candidate_id" id="candidate_id" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                            <option value="">-- Pilih Kandidat --</option>
                            @foreach($candidates as $candidate)
                                <option value="{{ $candidate->id }}" {{ (old('candidate_id', $selectedCandidateId) == $candidate->id) ? 'selected' : '' }}>
                                    {{ $candidate->full_name }} ({{ $candidate->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('candidate_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Position Select -->
                    <div>
                        <label for="position_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Posisi Lowongan yang Dilamar <span class="text-rose-500">*</span>
                        </label>
                        <select name="position_id" id="position_id" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                            <option value="">-- Pilih Posisi --</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ (old('position_id', $selectedPositionId) == $position->id) ? 'selected' : '' }}>
                                    {{ $position->title }} ({{ $position->department }})
                                </option>
                            @endforeach
                        </select>
                        @error('position_id')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Detail Wawancara & Pewawancara -->
            <div>
                <div class="pb-3 mb-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">2. Jadwal & Penugasan Pewawancara</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Tentukan waktu pelaksanaan dan anggota tim penilai</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Interview Type -->
                    <div>
                        <label for="interview_type" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tipe / Topik Sesi <span class="text-rose-500">*</span>
                        </label>
                        <select name="interview_type" id="interview_type" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                            <option value="User & Technical Interview" {{ old('interview_type') == 'User & Technical Interview' ? 'selected' : '' }}>User & Technical Interview</option>
                            <option value="System Design & Architecture" {{ old('interview_type') == 'System Design & Architecture' ? 'selected' : '' }}>System Design & Architecture</option>
                            <option value="HR Behavioral & Culture Fit" {{ old('interview_type') == 'HR Behavioral & Culture Fit' ? 'selected' : '' }}>HR Behavioral & Culture Fit</option>
                            <option value="Live Coding & Problem Solving" {{ old('interview_type') == 'Live Coding & Problem Solving' ? 'selected' : '' }}>Live Coding & Problem Solving</option>
                            <option value="Final Board Interview" {{ old('interview_type') == 'Final Board Interview' ? 'selected' : '' }}>Final Board Interview</option>
                        </select>
                        @error('interview_type')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Scheduled Date & Time -->
                    <div>
                        <label for="scheduled_at" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Waktu Wawancara <span class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" required
                               value="{{ old('scheduled_at', now()->addDay()->setHour(10)->setMinute(0)->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        @error('scheduled_at')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Assigned Interviewer -->
                    <div>
                        <label for="interviewer_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Pewawancara Utama <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <select name="interviewer_id" id="interviewer_id"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                            <option value="">-- Gunakan Akun Saya Saat Ini --</option>
                            @foreach($interviewers as $interviewer)
                                <option value="{{ $interviewer->id }}" {{ (old('interviewer_id', auth()->id()) == $interviewer->id) ? 'selected' : '' }}>
                                    {{ $interviewer->name }} ({{ strtoupper($interviewer->role?->name ?? 'User') }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 3: Metode Pelaksanaan & Ruang Wawancara (Online vs Offline) -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200">
                <div class="pb-3 mb-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">3. Metode Pelaksanaan Wawancara</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pilih format pelaksanaan: Online melalui platform video conference atau Offline tatap muka di kantor</p>
                    </div>
                </div>

                <!-- Method Switcher (Online vs Offline) -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-slate-700 mb-2">Metode Wawancara <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- Option Online -->
                        <label id="method_label_online" class="flex items-center gap-3 p-3.5 rounded-xl border-2 border-amber-500 bg-amber-50/50 cursor-pointer transition">
                            <input type="radio" name="interview_method" value="online" checked onchange="switchMethod('online')"
                                   class="text-amber-600 focus:ring-amber-500">
                            <div>
                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Wawancara Online (Virtual)</span>
                                </div>
                                <div class="text-[10px] text-slate-500 mt-0.5">Google Meet, Zoom, atau tautan video conference unik</div>
                            </div>
                        </label>

                        <!-- Option Offline -->
                        <label id="method_label_offline" class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-amber-400 transition">
                            <input type="radio" name="interview_method" value="offline" onchange="switchMethod('offline')"
                                   class="text-amber-600 focus:ring-amber-500">
                            <div>
                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <span>Wawancara Offline (Tatap Muka)</span>
                                </div>
                                <div class="text-[10px] text-slate-500 mt-0.5">Pertemuan fisik di kantor perusahaan / ruang wawancara</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- CONTAINER 1: ONLINE FIELDS -->
                <div id="container_online" class="space-y-4">
                    <!-- Platform Option Radio -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Platform Ruang Virtual:</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Google Meet Option -->
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-emerald-500 transition">
                                <input type="radio" name="platform" value="google_meet" checked onchange="switchPlatform('google_meet')"
                                       class="text-emerald-600 focus:ring-emerald-500">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900">Google Meet</div>
                                        <div class="text-[10px] text-slate-500">meet.google.com/{kode-unik}</div>
                                    </div>
                                </div>
                            </label>

                            <!-- Zoom Option -->
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-blue-500 transition">
                                <input type="radio" name="platform" value="zoom" onchange="switchPlatform('zoom')"
                                       class="text-blue-600 focus:ring-blue-500">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M4.5 5.5A2.5 2.5 0 002 8v8a2.5 2.5 0 002.5 2.5h9A2.5 2.5 0 0016 16v-1.5l4.5 3a1 1 0 001.5-.83V7.33a1 1 0 00-1.5-.83L16 9.5V8a2.5 2.5 0 00-2.5-2.5h-9z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-900">Zoom Meeting</div>
                                        <div class="text-[10px] text-slate-500">zoom.us/j/{meeting_id}?pwd={pass}</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Generated Link Field with Quick Re-generate Button -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="room_link" class="text-xs font-semibold text-slate-700">
                                Tautan Ruang Meeting Virtual <span class="text-rose-500">*</span>
                            </label>
                            <button type="button" onclick="generateNewLink()"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 hover:text-amber-900 bg-amber-50 hover:bg-amber-100 px-2.5 py-1 rounded-lg border border-amber-200 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span>Acak Link Baru (Generate Ulang)</span>
                            </button>
                        </div>

                        <div class="relative">
                            <input type="url" name="room_link" id="room_link"
                                   value="{{ old('room_link', $uniqueMeetLink) }}"
                                   class="w-full pl-9 pr-24 py-2.5 rounded-xl border border-slate-300 text-xs font-mono text-slate-800 bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                            <div class="absolute left-3 top-2.5 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                            <button type="button" onclick="testMeetingLink()"
                                    class="absolute right-2 top-1.5 px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                                Cek Link
                            </button>
                        </div>
                        @error('room_link')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <span class="text-[10px] text-slate-500 mt-1 block">
                            Tautan unik ini akan otomatis dikirimkan ke portal kandidat untuk diakses saat jadwal sesi tiba.
                        </span>
                    </div>
                </div>

                <!-- CONTAINER 2: OFFLINE FIELDS -->
                <div id="container_offline" class="space-y-4 hidden">
                    <div>
                        <label for="location" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Lokasi Tatap Muka & Ruang Wawancara <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}"
                               placeholder="e.g. Kantor Pusat Lt. 3 - Ruang Meeting Bromo / Gedung Menara Cyber 2, Kuningan"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 bg-white focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">
                        @error('location')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <span class="text-[10px] text-slate-500 mt-1 block">
                            Informasi gedung dan nama ruangan yang akan didatangi kandidat pada hari pelaksanaan.
                        </span>
                    </div>
                </div>
            </div>

            <!-- Section 4: Catatan & Instruksi -->
            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Fokus Pembahasan / Catatan Sesi Wawancara <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <textarea name="notes" id="notes" rows="3"
                          placeholder="Tuliskan catatan teknis, fokus pertanyaan, atau materi uji untuk sesi ini..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route($indexRoute) }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-xs transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/30 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Simpan & Terbitkan Jadwal Wawancara</span>
                </button>
            </div>

        </form>
    </div>

</div>

{{-- Dynamic Meeting Link Generator Script --}}
<script>
    function syncFromApplication(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        if (!selectedOption || !selectedOption.value) return;

        const candidateId = selectedOption.getAttribute('data-candidate-id');
        const positionId = selectedOption.getAttribute('data-position-id');

        if (candidateId) {
            document.getElementById('candidate_id').value = candidateId;
        }
        if (positionId) {
            document.getElementById('position_id').value = positionId;
        }
    }

    function generateRandomString(length) {
        const chars = 'abcdefghijklmnopqrstuvwxyz';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    }

    function generateRandomNumber(digits) {
        let result = '';
        for (let i = 0; i < digits; i++) {
            result += Math.floor(Math.random() * 10);
        }
        return result;
    }

    function switchMethod(method) {
        const containerOnline = document.getElementById('container_online');
        const containerOffline = document.getElementById('container_offline');
        const labelOnline = document.getElementById('method_label_online');
        const labelOffline = document.getElementById('method_label_offline');
        const roomLinkInput = document.getElementById('room_link');
        const locationInput = document.getElementById('location');

        if (method === 'offline') {
            containerOnline.classList.add('hidden');
            containerOffline.classList.remove('hidden');
            labelOffline.classList.add('border-2', 'border-amber-500', 'bg-amber-50/50');
            labelOffline.classList.remove('border-slate-200', 'bg-white');
            labelOnline.classList.remove('border-2', 'border-amber-500', 'bg-amber-50/50');
            labelOnline.classList.add('border-slate-200', 'bg-white');
            roomLinkInput.removeAttribute('required');
            locationInput.setAttribute('required', 'required');
        } else {
            containerOnline.classList.remove('hidden');
            containerOffline.classList.add('hidden');
            labelOnline.classList.add('border-2', 'border-amber-500', 'bg-amber-50/50');
            labelOnline.classList.remove('border-slate-200', 'bg-white');
            labelOffline.classList.remove('border-2', 'border-amber-500', 'bg-amber-50/50');
            labelOffline.classList.add('border-slate-200', 'bg-white');
            locationInput.removeAttribute('required');
            roomLinkInput.setAttribute('required', 'required');
        }
    }

    function getSelectedPlatform() {
        const radios = document.getElementsByName('platform');
        for (const radio of radios) {
            if (radio.checked) return radio.value;
        }
        return 'google_meet';
    }

    function switchPlatform(platform) {
        generateNewLink();
    }

    function generateNewLink() {
        const platform = getSelectedPlatform();
        const roomLinkInput = document.getElementById('room_link');

        if (platform === 'zoom') {
            const meetingId = generateRandomNumber(10);
            const password = generateRandomString(6);
            roomLinkInput.value = `https://zoom.us/j/${meetingId}?pwd=${password}`;
        } else {
            // Google Meet format: xxx-yyyy-zzz
            const code1 = generateRandomString(3);
            const code2 = generateRandomString(4);
            const code3 = generateRandomString(3);
            roomLinkInput.value = `https://meet.google.com/${code1}-${code2}-${code3}`;
        }
    }

    function testMeetingLink() {
        const url = document.getElementById('room_link').value;
        if (url) {
            window.open(url, '_blank');
        } else {
            alert('Silakan masukkan tautan meeting terlebih dahulu.');
        }
    }
</script>
@endsection
