@extends('layouts.dashboard')

@section('title', 'Manajemen Pengguna Sistem')
@section('role-label', 'Administrator')
@section('page-title', 'Daftar Seluruh Pengguna Sistem (User Management)')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Overview Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 font-medium">Admin Dashboard</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Pengguna Sistem</span>
            </div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Manajemen Pengguna & Akses</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh akun pengguna, verifikasi NIK pelamar, hak akses role, serta kontrol keamanan akun</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span>Tambah Pengguna Baru</span>
            </a>
        </div>
    </div>

    <!-- Alert Modal Password Reset Success if applicable -->
    @if(session('password_reset_success'))
        @php $resetInfo = session('password_reset_success'); @endphp
        <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200 text-indigo-900 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-700">Kata Sandi Berhasil Direset!</h4>
                    <p class="text-xs mt-0.5 text-indigo-900">
                        Pengguna: <strong class="font-semibold">{{ $resetInfo['name'] }}</strong> ({{ $resetInfo['email'] }})
                    </p>
                    <div class="mt-2 flex items-center gap-2">
                        <span class="text-xs text-indigo-700 font-medium">Kata Sandi Baru:</span>
                        <code id="newPasswordCode" class="px-2.5 py-1 rounded-lg bg-white border border-indigo-300 font-mono font-bold text-xs text-indigo-950 select-all">{{ $resetInfo['password'] }}</code>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $resetInfo['password'] }}'); alert('Kata sandi berhasil disalin!');"
                                class="px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition">
                            Salin Sandi
                        </button>
                    </div>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-indigo-400 hover:text-indigo-700 self-start sm:self-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    @endif

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Pengguna</span>
                <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</div>
                <span class="text-[10px] text-slate-500">Akun terdaftar</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Pengguna Aktif</span>
                <div class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['active']) }}</div>
                <span class="text-[10px] text-slate-500">Dapat login normal</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-rose-600">Blacklisted</span>
                <div class="text-2xl font-black text-rose-600 mt-1">{{ number_format($stats['blacklisted']) }}</div>
                <span class="text-[10px] text-slate-500">Akses diblokir</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-blue-600">Kandidat / Pelamar</span>
                <div class="text-2xl font-black text-blue-600 mt-1">{{ number_format($stats['candidates']) }}</div>
                <span class="text-[10px] text-slate-500">1 Akun = 1 NIK KTP</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <!-- Keyword Search -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Pengguna</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $searchTerm }}" placeholder="Nama, email, telepon, atau NIK KTP..."
                           class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Role Filter -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Peran / Role</label>
                <select name="role" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <option value="">Semua Peran</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ $selectedRole == $role->id ? 'selected' : '' }}>
                            {{ $role->display_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Keamanan</label>
                <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                    <option value="">Semua Status</option>
                    <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>Aktif Normal</option>
                    <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    <option value="blacklisted" {{ $selectedStatus === 'blacklisted' ? 'selected' : '' }}>Blacklisted (Diblokir)</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    <span>Filter</span>
                </button>

                @if($searchTerm || $selectedRole || $selectedStatus)
                    <a href="{{ route('admin.users.index') }}" title="Reset Filter"
                       class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-sm font-bold text-slate-900">Daftar Pengguna Sistem</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-semibold">
                    {{ $users->total() }} Data Ditemukan
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                    <tr>
                        <th class="px-5 py-3.5">Nama & Profil</th>
                        <th class="px-5 py-3.5">Email & Kontak</th>
                        <th class="px-5 py-3.5">NIK (Kandidat)</th>
                        <th class="px-5 py-3.5">Peran / Role</th>
                        <th class="px-5 py-3.5">Status Akun</th>
                        <th class="px-5 py-3.5">Terdaftar</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        @php
                            $candidate = $user->candidate;
                            $profile = $candidate?->profile;
                            $userRole = $user->role?->name;
                            $isSelf = (auth()->id() === $user->id);
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition {{ $user->is_blacklisted ? 'bg-rose-50/30' : '' }}">
                            <!-- Name & Avatar -->
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl font-bold flex items-center justify-center text-xs shrink-0 shadow-xs
                                        {{ $user->is_blacklisted ? 'bg-rose-100 text-rose-700' : ($userRole === 'admin' ? 'bg-indigo-100 text-indigo-700' : ($userRole === 'kandidat' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700')) }}">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($isSelf)
                                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-indigo-50 text-indigo-600 border border-indigo-200">Anda</span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-slate-400 font-mono">ID: #{{ $user->id }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Email & Phone -->
                            <td class="px-5 py-4">
                                <div class="font-mono text-slate-700 font-medium">{{ $user->email }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $candidate?->phone_number ?? '-' }}
                                </div>
                            </td>

                            <!-- NIK (1 Akun = 1 NIK) -->
                            <td class="px-5 py-4">
                                @if($userRole === 'kandidat')
                                    @if(!empty($profile?->nik))
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 font-mono text-[11px] font-bold text-slate-800">
                                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                            </svg>
                                            <span>{{ $profile->nik }}</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[11px] text-amber-600 font-semibold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                            </svg>
                                            <span>Belum Ada NIK</span>
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-300 text-[11px]">- Bukan Kandidat -</span>
                                @endif
                            </td>

                            <!-- Role -->
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold border
                                    {{ $userRole === 'admin' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : '' }}
                                    {{ $userRole === 'hr' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                    {{ $userRole === 'interviewer' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                    {{ $userRole === 'kandidat' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                ">
                                    {{ $user->role?->display_name ?? 'User' }}
                                </span>
                            </td>

                            <!-- Account Security Status -->
                            <td class="px-5 py-4">
                                @if($user->is_blacklisted)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-rose-600 text-white shadow-xs"
                                          title="{{ $user->blacklist_reason ?: 'Akun di-blacklist oleh administrator' }}">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                        <span>Blacklisted</span>
                                    </span>
                                    @if($user->blacklist_reason)
                                        <p class="text-[10px] text-rose-700 font-medium mt-1 max-w-[140px] truncate" title="{{ $user->blacklist_reason }}">
                                            {{ $user->blacklist_reason }}
                                        </p>
                                    @endif
                                @elseif($user->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Nonaktif</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Created At -->
                            <td class="px-5 py-4 text-slate-500 font-medium">
                                {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}
                                <div class="text-[10px] text-slate-400">{{ $user->created_at ? $user->created_at->diffForHumans() : '' }}</div>
                            </td>

                            <!-- Action Dropdown -->
                            <td class="px-5 py-4 text-right">
                                <div class="relative inline-block text-left user-action-dropdown">
                                    <button type="button"
                                            onclick="toggleUserDropdown('dropdown-{{ $user->id }}', this, event)"
                                            class="user-action-dropdown-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-50 active:bg-slate-100 text-slate-700 hover:text-slate-900 border border-slate-200 font-semibold text-xs transition shadow-2xs cursor-pointer focus:outline-hidden focus:ring-2 focus:ring-indigo-500/20"
                                            aria-expanded="false"
                                            aria-haspopup="true">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                        <span>Aksi</span>
                                        <svg class="w-3 h-3 text-slate-400 dropdown-arrow transition-transform duration-150" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div id="dropdown-{{ $user->id }}"
                                         class="user-action-dropdown-menu hidden bg-white rounded-2xl shadow-xl border border-slate-200/90 text-xs text-slate-700 overflow-hidden divide-y divide-slate-100 text-left"
                                         style="min-width: 220px;">

                                        <!-- User Mini Header -->
                                        <div class="px-3.5 py-2.5 bg-slate-50/90 flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-lg font-bold flex items-center justify-center text-[10px] shrink-0
                                                {{ $user->is_blacklisted ? 'bg-rose-100 text-rose-700' : ($userRole === 'admin' ? 'bg-indigo-100 text-indigo-700' : ($userRole === 'kandidat' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700')) }}">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="font-bold text-slate-900 truncate text-[11px] leading-tight">{{ $user->name }}</p>
                                                <p class="text-[10px] text-slate-400 font-mono truncate leading-tight mt-0.5">{{ $user->email }}</p>
                                            </div>
                                        </div>

                                        <!-- Menu Actions List -->
                                        <div class="p-1.5 space-y-0.5">
                                            <!-- 1. Edit Profil -->
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                               class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-indigo-600 hover:bg-indigo-50/70 font-semibold transition group">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                <span>Edit Profil</span>
                                            </a>

                                            <!-- 2. Lihat Lamaran (Jika role Kandidat) -->
                                            @if($userRole === 'kandidat')
                                                <a href="{{ route('admin.applications.index', ['search' => $user->email]) }}"
                                                   class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-blue-600 hover:bg-blue-50/70 font-semibold transition group">
                                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <span>Lihat Berkas Lamaran</span>
                                                </a>
                                            @endif

                                            <!-- 3. Reset Password -->
                                            <button type="button"
                                                    onclick="closeAllDropdowns(); openResetPasswordModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-amber-700 hover:bg-amber-50/70 font-semibold transition group text-left cursor-pointer">
                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-amber-600 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                                </svg>
                                                <span>Reset Password</span>
                                            </button>

                                            <!-- 4. Blacklist / Buka Blacklist -->
                                            @if(!$isSelf)
                                                <div class="my-1 border-t border-slate-100"></div>

                                                @if($user->is_blacklisted)
                                                    <form action="{{ route('admin.users.blacklist', $user->id) }}" method="POST"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin memulihkan / mencabut status blacklist untuk {{ addslashes($user->name) }}?')">
                                                        @csrf
                                                        <button type="submit"
                                                                class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-emerald-700 hover:bg-emerald-50/80 font-semibold transition group text-left cursor-pointer">
                                                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span>Buka Blacklist</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button"
                                                            onclick="closeAllDropdowns(); openBlacklistModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-slate-700 hover:text-rose-700 hover:bg-rose-50/70 font-semibold transition group text-left cursor-pointer">
                                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-rose-600 transition shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                                        </svg>
                                                        <span>Blacklist Akun</span>
                                                    </button>
                                                @endif

                                                <!-- 5. Hapus Pengguna -->
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                      onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus akun {{ addslashes($user->name) }} ({{ addslashes($user->email) }}) secara permanen?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-rose-600 hover:bg-rose-50/80 font-semibold transition group text-left cursor-pointer">
                                                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        <span>Hapus Akun</span>
                                                    </button>
                                                </form>
                                            @else
                                                <div class="my-1 border-t border-slate-100"></div>
                                                <div class="px-3 py-1.5 text-[10px] text-slate-400 italic">
                                                    Akun Anda (Proteksi aktif)
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                                <div class="max-w-xs mx-auto space-y-2">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-700">Tidak ada data pengguna yang sesuai</p>
                                    <p class="text-[11px] text-slate-400">Silakan ubah filter atau kata kunci pencarian Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Blacklist User Confirmation & Reason -->
<div id="blacklistModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2 text-rose-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <h3 class="text-sm font-bold text-slate-900">Konfirmasi Blacklist Pengguna</h3>
            </div>
            <button type="button" onclick="closeBlacklistModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed">
            Anda akan memasukkan pengguna <strong id="blacklistUserName" class="text-slate-900 font-bold"></strong> ke dalam daftar hitam (blacklist).
            Pengguna yang di-blacklist <strong>tidak dapat login ke sistem</strong> dan <strong>tidak dapat mengajukan lamaran pekerjaan</strong>.
        </p>

        <form id="blacklistForm" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="blacklistReasonInput" class="block text-xs font-semibold text-slate-700 mb-1">Alasan Blacklist <span class="text-rose-500">*</span></label>
                <textarea name="reason" id="blacklistReasonInput" rows="3" required
                          class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 placeholder-slate-400"
                          placeholder="Contoh: Dokumen palsu, pelanggaran aturan tes, atau perilaku tidak pantas..."></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeBlacklistModal()"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/30 transition">
                    Ya, Blacklist Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reset Password -->
<div id="resetPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 animate-in fade-in zoom-in duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2 text-amber-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                </svg>
                <h3 class="text-sm font-bold text-slate-900">Setel Ulang (Reset) Kata Sandi</h3>
            </div>
            <button type="button" onclick="closeResetPasswordModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed">
            Reset kata sandi untuk <strong id="resetUserName" class="text-slate-900 font-bold"></strong> (<span id="resetUserEmail" class="font-mono"></span>).
        </p>

        <form id="resetPasswordForm" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label for="newPasswordInput" class="block text-xs font-semibold text-slate-700 mb-1">
                    Kata Sandi Baru (Opsional)
                </label>
                <input type="text" name="new_password" id="newPasswordInput" minlength="8"
                       class="w-full px-3.5 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 font-mono placeholder-slate-400"
                       placeholder="Kosongkan untuk dibuatkan otomatis (misal: Recruit1234!#)">
                <p class="text-[10px] text-slate-400 mt-1">Jika dikosongkan, sistem akan meng-generate kata sandi aman secara acak.</p>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeResetPasswordModal()"
                        class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md shadow-amber-600/30 transition">
                    Reset Kata Sandi Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let activeDropdownMenu = null;
    let activeDropdownButton = null;

    function toggleUserDropdown(dropdownId, button, event) {
        if (event) {
            event.stopPropagation();
        }

        const menu = document.getElementById(dropdownId);
        if (!menu) return;

        const isCurrentlyOpen = !menu.classList.contains('hidden');

        // Always close all open dropdowns first
        closeAllDropdowns();

        if (isCurrentlyOpen) {
            return;
        }

        // Calculate dynamic position (fixed coords) to prevent overflow clipping by table
        const rect = button.getBoundingClientRect();
        const estimatedHeight = 260;
        const spaceBelow = window.innerHeight - rect.bottom;
        const shouldFlipUp = spaceBelow < estimatedHeight && rect.top > estimatedHeight;

        menu.style.position = 'fixed';
        menu.style.zIndex = '9999';

        if (shouldFlipUp) {
            menu.style.top = 'auto';
            menu.style.bottom = (window.innerHeight - rect.top + 6) + 'px';
        } else {
            menu.style.bottom = 'auto';
            menu.style.top = (rect.bottom + 6) + 'px';
        }

        const rightOffset = window.innerWidth - rect.right;
        menu.style.right = Math.max(12, rightOffset) + 'px';
        menu.style.left = 'auto';

        menu.classList.remove('hidden');
        activeDropdownMenu = menu;
        activeDropdownButton = button;

        button.setAttribute('aria-expanded', 'true');
        const arrow = button.querySelector('.dropdown-arrow');
        if (arrow) {
            arrow.classList.add('rotate-180');
        }
    }

    function closeAllDropdowns() {
        document.querySelectorAll('.user-action-dropdown-menu').forEach(menu => {
            menu.classList.add('hidden');
        });
        document.querySelectorAll('.user-action-dropdown-btn').forEach(btn => {
            btn.setAttribute('aria-expanded', 'false');
            const arrow = btn.querySelector('.dropdown-arrow');
            if (arrow) {
                arrow.classList.remove('rotate-180');
            }
        });
        activeDropdownMenu = null;
        activeDropdownButton = null;
    }

    // Close when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.user-action-dropdown-menu') && !e.target.closest('.user-action-dropdown-btn')) {
            closeAllDropdowns();
        }
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllDropdowns();
        }
    });

    // Close on scroll or resize to prevent detached floating menus
    window.addEventListener('scroll', function() {
        if (activeDropdownMenu) {
            closeAllDropdowns();
        }
    }, true);

    window.addEventListener('resize', function() {
        if (activeDropdownMenu) {
            closeAllDropdowns();
        }
    });

    function openBlacklistModal(userId, userName) {
        closeAllDropdowns();
        document.getElementById('blacklistUserName').textContent = userName;
        document.getElementById('blacklistForm').action = '/admin/users/' + userId + '/blacklist';
        document.getElementById('blacklistReasonInput').value = '';
        document.getElementById('blacklistModal').classList.remove('hidden');
    }

    function closeBlacklistModal() {
        document.getElementById('blacklistModal').classList.add('hidden');
    }

    function openResetPasswordModal(userId, userName, userEmail) {
        closeAllDropdowns();
        document.getElementById('resetUserName').textContent = userName;
        document.getElementById('resetUserEmail').textContent = userEmail;
        document.getElementById('resetPasswordForm').action = '/admin/users/' + userId + '/reset-password';
        document.getElementById('newPasswordInput').value = '';
        document.getElementById('resetPasswordModal').classList.remove('hidden');
    }

    function closeResetPasswordModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
    }
</script>
@endsection
