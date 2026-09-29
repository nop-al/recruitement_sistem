@extends('layouts.dashboard')

@section('title', 'HR & Recruiter Dashboard')
@section('role-label', 'HR & Talent Acquisition')
@section('page-title', 'Pipeline Rekrutmen & Manajemen Pelamar')

@section('role-nav')
    <a href="#pipeline" class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 font-semibold border border-emerald-200">Funnel Rekrutmen</a>
    <a href="#positions" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition">Lowongan Kerja</a>
    <a href="#applications" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition">Pelamar Masuk</a>
    <a href="#interviews" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition">Jadwal Wawancara</a>
    <a href="#hiring" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition">Offering & Onboarding</a>
@endsection

@section('content')
<div class="space-y-8" id="pipeline">

    <!-- Top Recruiter Welcome Banner -->
    <div class="rounded-2xl bg-gradient-to-r from-emerald-900 via-teal-950 to-slate-900 p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold mb-3 border border-emerald-400/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    Talent Acquisition Active Cycle
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Bertugas, Sarah Wijaya, M.Psi!</h2>
                <p class="text-emerald-100/80 text-sm mt-1 max-w-2xl leading-relaxed">
                    Kelola siklus rekrutmen perusahaan secara real-time. Terdapat <span class="font-bold text-white">{{ $screeningCount }} berkas baru</span> menanti evaluasi awal dan <span class="font-bold text-white">{{ $upcomingInterviews->count() }} sesi wawancara</span> dijadwalkan.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('hr.positions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold shadow-lg shadow-emerald-500/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buka Lowongan Baru
                </a>
                <button type="button" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Export Laporan (Excel)
                </button>
            </div>
        </div>
    </div>

    <!-- Recruitment Funnel Strip -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">Funnel Jalur Rekrutmen (Pipeline)</h3>
                <p class="text-xs text-slate-500">Pergerakan kandidat dari registrasi awal hingga proses onboarding</p>
            </div>
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                Total {{ $totalApplications }} Pelamar Aktif
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mt-5">
            <!-- Step 1: Total Masuk -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-slate-300 transition text-center">
                <div class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">1. Total Berkas</div>
                <div class="text-2xl font-bold text-slate-900 mt-2">{{ $totalApplications }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Lamaran Masuk</div>
            </div>

            <!-- Step 2: Screening -->
            <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/50 hover:bg-white transition text-center">
                <div class="text-[11px] font-semibold text-amber-700 uppercase tracking-wider">2. Screening</div>
                <div class="text-2xl font-bold text-amber-900 mt-2">{{ $screeningCount }}</div>
                <div class="text-[11px] text-amber-700 mt-1">Verifikasi CV</div>
            </div>

            <!-- Step 3: Assessment -->
            <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/50 hover:bg-white transition text-center">
                <div class="text-[11px] font-semibold text-blue-700 uppercase tracking-wider">3. Assessment</div>
                <div class="text-2xl font-bold text-blue-900 mt-2">{{ $assessmentCount }}</div>
                <div class="text-[11px] text-blue-700 mt-1">Ujian & Tes Skill</div>
            </div>

            <!-- Step 4: Interview -->
            <div class="p-4 rounded-xl border border-indigo-200 bg-indigo-50/50 hover:bg-white transition text-center">
                <div class="text-[11px] font-semibold text-indigo-700 uppercase tracking-wider">4. Interview</div>
                <div class="text-2xl font-bold text-indigo-900 mt-2">{{ $interviewCount }}</div>
                <div class="text-[11px] text-indigo-700 mt-1">Wawancara User</div>
            </div>

            <!-- Step 5: Offering -->
            <div class="p-4 rounded-xl border border-purple-200 bg-purple-50/50 hover:bg-white transition text-center">
                <div class="text-[11px] font-semibold text-purple-700 uppercase tracking-wider">5. Offering</div>
                <div class="text-2xl font-bold text-purple-900 mt-2">{{ $offeringCount }}</div>
                <div class="text-[11px] text-purple-700 mt-1">Negosiasi Gaji</div>
            </div>

            <!-- Step 6: Onboarding -->
            <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/50 hover:bg-white transition text-center">
                <div class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">6. Onboarding</div>
                <div class="text-2xl font-bold text-emerald-900 mt-2">{{ $onboardingCount + $hiredCount }}</div>
                <div class="text-[11px] text-emerald-700 mt-1">Diterima Kerja</div>
            </div>
        </div>
    </div>

    <!-- Section: Lowongan Kerja Aktif -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6" id="positions">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">Lowongan Pekerjaan Aktif</h3>
                <p class="text-xs text-slate-500">Posisi yang sedang dibuka dan menerima aplikasi pelamar</p>
            </div>
            <button class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">+ Tambah Posisi Baru</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-5">
            @forelse($activePositions as $position)
                <div class="p-5 rounded-xl border border-slate-200/80 bg-slate-50/40 hover:bg-white hover:border-emerald-300 hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-2">
                            <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                {{ $position->department }}
                            </span>
                            <span class="text-xs font-semibold text-slate-500">Kuota: {{ $position->quota }} Orang</span>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 mt-2 line-clamp-1">{{ $position->title }}</h4>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $position->location }} &bull; {{ $position->type }}
                        </p>
                        <div class="mt-3 text-xs font-semibold text-slate-700">
                            Rp {{ number_format($position->salary_min, 0, ',', '.') }} - {{ number_format($position->salary_max, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-600">
                            <strong class="text-slate-900">{{ $position->applications_count }}</strong> Pelamar Masuk
                        </span>
                        <a href="#applications" class="font-bold text-emerald-600 hover:text-emerald-800">Review &rarr;</a>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-500 py-4 col-span-3 text-center">Belum ada lowongan pekerjaan aktif.</p>
            @endforelse
        </div>
    </div>

    <!-- Section: Pelamar Masuk & Jadwal Wawancara (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Latest Applications Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" id="applications">
            <div class="p-6 flex items-center justify-between border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Pelamar Masuk & Tahapan Terkini</h3>
                    <p class="text-xs text-slate-500">Kelola dan pindahkan tahap seleksi pelamar secara instan</p>
                </div>
                <span class="text-xs font-semibold text-slate-400">Terbaru</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-5 py-3">Nama Kandidat</th>
                            <th class="px-5 py-3">Posisi Dilamar</th>
                            <th class="px-5 py-3">Skor Cocok</th>
                            <th class="px-5 py-3">Tahap Sekarang</th>
                            <th class="px-5 py-3 text-right">Tindakan Cepat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentApplications as $app)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($app->candidate?->first_name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $app->candidate?->full_name }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $app->candidate?->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-slate-800">{{ $app->position?->title }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $app->position?->department }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                                        {{ $app->match_score >= 90 ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $app->match_score }}% Match
                                    </span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold uppercase tracking-wider
                                        {{ $app->stage === 'screening' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $app->stage === 'assessment' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $app->stage === 'interview' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                        {{ $app->stage === 'offering' ? 'bg-purple-100 text-purple-800' : '' }}
                                        {{ $app->stage === 'onboarding' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    ">
                                        {{ $app->stage }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right space-x-1">
                                    <button class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-700 hover:bg-emerald-100 font-semibold text-[11px] transition">
                                        Loloskan
                                    </button>
                                    <button class="px-2.5 py-1 rounded bg-slate-100 text-slate-600 hover:bg-slate-200 font-semibold text-[11px] transition">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-4 text-center text-slate-400">Belum ada data pelamar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Col: Upcoming Interviews -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 flex flex-col justify-between" id="interviews">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Jadwal Wawancara Terdekat</h3>
                        <p class="text-xs text-slate-500">Sesi user & technical interview terjadwal</p>
                    </div>
                    <span class="p-1.5 rounded-lg bg-amber-50 text-amber-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </span>
                </div>

                <div class="mt-4 space-y-3.5">
                    @forelse($upcomingInterviews as $interview)
                        <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:bg-white hover:border-amber-300 transition">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900">{{ $interview->candidate?->full_name }}</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $interview->position?->title }}</p>
                                </div>
                                <span class="text-[10px] font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded">
                                    {{ $interview->scheduled_at->format('d M, H:i') }} WIB
                                </span>
                            </div>

                            <div class="mt-2.5 pt-2 border-t border-slate-200/50 flex items-center justify-between text-[11px]">
                                <span class="text-slate-500">Pewawancara: <strong class="text-slate-700">{{ $interview->interviewer?->name ?? 'User Interviewer' }}</strong></span>
                                @if($interview->room_link)
                                    <a href="{{ $interview->room_link }}" target="_blank" class="text-emerald-600 font-bold hover:underline flex items-center gap-1">
                                        Google Meet &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-6 text-center">Tidak ada jadwal wawancara terdekat.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-5 pt-4 border-t border-slate-100">
                <button class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition text-center">
                    Buka Kalender Wawancara Lengkap
                </button>
            </div>
        </div>

    </div>

    <!-- Section: Offering & Onboarding Trackers -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="hiring">
        <!-- Offering Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Penawaran Kerja (Offering Letters)</h3>
                    <p class="text-xs text-slate-500">Status dokumen perjanjian kerja dan konfirmasi kompensasi</p>
                </div>
                <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full">Active</span>
            </div>

            <div class="mt-4 space-y-3">
                @forelse($recentOfferings as $offer)
                    <div class="p-3.5 rounded-xl border border-purple-100 bg-purple-50/30 flex items-center justify-between">
                        <div>
                            <div class="font-bold text-xs text-slate-900">{{ $offer->candidate?->full_name }}</div>
                            <div class="text-[11px] text-slate-500">{{ $offer->position?->title }}</div>
                            <div class="text-[11px] font-semibold text-purple-700 mt-1">
                                Tawaran: Rp {{ number_format($offer->salary_offered, 0, ',', '.') }} / bln
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold uppercase
                                {{ $offer->status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $offer->status }}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-1">Mulai: {{ $offer->start_date?->format('d M Y') }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-3 text-center">Belum ada offering aktif.</p>
                @endforelse
            </div>
        </div>

        <!-- Onboarding Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Onboarding Karyawan Baru</h3>
                    <p class="text-xs text-slate-500">Persiapan dokumen, perangkat kerja, dan induksi tim</p>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">In Progress</span>
            </div>

            <div class="mt-4 space-y-3">
                @forelse($recentOnboardings as $onb)
                    <div class="p-3.5 rounded-xl border border-emerald-100 bg-emerald-50/30">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-bold text-xs text-slate-900">{{ $onb->candidate?->full_name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $onb->position?->title }}</div>
                            </div>
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">
                                {{ $onb->checklist_percentage }}% Selesai
                            </span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-1.5 mt-3 overflow-hidden">
                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $onb->checklist_percentage }}%"></div>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-2">{{ $onb->notes }}</div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-3 text-center">Belum ada kandidat dalam tahap onboarding.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
