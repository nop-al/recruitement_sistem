@php
    $appName = $settings['app_name'] ?? \App\Models\SystemSetting::get('app_name', config('app.name', 'RecruitPro'));
    $companyName = $settings['company_name'] ?? \App\Models\SystemSetting::get('company_name', 'PT Rekrutmen Indonesia');
    $footerText = $settings['footer_text'] ?? \App\Models\SystemSetting::get('footer_text', 'Sistem Rekrutmen & Karir • All Rights Reserved');
    $appLogo = $settings['app_logo'] ?? \App\Models\SystemSetting::get('app_logo');
    $companyEmail = $settings['company_email'] ?? \App\Models\SystemSetting::get('company_email', 'talent@recruitment.test');
    $companyPhone = $settings['company_phone'] ?? \App\Models\SystemSetting::get('company_phone', '+62 21 555-0199');
    $isLoggedIn = auth()->check();
    $authUser = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Lowongan Kerja - {{ $appName }} | {{ $companyName }}</title>
    <meta name="description" content="Temukan semua lowongan kerja aktif di {{ $companyName }}. Lamar sekarang melalui portal rekrutmen resmi.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased selection:bg-indigo-500 selection:text-white flex flex-col min-h-screen">

    <div class="bg-indigo-950 text-indigo-200 text-xs py-2 px-4 border-b border-indigo-900/60 hidden sm:block">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">INFO</span>
                <span>Portal Karir Resmi <strong>{{ $companyName }}</strong></span>
            </div>
            <div class="flex items-center gap-4 text-[11px] text-indigo-300">
                <span>HR: {{ $companyEmail }}</span>
                <span class="text-indigo-600">•</span>
                <span>{{ $companyPhone }}</span>
            </div>
        </div>
    </div>

    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                @if($appLogo && file_exists(public_path('storage/' . $appLogo)))
                    <img src="{{ asset('storage/' . $appLogo) }}" alt="{{ $appName }}" class="h-10 w-auto object-contain">
                @else
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 text-white flex items-center justify-center font-black text-xl shadow-md shadow-indigo-600/30 group-hover:scale-105 transition transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
                <div>
                    <span class="text-lg font-black text-slate-900 tracking-tight block leading-tight group-hover:text-indigo-600 transition">{{ $appName }}</span>
                    <span class="text-[10px] text-slate-500 font-semibold tracking-wider uppercase block">{{ $companyName }}</span>
                </div>
            </a>
            <nav class="hidden md:flex items-center gap-7 text-xs font-bold text-slate-600">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 transition">Beranda</a>
                <a href="{{ route('public.lowongan.index') }}" class="text-indigo-600 border-b-2 border-indigo-600 pb-0.5">Lowongan</a>
                <a href="{{ route('home') }}#departemen" class="hover:text-indigo-600 transition">Departemen</a>
                <a href="{{ route('home') }}#alur-seleksi" class="hover:text-indigo-600 transition">Alur Seleksi</a>
            </nav>
            <div class="flex items-center gap-2.5">
                @if($isLoggedIn)
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-slate-700 hover:text-indigo-600 hover:bg-slate-100 font-bold text-xs transition">Masuk</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                        <span>Daftar</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <main class="grow">
        <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-purple-900 text-white py-12 sm:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex items-center gap-2 text-xs text-indigo-300 mb-5">
                    <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <span class="text-white font-semibold">Lowongan Kerja</span>
                </nav>
                <h1 class="text-2xl sm:text-4xl font-black tracking-tight mb-2">Lowongan Kerja Tersedia</h1>
                <p class="text-sm text-indigo-200 max-w-xl mb-8">Temukan posisi pekerjaan yang sesuai dengan keahlian Anda. Semua rekrutmen resmi dari <strong>{{ $companyName }}</strong>.</p>
                <form method="GET" action="{{ route('public.lowongan.index') }}" class="flex flex-col sm:flex-row gap-3 max-w-3xl">
                    <div class="relative grow">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari posisi, departemen, atau lokasi..."
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white text-slate-900 placeholder-slate-400 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-400">
                    </div>
                    <select name="dept" class="sm:w-48 px-4 py-2.5 rounded-xl bg-white text-slate-900 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->name }}" {{ request('dept') === $dept->name ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-400 text-white font-bold text-sm shadow-md transition shrink-0">Cari Lowongan</button>
                </form>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm font-semibold text-slate-700">
                    <span class="text-indigo-600">{{ $positions->total() }}</span> Lowongan Ditemukan
                    @if(request('q') || request('dept'))
                        <span class="text-slate-400 font-normal">
                            @if(request('q')) &mdash; "<em>{{ request('q') }}</em>"@endif
                            @if(request('dept')) di <em>{{ request('dept') }}</em>@endif
                        </span>
                    @endif
                </p>
                @if(request('q') || request('dept'))
                    <a href="{{ route('public.lowongan.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-800 transition flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Reset Filter
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($positions as $pos)
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-lg hover:border-indigo-300 transition duration-200 p-6 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between gap-2 pb-3 border-b border-slate-100">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 truncate max-w-[130px]">{{ $pos->department }}</span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    @if($pos->is_internal)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-100 text-purple-800 border border-purple-200">Internal</span>
                                    @endif
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">{{ $pos->type }}</span>
                                </div>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 mt-3 group-hover:text-indigo-600 transition line-clamp-2">{{ $pos->title }}</h3>
                            <div class="mt-2 space-y-1 text-xs text-slate-500">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="truncate">{{ $pos->location }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Level: {{ $pos->experience_level }}</span>
                                </div>
                            </div>
                            <div class="mt-4 p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-semibold block">Kisaran Gaji</span>
                                    <span class="font-bold text-slate-900">Rp {{ number_format($pos->salary_min, 0, ',', '.') }} - {{ number_format($pos->salary_max, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 font-semibold block">Kuota</span>
                                    <span class="font-bold text-indigo-700">{{ $pos->applications_count }} / {{ $pos->quota }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <span class="text-[11px] text-slate-400">
                                @if($pos->deadline) Batas: {{ \Carbon\Carbon::parse($pos->deadline)->format('d M Y') }}
                                @else Buka Terus @endif
                            </span>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('public.lowongan.show', encrypt($pos->id)) }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                                    Detail
                                </a>
                                @if($isLoggedIn && $authUser->role?->name === 'kandidat')
                                    <a href="{{ route('kadidat.positions.show', $pos->id) }}"
                                        class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-xs transition">
                                        Lamar <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs shadow-xs transition">
                                        Lamar <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Tidak ada lowongan ditemukan</p>
                        <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci atau filter pencarian Anda.</p>
                        <a href="{{ route('public.lowongan.index') }}" class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition">Lihat Semua Lowongan</a>
                    </div>
                @endforelse
            </div>

            @if($positions->hasPages())
                <div class="mt-10 flex justify-center">{{ $positions->links() }}</div>
            @endif
        </div>
    </main>

    <footer class="bg-slate-950 text-slate-400 text-xs pt-10 pb-8 border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500">
            <p>&copy; {{ date('Y') }} {{ $companyName }}. {{ $footerText }}</p>
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                <span>•</span>
                <a href="{{ route('public.lowongan.index') }}" class="hover:text-white transition">Semua Lowongan</a>
                <span>•</span>
                <a href="{{ route('login') }}" class="hover:text-white transition">Masuk</a>
            </div>
        </div>
    </footer>

</body>
</html>

