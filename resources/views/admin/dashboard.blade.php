@extends('layouts.dashboard')

@section('title', 'Admin Dashboard')
@section('role-label', 'Administrator')
@section('page-title', 'Pusat Kontrol & Manajemen Sistem')

@section('role-nav')
    <a href="#overview" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 font-semibold border border-indigo-200">Ringkasan</a>
    <a href="#users" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition">Manajemen Pengguna</a>
    <a href="#roles" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition">Roles & Permissions</a>
    <a href="#positions" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition">Daftar Lowongan</a>
    <a href="#logs" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-100 transition">Log Audit & Keamanan</a>
@endsection

@section('content')
<div class="space-y-8" id="overview">

    <!-- Top Notice / Welcome Card -->
    <div class="rounded-2xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-semibold mb-3 border border-indigo-400/30">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-ping"></span>
                    Sistem Enterprise Terkelola
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, Administrator!</h2>
                <p class="text-slate-300 text-sm mt-1 max-w-2xl leading-relaxed">
                    Pantau seluruh pengguna, status lowongan kerja perusahaan, distribusi hak akses role, serta rekaman audit log keamanan dalam satu dasbor terpusat.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold shadow-lg shadow-indigo-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                    Tambah Akun Pengguna
                </a>
                <a href="{{ route('admin.positions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-lg shadow-emerald-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Lowongan Kerja
                </a>
            </div>
        </div>
    </div>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1 -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengguna</span>
                <span class="p-2.5 rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ $totalUsers }}</span>
                <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">+12% bln ini</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Terbagi dalam 4 role akses sistem</p>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Lowongan Aktif</span>
                <span class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ $activePositions }}</span>
                <span class="text-xs font-normal text-slate-500">dari total {{ $totalPositions }} posisi</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Dipublikasikan di portal karir</p>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Kandidat Terdaftar</span>
                <span class="p-2.5 rounded-xl bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ $totalCandidates }}</span>
                <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">Talent Pool</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Dengan profil CV & skill lengkap</p>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Berkas Lamaran</span>
                <span class="p-2.5 rounded-xl bg-purple-50 text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </span>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-bold text-slate-900">{{ $totalApplications }}</span>
                <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full">Aktif Berjalan</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Sedang diproses dalam pipeline</p>
        </div>
    </div>

    <!-- Section: Roles Distribution & System Health -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" id="roles">
        <!-- Roles Distribution Card -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Distribusi Role & Hak Akses</h3>
                    <p class="text-xs text-slate-500">Rincian anggota tim dan akun terdaftar menurut peran sistem</p>
                </div>
                <button class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Kelola Role &rarr;</button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
                @foreach($rolesCount as $role)
                    <div class="p-4 rounded-xl border border-slate-200/60 bg-slate-50/50 hover:bg-white hover:border-indigo-200 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full
                                    {{ $role->name === 'admin' ? 'bg-indigo-600' : '' }}
                                    {{ $role->name === 'hr' ? 'bg-emerald-600' : '' }}
                                    {{ $role->name === 'interviewer' ? 'bg-amber-600' : '' }}
                                    {{ $role->name === 'kandidat' ? 'bg-blue-600' : '' }}
                                "></span>
                                <h4 class="text-sm font-bold text-slate-900">{{ $role->display_name }}</h4>
                            </div>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">
                                {{ $role->users_count }} Akun
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-2 line-clamp-2">{{ $role->description }}</p>
                        <div class="mt-3 flex items-center justify-between pt-2 border-t border-slate-200/40 text-[11px] text-slate-400">
                            <span>Slug: <code class="text-indigo-600 font-mono">{{ $role->name }}</code></span>
                            <span class="text-slate-500 font-medium">Izin Terkonfigurasi</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- System Information Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Spesifikasi Lingkungan</h3>
                        <p class="text-xs text-slate-500">Status runtime aplikasi & server</p>
                    </div>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                </div>

                <ul class="divide-y divide-slate-100 mt-4 text-xs">
                    <li class="py-2.5 flex justify-between">
                        <span class="text-slate-500">Versi Framework:</span>
                        <span class="font-semibold text-slate-800">Laravel {{ $systemInfo['laravel_version'] }}</span>
                    </li>
                    <li class="py-2.5 flex justify-between">
                        <span class="text-slate-500">Versi PHP:</span>
                        <span class="font-semibold text-slate-800">PHP {{ $systemInfo['php_version'] }}</span>
                    </li>
                    <li class="py-2.5 flex justify-between">
                        <span class="text-slate-500">Driver Database:</span>
                        <span class="font-semibold text-slate-800 uppercase">{{ $systemInfo['database'] }}</span>
                    </li>
                    <li class="py-2.5 flex justify-between">
                        <span class="text-slate-500">Environment:</span>
                        <span class="font-semibold text-indigo-600 uppercase bg-indigo-50 px-2 py-0.5 rounded">{{ $systemInfo['environment'] }}</span>
                    </li>
                    <li class="py-2.5 flex justify-between">
                        <span class="text-slate-500">Zona Waktu:</span>
                        <span class="font-semibold text-slate-800">{{ $systemInfo['timezone'] }}</span>
                    </li>
                </ul>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100">
                <button type="button" class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold text-center transition">
                    Bersihkan Cache Aplikasi
                </button>
            </div>
        </div>
    </div>

    <!-- Section: Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden" id="users">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">Daftar Pengguna Sistem Terbaru</h3>
                <p class="text-xs text-slate-500">Seluruh pengguna terdaftar yang memiliki kredensial login</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 text-xs font-bold transition">
                    <span>Lihat Semua Pengguna ({{ $totalUsers }})</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Nama & Profil</th>
                        <th class="px-6 py-3.5">Email</th>
                        <th class="px-6 py-3.5">Peran / Role</th>
                        <th class="px-6 py-3.5">Status Akun</th>
                        <th class="px-6 py-3.5">Terdaftar</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($recentUsers as $user)
                        <tr class="hover:bg-slate-50/50 transition {{ $user->is_blacklisted ? 'bg-rose-50/30' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full font-bold flex items-center justify-center text-xs
                                        {{ $user->is_blacklisted ? 'bg-rose-100 text-rose-700' : 'bg-slate-200 text-slate-700' }}">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <span class="font-bold text-slate-900">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-mono text-slate-600">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold
                                    {{ $user->role?->name === 'admin' ? 'bg-indigo-100 text-indigo-800' : '' }}
                                    {{ $user->role?->name === 'hr' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $user->role?->name === 'interviewer' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $user->role?->name === 'kandidat' ? 'bg-blue-100 text-blue-800' : '' }}
                                ">
                                    {{ $user->role?->display_name ?? 'User Biasa' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->is_blacklisted)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-rose-600 text-white font-extrabold text-[10px]">
                                        Blacklisted
                                    </span>
                                @elseif($user->is_active)
                                    <span class="inline-flex items-center text-emerald-600 font-medium text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-rose-600 font-medium text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold mr-2">Edit</a>
                                <a href="{{ route('admin.users.index') }}?search={{ urlencode($user->email) }}" class="text-slate-600 hover:text-slate-900 font-semibold">Kelola</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section: Security & Activity Logs -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6" id="logs">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900">Audit Trail & Rekaman Aktivitas Sistem</h3>
                <p class="text-xs text-slate-500">Log perubahan konfigurasi, penjadwalan, dan aksi rekrutmen terkini</p>
            </div>
            <span class="text-xs font-semibold text-slate-400">Total: {{ $recentActivities->count() }} Aktivitas Baru</span>
        </div>

        <div class="mt-4 space-y-3">
            @forelse($recentActivities as $log)
                <div class="flex items-start justify-between p-3.5 rounded-xl border border-slate-100 bg-slate-50/60 hover:bg-white hover:border-slate-200 transition">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-xs text-slate-900">{{ $log->user_name ?? 'System' }}</span>
                                <span class="text-[10px] uppercase font-mono px-2 py-0.5 rounded bg-slate-200 text-slate-700 font-semibold">{{ $log->action }}</span>
                                <span class="text-[10px] text-slate-400">Modul: {{ $log->module }}</span>
                            </div>
                            <p class="text-xs text-slate-600 mt-1">{{ $log->description }}</p>
                        </div>
                    </div>
                    <div class="text-right shrink-0 text-[11px] text-slate-400">
                        <span>{{ $log->created_at->diffForHumans() }}</span>
                        <div class="text-[10px] font-mono text-slate-400 mt-0.5">IP: {{ $log->ip_address ?? '127.0.0.1' }}</div>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-500 py-4 text-center">Belum ada aktivitas yang tercatat.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
