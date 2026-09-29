@php
    $appName = $settings['app_name'] ?? \App\Models\SystemSetting::get('app_name', config('app.name', 'RecruitPro'));
    $appTagline = $settings['app_tagline'] ?? \App\Models\SystemSetting::get('app_tagline', 'Platform Rekrutmen & Penilaian Talenta Terpadu');
    $appVersion = $settings['app_version'] ?? \App\Models\SystemSetting::get('app_version', '2.5');
    $companyName = $settings['company_name'] ?? \App\Models\SystemSetting::get('company_name', 'PT Rekrutmen Indonesia');
    $footerText = $settings['footer_text'] ?? \App\Models\SystemSetting::get('footer_text', 'Sistem Rekrutmen & Karir • All Rights Reserved');
    $appLogo = $settings['app_logo'] ?? \App\Models\SystemSetting::get('app_logo');
    $companyAddress = $settings['company_address'] ?? \App\Models\SystemSetting::get('company_address', 'Cyber 2 Tower Lt. 18, Jl. HR Rasuna Said, Jakarta');
    $companyEmail = $settings['company_email'] ?? \App\Models\SystemSetting::get('company_email', 'talent@recruitment.test');
    $companyPhone = $settings['company_phone'] ?? \App\Models\SystemSetting::get('company_phone', '+62 21 555-0199');
    $companyCity = $settings['company_city'] ?? \App\Models\SystemSetting::get('company_city', 'Jakarta');

    $isLoggedIn = auth()->check();
    $authUser = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $appName }} - Portal Karir & Rekrutmen Resmi {{ $companyName }}</title>
    <meta name="description"
        content="{{ $appTagline }}. Temukan peluang kerja terbaik, ikuti asesmen online, dan bergabunglah bersama {{ $companyName }}.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind & App Assets via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .glow-aura {
            background: radial-gradient(circle at 50% 50%, rgba(99, 102, 241, 0.15), transparent 70%);
        }
    </style>
</head>

<body
    class="bg-slate-50 text-slate-900 antialiased selection:bg-indigo-500 selection:text-white flex flex-col min-h-screen">

    <!-- Top Announcement Bar -->
    <div class="bg-indigo-950 text-indigo-200 text-xs py-2 px-4 border-b border-indigo-900/60 hidden sm:block">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                    INFO
                </span>
                <span>Pendaftaran Recruitment <strong>{{ $companyName }}</strong> • </span>
            </div>
            <div class="flex items-center gap-4 text-[11px] text-indigo-300">
                <span>Hubungi HR: {{ $companyEmail }}</span>
                <span class="text-indigo-600">•</span>
                <span>{{ $companyPhone }}</span>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                @if($appLogo && file_exists(public_path('storage/' . $appLogo)))
                    <img src="{{ asset('storage/' . $appLogo) }}" alt="{{ $appName }}" class="h-10 w-auto object-contain">
                @else
                    <div
                        class="w-11 h-11 rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 text-white flex items-center justify-center font-black text-xl shadow-md shadow-indigo-600/30 group-hover:scale-105 transition transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                @endif
                <div>
                    <span
                        class="text-lg font-black text-slate-900 tracking-tight block leading-tight group-hover:text-indigo-600 transition">
                        {{ $appName }}
                    </span>
                    <span class="text-[10px] text-slate-500 font-semibold tracking-wider uppercase block">
                        {{ $companyName }}
                    </span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-7 text-xs font-bold text-slate-600">
                <a href="#hero" class="hover:text-indigo-600 transition">Beranda</a>
                <a href="{{ route('public.lowongan.index') }}" class="text-indigo-600 font-extrabold hover:text-indigo-700 transition flex items-center gap-1.5">
                    <span>Lowongan</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-indigo-100 text-indigo-700">Baru</span>
                </a>
                <a href="#departemen" class="hover:text-indigo-600 transition">Departemen</a>
                <a href="#alur-seleksi" class="hover:text-indigo-600 transition">Alur Seleksi</a>
                <a href="#keunggulan" class="hover:text-indigo-600 transition">Keunggulan</a>
            </nav>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2.5">
                <!-- Cek Status NIK Quick Button -->
                <button type="button" onclick="openNikCheckModal()"
                    class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300/90 font-bold text-xs shadow-xs transition">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                        </path>
                    </svg>
                    <span class="hidden sm:inline">Cek Status (NIK)</span>
                    <span class="sm:hidden">Cek NIK</span>
                </button>

                @if($isLoggedIn)
                    <div class="hidden sm:flex items-center gap-2 mr-1">
                        <div
                            class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center">
                            {{ strtoupper(substr($authUser->name, 0, 2)) }}
                        </div>
                        <div class="text-left text-xs">
                            <div class="font-bold text-slate-900 truncate max-w-[120px]">{{ $authUser->name }}</div>
                            <span
                                class="text-[10px] text-slate-500 capitalize">{{ $authUser->role?->display_name ?? 'User' }}</span>
                        </div>
                    </div>

                    <a href="{{ route('home') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Keluar"
                            class="p-2 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 rounded-xl text-slate-700 hover:text-indigo-600 hover:bg-slate-100 font-bold text-xs transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                        <span>Daftar Akun</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="grow">

        <!-- Hero Section -->
        <section id="hero"
            class="relative overflow-hidden text-white py-16 sm:py-24 lg:py-28">
            <!-- Background: Gedung Photo -->
            <div class="absolute inset-0 pointer-events-none">
                <img src="{{ asset('gedung.jpg') }}"
                     alt="Gedung Perusahaan"
                     class="w-full h-full object-cover object-center"
                     loading="eager">
                <!-- Dark gradient overlay for text legibility -->
                <div class="absolute inset-0"
                     style="background: linear-gradient(160deg, rgba(10,14,40,0.82) 0%, rgba(30,27,90,0.75) 45%, rgba(10,14,40,0.88) 100%);"></div>
                <!-- Bottom fade to blend with next section -->
                <div class="absolute bottom-0 inset-x-0 h-32"
                     style="background: linear-gradient(to bottom, transparent, rgba(248,250,252,0.15));"></div>
            </div>
            <!-- Subtle accent glow on top of photo -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-purple-600/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-600/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto space-y-5">
                    <!-- Top Pill -->
                    {{-- <div
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/10 border border-white/15 text-indigo-300 text-xs font-semibold backdrop-blur-md shadow-inner">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Portal Karir Resmi <strong>{{ $companyName }}</strong></span>
                    </div> --}}

                    <!-- Main Heading -->
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white leading-tight">
                        Temukan Karir Impianmu & Tumbuh Bersama Kami
                    </h1>

                    <!-- Subtitle / Tagline from Config -->
                    <p class="text-sm sm:text-base text-slate-300 leading-relaxed max-w-2xl mx-auto">
                        {{ $appTagline }}. Proses seleksi transparan, terpadu dengan asesmen kompetensi online dan
                        wawancara digital terverifikasi NIK tunggal.
                    </p>

                    <!-- Search Bar Form -->
                    <div class="pt-4 max-w-2xl mx-auto">
                        <form onsubmit="event.preventDefault(); filterVacanciesFromHero();"
                            class="p-2 sm:p-2.5 rounded-2xl bg-white/10 border border-white/20 backdrop-blur-lg shadow-2xl flex flex-col sm:flex-row gap-2">
                            <div class="relative grow">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" id="heroSearchInput"
                                    placeholder="Cari posisi pekerjaan / keahlian..."
                                    class="w-full pl-10 pr-3 py-2.5 rounded-xl bg-white text-slate-900 placeholder-slate-400 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div class="sm:w-48">
                                <select id="heroDepartmentSelect"
                                    class="w-full px-3 py-2.5 rounded-xl bg-white text-slate-900 text-xs font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Semua Departemen</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->name }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-md shadow-indigo-600/40 transition flex items-center justify-center gap-1.5 shrink-0">
                                <span>Cari Lowongan</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Quick NIK Status Check Callout in Hero -->
                    <div class="pt-3 max-w-2xl mx-auto">
                        <div
                            class="inline-flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 rounded-2xl bg-amber-500/15 border border-amber-400/30 text-white text-xs backdrop-blur-md w-full shadow-lg transition">
                            <div class="flex items-center gap-3 text-left">
                                <span
                                    class="w-9 h-9 rounded-xl bg-amber-400/20 text-amber-300 flex items-center justify-center shrink-0 border border-amber-400/30">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                                        </path>
                                    </svg>
                                </span>
                                <div>
                                    <p class="font-bold text-amber-300 text-xs sm:text-[13px] leading-snug">
                                        Anda sudah pernah daftar? Tapi ribet untuk cek status harus login?
                                    </p>
                                    <p class="text-[11px] text-slate-300 leading-tight mt-0.5">
                                        Cek proses seleksi & pengumuman cukup melalui 16 digit NIK saja!
                                    </p>
                                </div>
                            </div>
                            <button type="button" onclick="openNikCheckModal()"
                                class="shrink-0 w-full sm:w-auto px-4 py-2 rounded-xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black text-xs shadow-md shadow-amber-500/20 transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>Cek Melalui NIK Saja</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Quick Stats from Database -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-8 max-w-4xl mx-auto">
                        <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs text-center">
                            <div class="text-2xl sm:text-3xl font-black text-indigo-400">{{ $stats['total_jobs'] }}
                            </div>
                            <span class="text-[11px] text-slate-400 font-medium">Lowongan Aktif</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs text-center">
                            <div class="text-2xl sm:text-3xl font-black text-purple-400">
                                {{ $stats['total_departments'] }}
                            </div>
                            <span class="text-[11px] text-slate-400 font-medium">Departemen Bidang</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs text-center">
                            <div class="text-2xl sm:text-3xl font-black text-emerald-400">
                                {{ number_format($stats['total_candidates']) }}
                            </div>
                            <span class="text-[11px] text-slate-400 font-medium">Kandidat Terdaftar</span>
                        </div>
                        <div class="p-3.5 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-xs text-center">
                            <div class="text-2xl sm:text-3xl font-black text-amber-400">100%</div>
                            <span class="text-[11px] text-slate-400 font-medium">Online & Terverifikasi</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Departments Section (From Database) -->
        <section id="departemen" class="py-16 sm:py-20 bg-white border-b border-slate-200/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 block mb-1">Kategori
                            Spesialisasi</span>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Eksplorasi Bidang
                            Pekerjaan</h2>
                        <p class="text-xs text-slate-500 mt-1 max-w-xl">Pilih departemen yang sesuai dengan minat,
                            kompetensi, dan latar belakang profesional Anda.</p>
                    </div>
                    <a href="#lowongan"
                        class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 self-start sm:self-auto transition">
                        <span>Lihat Semua Lowongan</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @forelse($departments as $dept)
                        @php
                            $pCount = $dept->positions_count ?? 0;
                        @endphp
                        <button type="button" onclick="selectDepartmentFilter('{{ addslashes($dept->name) }}')"
                            class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-indigo-300 hover:shadow-md hover:shadow-indigo-500/5 transition text-left group flex flex-col justify-between h-36">
                            <div class="flex items-center justify-between">
                                <div
                                    class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-sm group-hover:bg-indigo-600 group-hover:text-white transition">
                                    {{ strtoupper(substr($dept->name, 0, 2)) }}
                                </div>
                                <span
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-200 text-slate-700 group-hover:bg-indigo-50 group-hover:text-indigo-700 transition">
                                    {{ $pCount }} Posisi
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition truncate"
                                    title="{{ $dept->name }}">
                                    {{ $dept->name }}
                                </h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Klik untuk memfilter</p>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full py-8 text-center text-slate-400 text-xs">
                            Departemen belum terkonfigurasi.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Vacancies List Section (From Database) -->
        <section id="lowongan" class="py-16 sm:py-20 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 block mb-1">Lowongan
                            Tersedia</span>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Posisi Pekerjaan
                            Terbuka</h2>
                        <p class="text-xs text-slate-500 mt-1 max-w-xl">Bergabunglah dengan tim kami di
                            {{ $companyName }}. Ajukan lamaran Anda secara langsung melalui platform ini.
                        </p>
                    </div>

                    <!-- Search Filter Controls -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <div class="relative">
                            <input type="text" id="liveSearchInput" onkeyup="filterVacanciesList()"
                                placeholder="Filter judul / lokasi..."
                                class="pl-8 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 w-52">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>

                        <select id="typeFilterSelect" onchange="filterVacanciesList()"
                            class="px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <option value="">Semua Tipe</option>
                            <option value="Full-time">Full-time</option>
                            <option value="Contract">Contract</option>
                            <option value="Part-time">Part-time</option>
                            <option value="Internship">Internship</option>
                        </select>
                    </div>
                </div>

                <!-- Vacancies Grid -->
                <div id="vacanciesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @forelse($featuredPositions as $pos)
                        <div class="vacancy-card bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-indigo-300 transition duration-200 p-6 flex flex-col justify-between group"
                            data-title="{{ strtolower($pos->title) }}" data-dept="{{ strtolower($pos->department) }}"
                            data-location="{{ strtolower($pos->location) }}" data-type="{{ strtolower($pos->type) }}">
                            <div>
                                <!-- Top Tags -->
                                <div class="flex items-center justify-between gap-2 pb-3 border-b border-slate-100">
                                    <span
                                        class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $pos->department }}
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        @if($pos->is_internal)
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-100 text-purple-800 border border-purple-200">
                                                Internal
                                            </span>
                                        @endif
                                        <span
                                            class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                            {{ $pos->type }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Title -->
                                <h3
                                    class="text-base font-bold text-slate-900 mt-3 group-hover:text-indigo-600 transition line-clamp-2">
                                    {{ $pos->title }}
                                </h3>

                                <!-- Location & Experience Level -->
                                <div class="mt-2 space-y-1 text-xs text-slate-500">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span class="truncate">{{ $pos->location }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>Level: {{ $pos->experience_level }}</span>
                                    </div>
                                </div>

                                <!-- Salary & Quota Pill -->
                                <div
                                    class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block">Kisaran Gaji</span>
                                        <span class="font-bold text-slate-900 text-xs">
                                            Rp {{ number_format($pos->salary_min, 0, ',', '.') }} -
                                            {{ number_format($pos->salary_max, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] text-slate-400 font-semibold block">Kuota Pelamar</span>
                                        <span class="font-bold text-indigo-700 text-xs">
                                            {{ $pos->applications_count }} / {{ $pos->quota }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer & Action -->
                            <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                <span class="text-[11px] text-slate-400">
                                    @if($pos->deadline)
                                        Batas: {{ \Carbon\Carbon::parse($pos->deadline)->format('d M Y') }}
                                    @else
                                        Buka Terus
                                    @endif
                                </span>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('public.lowongan.show', encrypt($pos->id)) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                        <span>Detail</span>
                                    </a>
                                    @if($isLoggedIn && $authUser->role?->name === 'kandidat')
                                        <a href="{{ route('kadidat.positions.show', $pos->id) }}"
                                            class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition">
                                            <span>Lamar</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" title="Masuk dengan akun untuk mengajukan lamaran"
                                            class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs shadow-xs transition">
                                            <span>Lamar</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full py-16 text-center text-slate-400 bg-white rounded-2xl border border-slate-200">
                            <p class="text-sm font-semibold text-slate-700">Saat ini belum ada lowongan pekerjaan yang
                                dibuka.</p>
                            <p class="text-xs text-slate-400 mt-1">Silakan kunjungi kembali halaman ini secara berkala.</p>
                        </div>
                    @endforelse
                </div>

                <!-- View All Lowongan Button -->
                <div class="text-center pt-4">
                    <a href="{{ route('public.lowongan.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md shadow-indigo-600/30 transition">
                        <span>Lihat Semua Lowongan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </section>

        <!-- Recruitment Flow / Alur Seleksi -->
        <section id="alur-seleksi" class="py-16 sm:py-20 bg-white border-b border-slate-200/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 block mb-1">Tahapan
                        Rekrutmen</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">4 Langkah Mudah Melamar
                        Pekerjaan</h2>
                    <p class="text-xs text-slate-500 mt-1">Sistem kami dirancang agar proses seleksi berjalan objektif,
                        terukur, dan transparan.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Step 1 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 relative">
                        <div
                            class="w-10 h-10 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-sm shadow-md shadow-indigo-600/30">
                            1
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Registrasi Akun (1 NIK)</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Daftarkan akun pelamar menggunakan Nomor Induk Kependudukan (NIK KTP) resmi. 1 kandidat
                            terhubung dengan 1 akun identitas tunggal.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 relative">
                        <div
                            class="w-10 h-10 rounded-2xl bg-purple-600 text-white flex items-center justify-center font-black text-sm shadow-md shadow-purple-600/30">
                            2
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Lengkapi Profil CV & Lamar</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Isi riwayat pendidikan, pengalaman kerja, keahlian teknis, dan portofolio Anda, lalu pilih
                            lowongan pekerjaan yang diinginkan.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 relative">
                        <div
                            class="w-10 h-10 rounded-2xl bg-amber-500 text-white flex items-center justify-center font-black text-sm shadow-md shadow-amber-500/30">
                            3
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Asesmen & Wawancara Online</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Kerjakan tes asesmen kompetensi online dan hadiri sesi wawancara daring melalui link virtual
                            unik yang digenerate sistem.
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3 relative">
                        <div
                            class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center font-black text-sm shadow-md shadow-emerald-600/30">
                            4
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Offering & Onboarding</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Terima Surat Penawaran Kerja (Offering Letter) resmi dari HR, tanda tangani secara digital,
                            dan lengkapi berkas onboarding.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Company Values / Keunggulan -->
        <section id="keunggulan" class="py-16 sm:py-20 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-12">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 block mb-1">Nilai & Budaya
                        Kerja</span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Mengapa Memilih
                        {{ $companyName }}?
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">Kami menyediakan lingkungan kerja yang dinamis, apresiatif,
                        dan mendukung perkembangan potensi Anda.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                            🚀
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Percepatan Jenjang Karir</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Jalur promosi yang transparan berbasis kinerja dan kompetensi nyata, bukan sekadar
                            senioritas.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                            💰
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Kompensasi Kompetitif</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Paket remunerasi menarik, BPJS lengkap, asuransi kesehatan swasta, dan bonus kinerja
                            berkala.
                        </p>
                    </div>

                    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold">
                            💻
                        </div>
                        <h3 class="text-sm font-bold text-slate-900">Fleksibilitas Kerja Modern</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Mendukung skema kerja hibrida (hybrid/remote) dengan perangkat kerja mumpuni dan tunjangan
                            komunikasi.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action Banner -->
        <section
            class="py-16 bg-gradient-to-r from-indigo-900 via-indigo-800 to-purple-900 text-white relative overflow-hidden">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-5 relative z-10">
                <h2 class="text-2xl sm:text-4xl font-black tracking-tight">Siap Memulai Perjalanan Karir Baru Anda?</h2>
                <p class="text-xs sm:text-sm text-indigo-200 max-w-xl mx-auto leading-relaxed">
                    Daftarkan akun kandidat Anda sekarang dalam 2 menit. Wujudkan aspirasi karir bersama
                    <strong>{{ $companyName }}</strong>.
                </p>
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                    @if(!$isLoggedIn)
                        <a href="{{ route('register') }}"
                            class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white text-indigo-900 font-black text-xs hover:bg-indigo-50 shadow-xl transition">
                            Daftar Akun Kandidat (Gratis)
                        </a>
                        <a href="{{ route('login') }}"
                            class="w-full sm:w-auto px-6 py-3 rounded-xl bg-indigo-700/60 hover:bg-indigo-700 border border-indigo-500 text-white font-bold text-xs transition">
                            Sudah Punya Akun? Masuk
                        </a>
                    @else
                        <a href="{{ route('home') }}"
                            class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white text-indigo-900 font-black text-xs hover:bg-indigo-50 shadow-xl transition">
                            Masuk ke Dashboard Anda
                        </a>
                    @endif
                </div>
            </div>
        </section>

    </main>

    <!-- Footer Section -->
    <footer class="bg-slate-950 text-slate-400 text-xs pt-16 pb-12 border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-12 border-b border-slate-900">
                <!-- Col 1: Brand Info -->
                <div class="space-y-3 md:col-span-2">
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-base font-extrabold text-white">{{ $appName }}</span>
                        <span
                            class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-900 text-indigo-300">v{{ $appVersion }}</span>
                    </div>
                    <p class="text-xs text-slate-400 max-w-sm leading-relaxed">
                        {{ $appTagline }}. Dikelola langsung oleh Departemen Human Resources
                        <strong>{{ $companyName }}</strong>.
                    </p>
                    <div class="text-[11px] text-slate-500 pt-2 space-y-1">
                        <div>Lokasi: {{ $companyAddress }} ({{ $companyCity }})</div>
                        <div>Email HR: <a href="mailto:{{ $companyEmail }}"
                                class="text-indigo-400 hover:underline">{{ $companyEmail }}</a></div>
                        <div>Telepon: {{ $companyPhone }}</div>
                    </div>
                </div>

                <!-- Col 2: Navigasi Cepat -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300">Navigasi Portal</h4>
                    <ul class="space-y-2">
                        <li><a href="#hero" class="hover:text-white transition">Beranda Utama</a></li>
                        <li><a href="#lowongan" class="hover:text-white transition">Daftar Lowongan Aktif</a></li>
                        <li><a href="#departemen" class="hover:text-white transition">Kategori Departemen</a></li>
                        <li><a href="#alur-seleksi" class="hover:text-white transition">Alur & Syarat Melamar</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Portal Masuk Karyawan &
                                HR</a></li>
                    </ul>
                </div>

                <!-- Col 3: Keamanan & Kebijakan -->
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300">Integritas Rekrutmen</h4>
                    <p class="text-[11px] text-slate-500 leading-relaxed">
                        Proses seleksi di {{ $companyName }} <strong>tidak memungut biaya apapun</strong>. Waspadai
                        segala bentuk penipuan yang mengatasnamakan tim rekrutmen kami.
                    </p>
                    <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 text-[11px] text-slate-400">
                        🛡️ Verifikasi NIK resmi 1 akun 1 kandidat untuk integritas data.
                    </div>
                </div>
            </div>

            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500">
                <p>&copy; {{ date('Y') }} {{ $companyName }}. {{ $footerText }}</p>
                <div class="flex items-center gap-4">
                    <span>Powered by {{ $appName }}</span>
                    <span>•</span>
                    <span>Laravel {{ app()->version() }}</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating NIK Quick Check Pop-up Widget -->
    <div id="nikPopupWidget"
        class="fixed bottom-5 right-5 z-40 max-w-sm sm:max-w-md w-[calc(100%-2.5rem)] bg-white/95 backdrop-blur-md rounded-2xl border border-indigo-200 shadow-2xl p-4 sm:p-5 transition-all duration-300 transform translate-y-0 shadow-indigo-950/20">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-2">
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                    QUICK CHECK
                </span>
                <span class="text-[11px] font-semibold text-slate-500">Cek Cepat Status Pelamar</span>
            </div>
            <button type="button" onclick="dismissNikPopupWidget()" title="Tutup Widget"
                class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        <div class="mt-2.5 flex items-start gap-3">
            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 text-white flex items-center justify-center shrink-0 shadow-md shadow-indigo-500/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                    </path>
                </svg>
            </div>
            <div>
                <h4 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">
                    Anda sudah pernah daftar? Tapi ribet untuk cek status harus login?
                </h4>
                <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">
                    Cek melalui NIK saja! Pantau langsung tahapan seleksi berkas, asesmen, wawancara, hingga offering
                    secara instan.
                </p>
            </div>
        </div>

        <div class="mt-3.5 flex items-center gap-2">
            <button type="button" onclick="openNikCheckModal()"
                class="grow py-2.5 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Cek Melalui NIK Saja</span>
            </button>
            <button type="button" onclick="dismissNikPopupWidget()"
                class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition cursor-pointer">
                Nanti
            </button>
        </div>
    </div>

    <!-- Minimized Floating Button (shown when widget is closed) -->
    <button id="nikFloatingBtn" onclick="openNikCheckModal()"
        class="fixed bottom-5 right-5 z-40 hidden items-center gap-2 px-4 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xl shadow-indigo-600/40 transition transform hover:scale-105 border border-indigo-400/30 cursor-pointer">
        <svg class="w-4 h-4 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
            </path>
        </svg>
        <span>Cek Status via NIK</span>
    </button>

    <!-- Interactive NIK Quick Status Modal -->
    <div id="nikCheckModal"
        class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm items-center justify-center p-3 sm:p-4 overflow-y-auto">
        <div
            class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh] my-auto animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-indigo-900 text-white p-5 sm:p-6 relative">
                <button type="button" onclick="closeNikCheckModal()"
                    class="absolute top-4 right-4 p-2 rounded-xl bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <div class="flex items-center gap-3">
                    <div
                        class="w-11 h-11 rounded-2xl bg-amber-400/20 border border-amber-400/30 text-amber-300 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <div
                            class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-400/20 text-amber-300 border border-amber-400/30 mb-1">
                            Tanpa Perlu Login
                        </div>
                        <h3 class="text-base sm:text-lg font-black tracking-tight text-white">Cek Status Lamaran via NIK
                        </h3>
                    </div>
                </div>
                <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                    Anda sudah pernah daftar? Cukup masukkan 16 digit NIK KTP Anda untuk memantau tahapan proses seleksi
                    secara instan.
                </p>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-5 sm:p-6 overflow-y-auto space-y-4">
                <!-- NIK Search Form -->
                <div>
                    <label for="nikSearchInput"
                        class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nomor Induk Kependudukan (NIK)
                    </label>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <div class="relative grow">
                            <div
                                class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2">
                                    </path>
                                </svg>
                            </div>
                            <input type="text" id="nikSearchInput" placeholder="16 Digit NIK KTP (contoh: 317101...)"
                                maxlength="16" inputmode="numeric"
                                class="w-full pl-10 pr-12 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-500/20 text-xs font-mono tracking-wider font-semibold text-slate-900 transition">
                            <span id="nikCharCounter"
                                class="absolute right-3 top-3 text-[10px] text-slate-400 font-mono">0/16</span>
                        </div>
                        <button type="button" id="nikSearchBtn" onclick="submitNikCheck()"
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition flex items-center justify-center gap-1.5 shrink-0 cursor-pointer">
                            <span id="nikSearchBtnText">Periksa Status</span>
                            <svg id="nikSearchSpinner" class="w-4 h-4 animate-spin hidden" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                        <span>🔒 Privasi terjaga. Nama lengkap pelamar disamarkan demi keamanan data.</span>
                    </p>
                </div>

                <!-- Error Alert Box -->
                <div id="nikErrorAlert"
                    class="hidden p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <div id="nikErrorMessage" class="leading-relaxed"></div>
                </div>

                <!-- Success Results Area -->
                <div id="nikResultArea" class="hidden space-y-3.5">
                    <!-- Candidate Greeting Card -->
                    <div
                        class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center"
                                id="nikCandidateAvatar">
                                KP
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block">Kandidat Terdaftar</span>
                                <h4 class="text-xs font-bold text-slate-900" id="nikCandidateName">-</h4>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            ✓ NIK Terverifikasi
                        </span>
                    </div>

                    <!-- Application Cards List -->
                    <div id="nikApplicationsList" class="space-y-3">
                        <!-- Rendered by JS -->
                    </div>

                    <!-- Offering Special Banner (if applicable) -->
                    <div id="nikOfferingNotice"
                        class="hidden p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs flex items-start gap-2.5">
                        <span class="text-base">🎉</span>
                        <div>
                            <p class="font-bold">Selamat! Anda telah menerima penawaran kerja (Offering Letter).</p>
                            <p class="text-[11px] text-emerald-800 mt-0.5">
                                Untuk melihat detail gaji, tunjangan, dan menanggapi surat penawaran resmi, silakan
                                login ke akun Anda.
                            </p>
                            <a href="{{ route('login') }}"
                                class="inline-flex items-center gap-1 mt-2 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition">
                                <span>Masuk ke Akun Sekarang</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-slate-50 border-t border-slate-100 p-4 px-6 flex items-center justify-between text-xs">
                <span class="text-slate-500 text-[11px]">Belum punya akun? <a href="{{ route('register') }}"
                        class="text-indigo-600 font-bold hover:underline">Daftar sekarang</a></span>
                <button type="button" onclick="closeNikCheckModal()"
                    class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Interactive Search, Filtering, and NIK Quick Check Scripts -->
    <script>
        function filterVacanciesFromHero() {
            const query = document.getElementById('heroSearchInput').value.toLowerCase().trim();
            const dept = document.getElementById('heroDepartmentSelect').value.toLowerCase().trim();

            document.getElementById('liveSearchInput').value = query;

            // Scroll to vacancies
            const target = document.getElementById('lowongan');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }

            filterVacanciesList(dept);
        }

        function selectDepartmentFilter(deptName) {
            document.getElementById('liveSearchInput').value = '';
            const target = document.getElementById('lowongan');
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
            filterVacanciesList(deptName.toLowerCase().trim());
        }

        function filterVacanciesList(filterDeptOverride = null) {
            const searchQuery = document.getElementById('liveSearchInput').value.toLowerCase().trim();
            const selectedType = document.getElementById('typeFilterSelect').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.vacancy-card');

            cards.forEach(card => {
                const title = card.getAttribute('data-title') || '';
                const dept = card.getAttribute('data-dept') || '';
                const location = card.getAttribute('data-location') || '';
                const type = card.getAttribute('data-type') || '';

                const matchesSearch = (!searchQuery || title.includes(searchQuery) || location.includes(searchQuery) || dept.includes(searchQuery));
                const matchesType = (!selectedType || type.includes(selectedType));
                const matchesDeptOverride = (!filterDeptOverride || dept.includes(filterDeptOverride));

                if (matchesSearch && matchesType && matchesDeptOverride) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // ==========================================
        // NIK Quick Status Check Modal & Widget Logic
        // ==========================================
        function openNikCheckModal(defaultNik = '') {
            const modal = document.getElementById('nikCheckModal');
            if (!modal) return;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');

            const input = document.getElementById('nikSearchInput');
            if (defaultNik) {
                input.value = defaultNik;
            }
            updateNikCharCount();
            input.focus();
        }

        function closeNikCheckModal() {
            const modal = document.getElementById('nikCheckModal');
            if (!modal) return;

            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        function dismissNikPopupWidget() {
            const widget = document.getElementById('nikPopupWidget');
            const floatingBtn = document.getElementById('nikFloatingBtn');
            if (widget) {
                widget.classList.add('hidden');
            }
            if (floatingBtn) {
                floatingBtn.classList.remove('hidden');
                floatingBtn.classList.add('inline-flex');
            }
            sessionStorage.setItem('nikWidgetDismissed', 'true');
        }

        function updateNikCharCount() {
            const input = document.getElementById('nikSearchInput');
            const counter = document.getElementById('nikCharCounter');
            if (input && counter) {
                // Ensure only digits
                input.value = input.value.replace(/[^0-9]/g, '');
                counter.textContent = `${input.value.length}/16`;
                if (input.value.length === 16) {
                    counter.classList.add('text-emerald-600', 'font-bold');
                    counter.classList.remove('text-slate-400');
                } else {
                    counter.classList.remove('text-emerald-600', 'font-bold');
                    counter.classList.add('text-slate-400');
                }
            }
        }

        async function submitNikCheck() {
            const input = document.getElementById('nikSearchInput');
            const nik = input.value.trim();
            const btn = document.getElementById('nikSearchBtn');
            const btnText = document.getElementById('nikSearchBtnText');
            const spinner = document.getElementById('nikSearchSpinner');
            const errorAlert = document.getElementById('nikErrorAlert');
            const errorMessage = document.getElementById('nikErrorMessage');
            const resultArea = document.getElementById('nikResultArea');
            const appsList = document.getElementById('nikApplicationsList');
            const candidateName = document.getElementById('nikCandidateName');
            const candidateAvatar = document.getElementById('nikCandidateAvatar');
            const offeringNotice = document.getElementById('nikOfferingNotice');

            errorAlert.classList.add('hidden');
            resultArea.classList.add('hidden');
            offeringNotice.classList.add('hidden');

            if (!nik) {
                errorAlert.classList.remove('hidden');
                errorMessage.textContent = 'Silakan masukkan 16 digit Nomor Induk Kependudukan (NIK) Anda.';
                input.focus();
                return;
            }

            if (nik.length !== 16 || !/^\d{16}$/.test(nik)) {
                errorAlert.classList.remove('hidden');
                errorMessage.textContent = 'Format NIK tidak valid. NIK harus terdiri dari 16 digit angka sesuai KTP Anda.';
                input.focus();
                return;
            }

            // Set loading state
            btn.disabled = true;
            btnText.textContent = 'Memeriksa...';
            spinner.classList.remove('hidden');

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch('{{ route("check-status-nik") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ nik: nik })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    errorAlert.classList.remove('hidden');
                    errorMessage.textContent = data.message || 'Terjadi kesalahan saat memeriksa data. Silakan coba beberapa saat lagi.';
                    return;
                }

                // Render success result
                candidateName.textContent = data.candidate_name || 'Kandidat';
                candidateAvatar.textContent = (data.candidate_name || 'KP').substring(0, 2).toUpperCase();

                appsList.innerHTML = '';

                if (!data.has_applications || data.applications.length === 0) {
                    appsList.innerHTML = `
                        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs text-center space-y-2">
                            <p class="font-bold">Akun Terdaftar, Belum Ada Lamaran Aktif</p>
                            <p class="text-[11px] text-amber-800">
                                Anda telah terdaftar di sistem dengan NIK ini, namun belum mengajukan lamaran pekerjaan untuk posisi apapun.
                            </p>
                            <a href="#lowongan" onclick="closeNikCheckModal()" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition">
                                <span>Lihat Lowongan Tersedia &rarr;</span>
                            </a>
                        </div>
                    `;
                } else {
                    let hasOffering = false;

                    data.applications.forEach(app => {
                        if (app.stage === 'offering') {
                            hasOffering = true;
                        }

                        const card = document.createElement('div');
                        card.className = 'p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs space-y-3';
                        card.innerHTML = `
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h5 class="text-xs sm:text-sm font-bold text-slate-900 leading-snug">${app.position_title}</h5>
                                    <div class="flex flex-wrap items-center gap-2 text-[11px] text-slate-500 mt-0.5">
                                        <span>${app.department}</span>
                                        <span>•</span>
                                        <span>Dilamar: ${app.applied_at}</span>
                                        ${app.is_internal ? '<span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Internal</span>' : ''}
                                    </div>
                                </div>
                                <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border ${app.badge_class}">
                                    ${app.stage_label}
                                </span>
                            </div>

                            <!-- Progress Bar -->
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-[10px] font-bold text-slate-500">
                                    <span>Progres Seleksi</span>
                                    <span>${app.progress}%</span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-600 rounded-full transition-all duration-700" style="width: ${app.progress}%"></div>
                                </div>
                            </div>

                            <!-- Stage Note -->
                            <p class="text-[11px] text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100 leading-relaxed">
                                💡 ${app.note}
                            </p>
                        `;
                        appsList.appendChild(card);
                    });

                    if (hasOffering) {
                        offeringNotice.classList.remove('hidden');
                    }
                }

                resultArea.classList.remove('hidden');

            } catch (err) {
                console.error(err);
                errorAlert.classList.remove('hidden');
                errorMessage.textContent = 'Gagal menghubungi server. Periksa koneksi internet Anda dan coba lagi.';
            } finally {
                btn.disabled = false;
                btnText.textContent = 'Periksa Status';
                spinner.classList.add('hidden');
            }
        }

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function () {
            const nikInput = document.getElementById('nikSearchInput');
            if (nikInput) {
                nikInput.addEventListener('input', updateNikCharCount);
                nikInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        submitNikCheck();
                    }
                });
            }

            // Close modal on Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeNikCheckModal();
                }
            });

            // Close modal on outside click
            const modal = document.getElementById('nikCheckModal');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        closeNikCheckModal();
                    }
                });
            }

            // Check if widget was previously dismissed in session
            if (sessionStorage.getItem('nikWidgetDismissed') === 'true') {
                const widget = document.getElementById('nikPopupWidget');
                const floatingBtn = document.getElementById('nikFloatingBtn');
                if (widget) widget.classList.add('hidden');
                if (floatingBtn) {
                    floatingBtn.classList.remove('hidden');
                    floatingBtn.classList.add('inline-flex');
                }
            }
        });
    </script>
</body>

</html>