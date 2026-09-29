@php
    $appName = $settings['app_name'] ?? \App\Models\SystemSetting::get('app_name', config('app.name', 'RecruitPro'));
    $companyName = $settings['company_name'] ?? \App\Models\SystemSetting::get('company_name', 'PT Rekrutmen Indonesia');
    $footerText = $settings['footer_text'] ?? \App\Models\SystemSetting::get('footer_text', 'Sistem Rekrutmen & Karir • All Rights Reserved');
    $appLogo = $settings['app_logo'] ?? \App\Models\SystemSetting::get('app_logo');
    $companyEmail = $settings['company_email'] ?? \App\Models\SystemSetting::get('company_email', 'talent@recruitment.test');
    $companyPhone = $settings['company_phone'] ?? \App\Models\SystemSetting::get('company_phone', '+62 21 555-0199');
    $isLoggedIn = auth()->check();
    $authUser = auth()->user();


    $genderText = 'PRIA / WANITA';
    $descLower = strtolower(($position->description ?? '') . ' ' . ($position->requirements ?? ''));
    if (str_contains($descLower, 'pria saja') || (str_contains($descLower, 'pria') && !str_contains($descLower, 'wanita'))) {
        $genderText = 'PRIA';
    } elseif (str_contains($descLower, 'wanita saja') || (str_contains($descLower, 'wanita') && !str_contains($descLower, 'pria'))) {
        $genderText = 'WANITA';
    }

    $eduText = 'SLTA / D3 / S1';
    if ($position->experience_level) {
        $expLower = strtolower($position->experience_level);
        if (str_contains($expLower, 'senior') || str_contains($expLower, 'lead')) {
            $eduText = 'S1 / D4';
        } elseif (str_contains($expLower, 'mid')) {
            $eduText = 'D3 / S1';
        } elseif (str_contains($expLower, 'junior') || str_contains($expLower, 'entry')) {
            $eduText = 'SLTA / D3';
        } elseif (str_contains($expLower, 'intern') || str_contains($expLower, 'magang')) {
            $eduText = 'SLTA / MAHASISWA / D3 / S1';
        }
    }
    if (str_contains($descLower, 'slta') || str_contains($descLower, 'sma') || str_contains($descLower, 'smk')) {
        $eduText = 'SLTA / SEDERAJAT';
    }

    $majorText = $position->requirements ?: ($position->department . ' atau jurusan lain yang relevan');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $position->title }} - Lowongan Kerja | {{ $companyName }}</title>
    <meta name="description"
        content="Detail lowongan {{ $position->title }} di {{ $companyName }}. Informasi formasi, kualifikasi, syarat dokumen, dan pendaftaran online.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
        }

        .table-custom-border th,
        .table-custom-border td {
            border: 1px solid #334155;
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
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">INFO</span>
                <span>Portal Rekrutmen Resmi <strong>{{ $companyName }}</strong></span>
            </div>
            <div class="flex items-center gap-4 text-[11px] text-indigo-300">
                <span>HR Care: {{ $companyEmail }}</span>
                <span class="text-indigo-600">•</span>
                <span>{{ $companyPhone }}</span>
            </div>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
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
                        class="text-lg font-black text-slate-900 tracking-tight block leading-tight group-hover:text-indigo-600 transition">{{ $appName }}</span>
                    <span
                        class="text-[10px] text-slate-500 font-semibold tracking-wider uppercase block">{{ $companyName }}</span>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-7 text-xs font-bold text-slate-600">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 transition">Beranda</a>
                <a href="{{ route('public.lowongan.index') }}"
                    class="text-indigo-600 font-extrabold pb-0.5 border-b-2 border-indigo-600">Lowongan</a>
                <a href="{{ route('home') }}#departemen" class="hover:text-indigo-600 transition">Departemen</a>
                <a href="{{ route('home') }}#alur-seleksi" class="hover:text-indigo-600 transition">Alur Seleksi</a>
            </nav>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('public.lowongan.index') }}"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Daftar Lowongan</span>
                </a>

                @if($isLoggedIn)
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-4 py-2 rounded-xl text-slate-700 hover:text-indigo-600 hover:bg-slate-100 font-bold text-xs transition">Masuk</a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                        <span>Daftar</span>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <main class="grow py-8 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6 flex-wrap">
                <a href="{{ route('home') }}" class="hover:text-indigo-600 transition">Beranda</a>
                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <a href="{{ route('public.lowongan.index') }}" class="hover:text-indigo-600 transition">Lowongan
                    Kerja</a>
                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-slate-900 font-bold truncate max-w-xs sm:max-w-md">{{ $position->title }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                <!-- Left / Main Column (3 Cols) -->
                <div class="lg:col-span-3 space-y-6">

                    <!-- Header Banner Lowongan (Style Gambar 1) -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8">
                        <div class="flex items-center gap-2 mb-3">
                            <span
                                class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                {{ $position->department }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                {{ $position->type }}
                            </span>
                            @if($position->is_internal)
                                <span
                                    class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-purple-100 text-purple-800 border border-purple-200">
                                    Internal Saja
                                </span>
                            @endif
                        </div>

                        <h1
                            class="text-xl sm:text-2xl lg:text-3xl font-black text-slate-900 tracking-tight leading-snug">
                            REKRUTMEN {{ strtoupper($position->title) }} TAHUN {{ date('Y') }}
                        </h1>

                        <div
                            class="mt-3 flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600 border-l-4 border-indigo-600 pl-3">
                            <span>Berakhir s.d :</span>
                            <svg class="w-4 h-4 text-indigo-600 inline-block" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-indigo-700 font-bold">
                                @if($position->deadline)
                                    {{ \Carbon\Carbon::parse($position->deadline)->format('d/m/Y') }}
                                @else
                                    Seterusnya (Belum Ditentukan)
                                @endif
                            </span>
                        </div>

                        @if($position->description)
                            <div
                                class="mt-4 pt-4 border-t border-slate-100 text-xs sm:text-sm text-slate-600 leading-relaxed">
                                <p>{{ $position->description }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Ketentuan & Persyaratan Umum Rekrutmen -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">Ketentuan &
                                Persyaratan Umum Pelamar</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Harap membaca dengan seksama seluruh ketentuan di
                                bawah ini sebelum melanjutkan pendaftaran.</p>
                        </div>

                        <ol
                            class="list-decimal list-inside space-y-2.5 text-xs sm:text-sm text-slate-700 leading-relaxed font-medium">
                            <li>Warga Negara Indonesia (WNI) yang setia kepada Pancasila dan UUD 1945.</li>
                            <li>Jenis kelamin sesuai dengan kebutuhan formasi yang tercantum pada tabel spesifikasi
                                jabatan.</li>
                            <li>Sehat jasmani dan rohani, tidak buta warna (parsial maupun total), serta bebas narkoba
                                dan zat adiktif lainnya (NAPZA).</li>
                            <li>Berkelakuan baik, tidak bertato, dan memiliki Surat Keterangan Catatan Kepolisian (SKCK)
                                yang masih aktif.</li>
                            <li>Tidak pernah diberhentikan dengan hormat tidak atas permintaan sendiri atau tidak dengan
                                hormat sebagai pegawai dari instansi pemerintah/swasta.</li>
                            <li>Memenuhi kualifikasi tingkat pendidikan, jurusan, dan nilai/IPK minimum yang
                                dipersyaratkan oleh posisi ini.</li>
                            <li>Bersedia ditempatkan di seluruh wilayah operasional unit kerja terkait sesuai kebutuhan
                                penugasan.</li>
                            <li>Setiap pelamar hanya diperbolehkan memilih dan mendaftar pada <strong>1 (satu) formasi
                                    jabatan</strong> pada periode rekrutmen ini.</li>
                            <li>Seluruh proses rekrutmen dan seleksi penerimaan pekerja ini <strong>TIDAK DIPUNGUT BIAYA
                                    APAPUN (GRATIS)</strong>. Waspadai segala bentuk penipuan yang mengatasnamakan
                                panitia.</li>
                            <li class="font-bold text-slate-900 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                                10. Seleksi penerimaan pekerja menggunakan sistem gugur dan keputusan panitia tidak
                                dapat diganggu gugat.
                            </li>
                        </ol>
                    </div>

                    <!-- TABEL FORMASI & SYARAT DOKUMEN (Sesuai Gambar 2) -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">Tabel
                                    Kualifikasi Formasi & Dokumen</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Rincian formasi jabatan, persyaratan
                                    pendidikan, jurusan, dan kelengkapan berkas dokumen.</p>
                            </div>
                        </div>

                        <!-- Table Responsive Container (Style persis gambar 2) -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs border-collapse table-custom-border border border-slate-700">
                                <thead>
                                    <tr
                                        class="bg-slate-100 text-slate-900 font-black text-center uppercase tracking-wider">
                                        <th class="py-3.5 px-4 font-bold border border-slate-700 min-w-[140px]">Formasi
                                        </th>
                                        <th class="py-3.5 px-3 font-bold border border-slate-700 min-w-[100px]">Jenis
                                            Kelamin</th>
                                        <th class="py-3.5 px-3 font-bold border border-slate-700 min-w-[100px]">
                                            Pendidikan</th>
                                        <th class="py-3.5 px-4 font-bold border border-slate-700 min-w-[180px]">Jurusan
                                        </th>
                                        <th class="py-3.5 px-3 font-bold border border-slate-700 min-w-[110px]">
                                            Keterangan</th>
                                        <th class="py-3.5 px-4 font-bold border border-slate-700 min-w-[180px]">Syarat
                                            Dokumen</th>
                                    </tr>
                                </thead>
                                <tbody class="text-slate-800 text-[11px] sm:text-xs leading-relaxed">
                                    <tr class="align-middle">
                                        <!-- Formasi -->
                                        <td class="p-4 font-bold text-center border border-slate-700 text-slate-900">
                                            {{ $position->title }}
                                        </td>

                                        <!-- Jenis Kelamin -->
                                        <td class="p-3 font-bold text-center border border-slate-700 uppercase">
                                            {{ $genderText }}
                                        </td>

                                        <!-- Pendidikan -->
                                        <td class="p-3 font-bold text-center border border-slate-700 uppercase">
                                            {{ $eduText }}
                                        </td>

                                        <!-- Jurusan -->
                                        <td
                                            class="p-4 border border-slate-700 text-slate-700 font-medium leading-normal">
                                            {{ $majorText }}
                                        </td>

                                        <!-- Keterangan -->
                                        <td class="p-3 font-bold text-center border border-slate-700">
                                            {{ $position->type === 'Full-time' ? 'Full Time' : ($position->type === 'Contract' ? 'Contract Based' : $position->type) }}
                                        </td>

                                        <!-- Syarat Dokumen (Style Uppercase persis Gambar 2) -->
                                        <td
                                            class="p-4 border border-slate-700 font-semibold text-slate-800 uppercase leading-snug">
                                            <div class="space-y-1">
                                                <div>• DAFTAR RIWAYAT HIDUP - CV</div>
                                                <div>• IJAZAH PENDIDIKAN TERAKHIR</div>
                                                <div>• KTP (KARTU TANDA PENDUDUK)</div>
                                                <div>• TRANSKRIP NILAI / SKHUN</div>
                                                <div>• SURAT KETERANGAN SEHAT DOKTER</div>
                                                <div>• SURAT BEBAS BUTA WARNA</div>
                                                <div>• SKCK YANG MASIH BERLAKU</div>
                                                <div>• SURAT LAMARAN PEKERJAAN</div>
                                                <div>• PAS FOTO TERBARU (WARNA)</div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Action Buttons di Bawah Tabel (Sesuai Gambar 2: Tombol KEMBALI & Tombol LAMAR) -->
                        <div
                            class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <a href="{{ route('public.lowongan.index') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg border-2 border-indigo-600 text-indigo-600 hover:bg-indigo-50 font-black text-xs uppercase tracking-wider transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                <span>KEMBALI</span>
                            </a>

                            <div class="w-full sm:w-auto flex items-center gap-2">
                                @if($isLoggedIn && $authUser->role?->name === 'kandidat')
                                    <a href="{{ route('kadidat.positions.show', $position->id) }}"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-wider shadow-md shadow-indigo-600/30 transition">
                                        <span>LAMAR SEKARANG</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </a>
                                @else
                                    <a href="{{ route('login') }}"
                                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-2.5 rounded-lg bg-slate-900 hover:bg-indigo-600 text-white font-black text-xs uppercase tracking-wider shadow-md transition">
                                        <span>LOGIN UNTUK MELAMAR</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>

                <!-- Right Sidebar Column (1 Col) -->
                <div class="lg:col-span-1 space-y-6">

                    <!-- Ringkasan Info Box -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-5 space-y-4">
                        <h3
                            class="text-xs font-black uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-100">
                            Ringkasan Formasi
                        </h3>

                        <div class="space-y-3.5 text-xs">
                            <div>
                                <span class="text-slate-400 text-[11px] block">Departemen</span>
                                <span class="font-bold text-slate-800">{{ $position->department }}</span>
                            </div>

                            <div>
                                <span class="text-slate-400 text-[11px] block">Penempatan Lokasi</span>
                                <span class="font-bold text-slate-800 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-indigo-600 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $position->location }}
                                </span>
                            </div>

                            <div>
                                <span class="text-slate-400 text-[11px] block">Tingkat Pengalaman</span>
                                <span
                                    class="font-bold text-slate-800">{{ $position->experience_level ?: 'Fresh Graduate / All Levels' }}</span>
                            </div>

                            @if($position->salary_min && $position->salary_max)
                                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block">Kisaran Gaji</span>
                                    <span class="font-black text-slate-900 text-xs text-indigo-700">
                                        Rp {{ number_format($position->salary_min, 0, ',', '.') }} -
                                        {{ number_format($position->salary_max, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif

                            <div class="p-3 rounded-xl bg-indigo-50/70 border border-indigo-100">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-indigo-900 font-semibold">Kuota Formasi:</span>
                                    <span class="font-black text-indigo-700">{{ $position->quota }} Posisi</span>
                                </div>
                                <div class="flex items-center justify-between text-xs mt-1">
                                    <span class="text-indigo-900 font-semibold">Pelamar Terdaftar:</span>
                                    <span class="font-black text-indigo-700">{{ $position->applications_count }}
                                        Orang</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100">
                            @if($isLoggedIn && $authUser->role?->name === 'kandidat')
                                <a href="{{ route('kadidat.positions.show', $position->id) }}"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                                    <span>Lamar Pekerjaan Ini</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white font-bold text-xs shadow-md transition">
                                    <span>Masuk untuk Melamar</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </a>
                                <p class="text-[10px] text-center text-slate-400 mt-2">
                                    Belum punya akun? <a href="{{ route('register') }}"
                                        class="text-indigo-600 font-bold hover:underline">Daftar Akun</a>
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Bantuan & Narahubung Card -->
                    <div class="bg-gradient-to-br from-indigo-900 to-purple-950 text-white rounded-2xl p-5 space-y-3">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-indigo-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="font-bold text-sm">Butuh Bantuan?</h4>
                        <p class="text-xs text-indigo-200 leading-relaxed">
                            Jika mengalami kendala teknis dalam proses pendaftaran, silakan hubungi tim rekrutmen kami
                            melalui email atau kontak resmi.
                        </p>
                        <div class="pt-2 text-xs border-t border-indigo-800/80 space-y-1 text-indigo-300 font-medium">
                            <div>Email: <strong class="text-white">{{ $companyEmail }}</strong></div>
                            <div>Telp: <strong class="text-white">{{ $companyPhone }}</strong></div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-400 text-xs pt-10 pb-8 border-t border-slate-900 mt-12">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-500">
            <p>&copy; {{ date('Y') }} {{ $companyName }}. {{ $footerText }}</p>
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="hover:text-white transition">Beranda</a>
                <span>•</span>
                <a href="{{ route('public.lowongan.index') }}" class="hover:text-white transition">Daftar Lowongan</a>
                <span>•</span>
                <a href="{{ route('login') }}" class="hover:text-white transition">Masuk</a>
                <span>•</span>
                <a href="{{ route('register') }}" class="hover:text-white transition">Daftar</a>
            </div>
        </div>
    </footer>

</body>

</html>