<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') - HR Recruitment System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        /* Sidebar slide transition */
        #sidebar {
            transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Overlay fade transition */
        #sidebar-overlay {
            transition: opacity 0.25s ease;
        }

        /* Print Styles: Clean printing without sidebar, header, or footer */
        @media print {
            body, html {
                background: #ffffff !important;
                color: #0f172a !important;
                height: auto !important;
                overflow: visible !important;
            }
            #sidebar,
            #sidebar-overlay,
            header,
            footer,
            .no-print,
            [data-no-print] {
                display: none !important;
            }
            .flex.h-full {
                display: block !important;
                height: auto !important;
            }
            .flex-1 {
                overflow: visible !important;
                display: block !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-100 text-slate-800 antialiased">

    {{-- ========================================================== --}}
    {{-- MOBILE OVERLAY: Darkens content when sidebar is open       --}}
    {{-- ========================================================== --}}
    <div id="sidebar-overlay"
         class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm opacity-0 pointer-events-none lg:hidden"
         onclick="closeSidebar()">
    </div>

    {{-- ========================================================== --}}
    {{-- WRAPPER: Full height flex row                              --}}
    {{-- ========================================================== --}}
    <div class="flex h-full">

        {{-- ====================================================== --}}
        {{-- LEFT SIDEBAR                                           --}}
        {{-- Mobile: fixed drawer sliding from the left            --}}
        {{-- Desktop: static column always visible                  --}}
        {{-- ====================================================== --}}
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-50 w-72
                      -translate-x-full lg:translate-x-0
                      lg:static lg:inset-auto lg:z-auto
                      bg-slate-950 text-slate-300
                      border-r border-slate-800
                      flex flex-col shrink-0
                      h-full overflow-hidden">

            {{-- Sidebar Brand / Logo --}}
            @php
                $sysName = \App\Models\SystemSetting::get('app_name', 'SisRec');
                $sysVersion = \App\Models\SystemSetting::get('app_version', '2.5');
                $sysTagline = \App\Models\SystemSetting::get('app_tagline', 'Sistem Rekrutmen');
                $sysLogo = \App\Models\SystemSetting::get('app_logo');
            @endphp
            <div class="h-16 px-6 flex items-center gap-3 border-b border-slate-800/80 bg-slate-950/50 shrink-0">
                @if($sysLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($sysLogo))
                    <img src="{{ asset('storage/' . $sysLogo) }}" alt="{{ $sysName }}" class="h-10 w-10 rounded-xl object-contain bg-slate-900 border border-slate-800 p-1 shrink-0">
                @else
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-base tracking-tight bg-gradient-to-r from-white via-slate-100 to-slate-300 bg-clip-text text-transparent">{{ $sysName }}</span>
                        <span class="text-[9px] font-bold tracking-wider uppercase px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">{{ $sysVersion }}</span>
                    </div>
                    <p class="text-[10px] text-slate-400 truncate">{{ $sysTagline }}</p>
                </div>
                {{-- Close button (mobile only) --}}
                <button id="sidebar-close-btn"
                        onclick="closeSidebar()"
                        class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition shrink-0"
                        aria-label="Tutup menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- User Profile Mini-Card in Sidebar --}}
            @auth
                @php
                    $currentUser = auth()->user();
                    $userRole = $currentUser?->role?->name ?? 'guest';
                    $candidateData = null;
                    $candidateProfileData = null;
                    $candidateLatestApp = null;
                    $candidateAppCode = null;

                  if ($userRole === 'kandidat' && $currentUser) {
    $candidateData = $currentUser->candidate ?? \App\Models\Candidate::where('user_id', $currentUser->id)->first();
    if ($candidateData) {
        $candidateProfileData = $candidateData->profile;
        $candidateLatestApp = $candidateData->applications()->latest()->first();
        if ($candidateLatestApp) {
            $candidateAppCode = 'CADINEW-' . str_pad((string) $candidateLatestApp->id, 5, '0', STR_PAD_LEFT);
            
            // Format tanggal (pilih salah satu sesuai kebutuhan):
            // 1. Format standar tanggal-bulan-tahun (contoh: 19-09-2026)
            $candidateAppDate = $candidateLatestApp->created_at->format('d-m-Y');

            // 2. Format lengkap bahasa Indonesia (contoh: 19 September 2026)
            $candidateAppDateIndo = $candidateLatestApp->created_at->translatedFormat('d F Y');
        }
    }
}
@endphp

                <div class="px-3 pt-3 shrink-0">
                    @if($userRole === 'kandidat')
                        {{-- Candidate Profile & ID Lamaran Card above Sidebar Menu --}}
                        <div class="p-3.5 rounded-2xl bg-gradient-to-b from-slate-900 via-slate-900 to-blue-950/40 border border-blue-800/40 shadow-lg relative overflow-hidden group">
                            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-500/10 rounded-full blur-xl pointer-events-none"></div>

                            <div class="flex items-center gap-3 relative z-10">
                                @if($candidateProfileData?->photo)
                                    <img src="{{ asset('storage/' . $candidateProfileData->photo) }}"
                                         alt="Foto Profil {{ $candidateData?->full_name ?? $currentUser->name }}"
                                         class="h-11 w-11 rounded-xl object-cover ring-2 ring-blue-500/50 shadow-md shrink-0">
                                @else
                                    <div class="h-11 w-11 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-500 flex items-center justify-center font-bold text-sm text-white shadow-md shadow-blue-500/20 shrink-0 ring-2 ring-blue-400/40">
                                        {{ strtoupper(substr($candidateData?->first_name ?? $currentUser->name, 0, 1) . substr($candidateData?->last_name ?? '', 0, 1)) ?: 'KD' }}
                                    </div>
                                @endif

                                <div class="overflow-hidden min-w-0 flex-1">
                                    <div class="text-xs font-bold text-white truncate group-hover:text-blue-300 transition">
                                        {{ $candidateData?->full_name ?: $currentUser->name }}
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        @if($candidateAppCode)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-blue-500/20 text-blue-300 border border-blue-500/30 text-[10px] font-mono font-bold tracking-tight" title="ID Lamaran Terbaru Anda">
                                                <svg class="w-3 h-3 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <span>{{ $candidateAppCode }}</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-800 text-slate-400 border border-slate-700 text-[9px] font-medium">
                                                Belum Ada Lamaran
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2.5 pt-2 border-t border-slate-800/80 flex items-center justify-between text-[10px] relative z-10">
                                <span class="text-slate-400 font-medium">Status Lamaran:</span>
                                @if($candidateLatestApp)
                                    <span class="text-emerald-400 font-bold flex items-center gap-1 capitalize">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        {{ $candidateLatestApp->stage }}
                                    </span>
                                @else
                                    <span class="text-slate-400 font-medium">Pelamar Baru</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800 flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-800 flex items-center justify-center font-bold text-xs text-white shadow-inner shrink-0 ring-2 ring-indigo-500/40">
                                {{ strtoupper(substr($currentUser->name, 0, 2)) }}
                            </div>
                            <div class="overflow-hidden min-w-0">
                                <div class="text-xs font-bold text-white truncate">{{ $currentUser->name }}</div>
                                <span class="inline-block text-[10px] font-semibold text-indigo-400 capitalize bg-indigo-500/10 px-2 py-0.5 rounded-full border border-indigo-500/20 mt-0.5">
                                    {{ $currentUser->role?->display_name ?? 'Pengguna' }}
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            @endauth

            {{-- Role-Specific Sidebar Navigation Links --}}
            <div class="flex-1 min-h-0 overflow-y-auto px-3 py-3 space-y-6 text-xs">

                {{-- ============================================== --}}
                {{-- MENU ADMIN: Khusus Role Administrator           --}}
                {{-- ============================================== --}}
                @if($userRole === 'admin')
                    <div>
                        <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Menu Utama Admin</div>
                        <nav class="space-y-1">
                            <a href="{{ route('admin.dashboard') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span>Dashboard Overview</span>
                            </a>

                            <a href="{{ route('admin.users.create') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                <span>Tambah Pengguna (User)</span>
                            </a>

                            <a href="{{ route('admin.positions.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ (request()->routeIs('admin.positions.index') || request()->routeIs('admin.positions.show')) ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <span>Daftar Lowongan Kerja</span>
                            </a>

                            <a href="{{ route('admin.positions.create') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.positions.create') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Buat Lowongan Kerja</span>
                            </a>

                            <a href="{{ route('admin.applications.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.applications.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Daftar Pelamar</span>
                            </a>

                            <a href="{{ route('admin.interviews.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.interviews.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Wawancara Online</span>
                            </a>

                            <a href="{{ route('admin.assessments.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.assessments.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <span>Assessment &amp; Bank Soal</span>
                            </a>

                            <a href="{{ route('admin.offerings.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.offerings.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Offering Kerja</span>
                            </a>

                            <a href="{{ route('admin.onboardings.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.onboardings.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>Onboarding Karyawan</span>
                            </a>
                        </nav>
                    </div>

                    <div>
                        <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Data & Keamanan</div>
                        <nav class="space-y-1">
                            <a href="{{ route('admin.users.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <span>Daftar Pengguna Sistem</span>
                            </a>

                            <a href="{{ route('admin.dashboard') }}#roles" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-slate-400 hover:text-white hover:bg-slate-900 transition">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <span>Roles & Hak Akses</span>
                            </a>

                            <a href="{{ route('admin.dashboard') }}#logs" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-slate-400 hover:text-white hover:bg-slate-900 transition">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Log Audit & Aktivitas</span>
                            </a>

                            <a href="{{ route('admin.config.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('admin.config.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Konfigurasi Sistem</span>
                            </a>
                        </nav>
                    </div>
                @endif

                {{-- ============================================== --}}
                {{-- MENU HR: Khusus Role HR & Talent Acquisition   --}}
                {{-- ============================================== --}}
                @if($userRole === 'hr')
                    <div>
                        <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Menu HR Recruiter</div>
                        <nav class="space-y-1">
                            <a href="{{ route('hr.dashboard') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('hr.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span>Pipeline Rekrutmen</span>
                            </a>

                            <a href="{{ route('hr.positions.create') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('hr.positions.create') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Pasang Lowongan Baru</span>
                            </a>

                            <a href="{{ route('hr.positions.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ (request()->routeIs('hr.positions.index') || request()->routeIs('hr.positions.show')) ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>Daftar Lowongan Aktif</span>
                            </a>

                            <a href="{{ route('hr.applications.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('hr.applications.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Daftar Pelamar</span>
                            </a>

                            <a href="{{ route('hr.interviews.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('hr.interviews.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Wawancara Online</span>
                            </a>

                            <a href="{{ route('hr.assessments.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('hr.assessments.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <span>Assessment &amp; Bank Soal</span>
                            </a>

                            <a href="{{ route('hr.offerings.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('hr.offerings.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Offering Kerja</span>
                            </a>

                            <a href="{{ route('hr.onboardings.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('hr.onboardings.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>Onboarding Karyawan</span>
                            </a>
                        </nav>
                    </div>
                @endif

                {{-- ============================================== --}}
                {{-- MENU INTERVIEWER: Khusus User Interviewer       --}}
                {{-- ============================================== --}}
                @if($userRole === 'interviewer')
                    <div>
                        <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Menu Interviewer</div>
                        <nav class="space-y-1">
                            <a href="{{ route('interview.dashboard') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('interview.dashboard') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span>Portal Wawancara</span>
                            </a>

                            <a href="{{ route('interview.interviews.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('interview.interviews.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                <span>Sesi Wawancara Online</span>
                            </a>

                            <a href="{{ route('interview.applications.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('interview.applications.*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>Daftar Seluruh Pelamar</span>
                            </a>
                        </nav>
                    </div>
                @endif

                {{-- ============================================== --}}
                {{-- MENU KANDIDAT: Khusus Pelamar / Job Seeker      --}}
                {{-- ============================================== --}}
                @if($userRole === 'kandidat')
                    <div>
                        <div class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Menu Pelamar</div>
                        <nav class="space-y-1">
                            <a href="{{ route('kadidat.dashboard') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('kadidat.dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span>Status Lamaran Saya</span>
                            </a>

                            <a href="{{ route('kadidat.profile.edit') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('kadidat.profile.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Profil & Resume CV</span>
                            </a>

                            <a href="{{ route('kadidat.positions.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('kadidat.positions.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>Jelajahi Lowongan Lainnya</span>
                            </a>

                            <a href="{{ route('kadidat.assessments.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('kadidat.assessments.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                </svg>
                                <span>Tes Online (Assessment)</span>
                            </a>

                            <a href="{{ route('kadidat.offerings.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('kadidat.offerings.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Surat Penawaran (Offering)</span>
                            </a>

                            <a href="{{ route('kadidat.onboarding.index') }}" onclick="closeSidebar()"
                               class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold transition {{ request()->routeIs('kadidat.onboarding.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' : 'text-slate-400 hover:text-white hover:bg-slate-900' }}">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>Proses Onboarding</span>
                            </a>
                        </nav>
                    </div>
                @endif

            </div>

            {{-- Sidebar Bottom Footer --}}
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/70 text-xs shrink-0">
                @auth
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-slate-900 hover:bg-rose-950/50 text-slate-400 hover:text-rose-400 border border-slate-800 hover:border-rose-900/40 font-semibold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Keluar (Logout)</span>
                        </button>
                    </form>
                @endauth
                <div class="mt-3 text-[10px] text-slate-400 text-center">
                   Sistem Recrutment V 2.1 &bull; 
                </div>
            </div>
        </aside>

        {{-- ====================================================== --}}
        {{-- MAIN CONTENT WRAPPER                                   --}}
        {{-- ====================================================== --}}
        <div class="flex-1 flex flex-col min-w-0 min-h-0 overflow-y-auto">

            {{-- Top Bar --}}
            <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 flex items-center justify-between shadow-sm sticky top-0 z-30 shrink-0">

                {{-- Left: Hamburger (mobile) + Role Badge + Page Title --}}
                <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                    {{-- Hamburger button – mobile only --}}
                    <button id="sidebar-open-btn"
                            onclick="openSidebar()"
                            class="lg:hidden p-2 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition shrink-0"
                            aria-label="Buka menu navigasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    {{-- Role badge --}}
                    <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold shrink-0
                        {{ request()->routeIs('admin.*') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : '' }}
                        {{ request()->routeIs('hr.*') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                        {{ request()->routeIs('interview.*') ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                        {{ request()->routeIs('kadidat.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                    ">
                        <span class="w-2 h-2 rounded-full mr-1.5 animate-pulse
                            {{ request()->routeIs('admin.*') ? 'bg-indigo-600' : '' }}
                            {{ request()->routeIs('hr.*') ? 'bg-emerald-600' : '' }}
                            {{ request()->routeIs('interview.*') ? 'bg-amber-600' : '' }}
                            {{ request()->routeIs('kadidat.*') ? 'bg-blue-600' : '' }}
                        "></span>
                        @yield('role-label', 'Dashboard')
                    </span>

                    {{-- Page title --}}
                    <h1 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate">
                        @yield('page-title', 'Overview')
                    </h1>
                </div>

                {{-- Right: User info + Logout --}}
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    @auth
                        @if(($userRole ?? null) === 'kandidat')
                            {{-- Candidate Application ID Badge in Top Navbar --}}
                            @if($candidateAppCode)
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 text-blue-900 shadow-sm" title="ID Lamaran Kandidat Terdaftar">
                                    <div class="p-1 rounded-lg bg-blue-600 text-white shrink-0 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[9px] uppercase tracking-wider font-bold text-blue-600 leading-none">ID Lamaran</span>
                                        <span class="text-xs font-black font-mono text-blue-950 leading-tight">{{ $candidateAppCode }}</span>
                                    </div>
                                </div>
                            @else
                                <div class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 text-xs">
                                    <span class="text-[10px] text-slate-400">ID Lamaran:</span>
                                    <span class="font-bold text-slate-600">Belum Ada</span>
                                </div>
                            @endif

                            {{-- Candidate Name in Top Navbar --}}
                            <div class="hidden sm:flex flex-col text-right min-w-0">
                                <span class="text-xs font-bold text-slate-900 truncate max-w-[160px]">
                                    {{ $candidateData?->full_name ?: auth()->user()->name }}
                                </span>
                                <span class="text-[10px] text-blue-600 font-semibold truncate">
                                    Pelamar / Kandidat
                                </span>
                            </div>

                            {{-- Candidate Profile Photo in Top Navbar --}}
                            <a href="{{ route('kadidat.profile.edit') }}" title="Lihat & Edit Profil (Termasuk Pasang Foto)">
                                @if($candidateProfileData?->photo)
                                    <img src="{{ asset('storage/' . $candidateProfileData->photo) }}"
                                         alt="Foto Profil {{ $candidateData?->full_name ?? auth()->user()->name }}"
                                         class="h-9 w-9 rounded-full object-cover ring-2 ring-blue-500/40 hover:ring-blue-600 shadow transition shrink-0">
                                @else
                                    <div class="h-9 w-9 rounded-full ring-2 ring-blue-500/40 hover:ring-blue-600 flex items-center justify-center font-bold text-xs text-white shadow-inner bg-gradient-to-tr from-blue-600 to-indigo-700 shrink-0 transition">
                                        {{ strtoupper(substr($candidateData?->first_name ?? auth()->user()->name, 0, 1) . substr($candidateData?->last_name ?? '', 0, 1)) ?: 'KD' }}
                                    </div>
                                @endif
                            </a>
                        @else
                            {{-- Default for Admin, HR, Interviewer --}}
                            <div class="hidden md:flex flex-col text-right">
                                <span class="text-xs font-bold text-slate-900 truncate max-w-[140px]">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] text-slate-400 capitalize">{{ auth()->user()->role?->display_name ?? 'Pengguna' }}</span>
                            </div>

                            <div class="h-9 w-9 rounded-full ring-2 ring-indigo-500/30 flex items-center justify-center font-bold text-xs text-white shadow-inner bg-gradient-to-tr from-indigo-600 to-indigo-800 shrink-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" title="Keluar dari Akun"
                                    class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-slate-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    @endauth
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success') || session('info') || session('error'))
                <div class="px-4 sm:px-6 lg:px-8 pt-5 space-y-2">
                    @if(session('success'))
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-2.5">
                                <span class="p-1 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-sm font-bold ml-4">&times;</button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-2.5">
                                <span class="p-1 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </span>
                                <span>{{ session('info') }}</span>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700 text-sm font-bold ml-4">&times;</button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-2.5">
                                <span class="p-1 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </span>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-sm font-bold ml-4">&times;</button>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Main Page Content --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

            {{-- Footer --}}
            <footer class="bg-white border-t border-slate-200 py-4 px-4 sm:px-6 lg:px-8 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2 shrink-0">
                <p>&copy; {{ date('Y') }} {{ \App\Models\SystemSetting::get('footer_text', 'Sistem Recruitment V 2.5 • All Rights Reserved') }}</p>
                <div class="flex items-center gap-3 text-slate-400 text-xs">
                    <span>{{ \App\Models\SystemSetting::get('app_name', 'SisRec') }} v{{ \App\Models\SystemSetting::get('app_version', '2.5') }} (Laravel 12 + Tailwind CSS)</span>
                    <span>&bull;</span>
                    <span class="inline-flex items-center text-emerald-600 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                        Sistem Berjalan Normal
                    </span>
                </div>
            </footer>

        </div>{{-- end main wrapper --}}

    </div>{{-- end flex wrapper --}}

    {{-- ============================================================ --}}
    {{-- JAVASCRIPT: Mobile sidebar toggle                           --}}
    {{-- ============================================================ --}}
    <script>
        const sidebar  = document.getElementById('sidebar');
        const overlay  = document.getElementById('sidebar-overlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('opacity-0', 'pointer-events-none');
            overlay.classList.add('opacity-100');
            document.body.style.overflow = 'hidden'; // prevent background scroll on mobile
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('opacity-0', 'pointer-events-none');
            overlay.classList.remove('opacity-100');
            document.body.style.overflow = '';
        }

        // Auto-close sidebar on resize to desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                overlay.classList.add('opacity-0', 'pointer-events-none');
                overlay.classList.remove('opacity-100');
                document.body.style.overflow = '';
            }
        });

        // Swipe-to-close on mobile
        let touchStartX = 0;
        sidebar.addEventListener('touchstart', (e) => { touchStartX = e.touches[0].clientX; }, { passive: true });
        sidebar.addEventListener('touchend', (e) => {
            const deltaX = e.changedTouches[0].clientX - touchStartX;
            if (deltaX < -60) { closeSidebar(); }
        }, { passive: true });
    </script>

</body>
</html>
