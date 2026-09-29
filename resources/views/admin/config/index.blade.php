@extends('layouts.dashboard')

@section('title', 'Konfigurasi & Pengaturan Pusat Sistem')
@section('role-label', 'Administrator')
@section('page-title', 'Konfigurasi & Pengaturan Pusat Sistem')

@section('content')
<div class="space-y-6">

    <!-- Top Action & Overview Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 font-medium">Admin Dashboard</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Pengaturan Pusat</span>
            </div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Konfigurasi & Pengaturan Pusat Sistem</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola identitas aplikasi, branding, struktur divisi, pencadangan database, dan pemeliharaan performa</p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-600 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Laravel {{ $laravelVersion }} • PHP {{ $phpVersion }}</span>
            </span>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-2xl border border-slate-200 p-1.5 shadow-sm">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-1.5">
            <a href="{{ route('admin.config.index', ['tab' => 'identity']) }}"
               class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ $activeTab === 'identity' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <span>Identitas & Logo Sistem</span>
            </a>

            <a href="{{ route('admin.config.index', ['tab' => 'departments']) }}"
               class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ $activeTab === 'departments' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span>Departemen & Divisi</span>
            </a>

            <a href="{{ route('admin.config.index', ['tab' => 'backup']) }}"
               class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ $activeTab === 'backup' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                </svg>
                <span>Backup & Restore Sistem</span>
            </a>

            <a href="{{ route('admin.config.index', ['tab' => 'cache']) }}"
               class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold transition {{ $activeTab === 'cache' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
                <span>Pembersihan Cache</span>
            </a>
        </div>
    </div>

    <!-- TAB 1: IDENTITAS & LOGO SISTEM -->
    @if($activeTab === 'identity')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- System Identity Form -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Identitas & Informasi Aplikasi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Atur nama sistem rekrutmen, informasi versi aplikasi, dan identitas perusahaan</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
            </div>

            <form action="{{ route('admin.config.identity.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Sistem / Aplikasi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="app_name" value="{{ old('app_name', $settings['app_name'] ?? 'Sistem Rekrutmen') }}" required
                               placeholder="e.g. RecruitPro Enterprise"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-xs font-semibold text-slate-900 transition">
                        @error('app_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nomor Versi Aplikasi
                        </label>
                        <input type="text" name="app_version" value="{{ old('app_version', $settings['app_version'] ?? '1.0.0') }}"
                               placeholder="e.g. 2.1.0"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-xs font-semibold text-slate-900 transition">
                        @error('app_version') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Tagline / Slogan Aplikasi
                    </label>
                    <input type="text" name="app_tagline" value="{{ old('app_tagline', $settings['app_tagline'] ?? '') }}"
                           placeholder="e.g. Platform Terintegrasi Rekrutmen & Manajemen Talenta Terbaik"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-xs font-semibold text-slate-900 transition">
                    @error('app_tagline') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Perusahaan / Organisasi
                        </label>
                        <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? '') }}"
                               placeholder="e.g. PT Teknologi Digital Nusantara"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-xs font-semibold text-slate-900 transition">
                        @error('company_name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Teks Footer / Hak Cipta
                        </label>
                        <input type="text" name="footer_text" value="{{ old('footer_text', $settings['footer_text'] ?? '') }}"
                               placeholder="e.g. Hak Cipta Dilindungi Undang-Undang"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-xs font-semibold text-slate-900 transition">
                        @error('footer_text') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex justify-end">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Simpan Perubahan Identitas</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Logo & Brand Mark -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Logo Sistem</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Kustomisasi logo resmi pada header portal</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>

            <!-- Current Logo Preview -->
            <div class="flex flex-col items-center justify-center p-6 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-center">
                @if(!empty($settings['app_logo']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($settings['app_logo']))
                    <img src="{{ asset('storage/' . $settings['app_logo']) }}" alt="Logo Sistem" class="h-16 max-w-full object-contain mb-3 drop-shadow-sm">
                    <span class="text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200">
                        Logo Kustom Aktif
                    </span>
                @else
                    <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-2xl shadow-lg shadow-indigo-600/30 mb-3">
                        {{ strtoupper(substr($settings['app_name'] ?? 'HR', 0, 2)) }}
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500 bg-slate-200/60 px-2.5 py-1 rounded-md">
                        Menggunakan Brand Mark Bawaan
                    </span>
                @endif
            </div>

            <!-- Upload Logo Form -->
            <form action="{{ route('admin.config.logo.update') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Pilih Berkas Logo Baru (PNG, JPG, SVG, WEBP • Max 2MB)
                    </label>
                    <input type="file" name="logo" required accept="image/*"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition border border-slate-200 rounded-xl cursor-pointer">
                    @error('logo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    <span>Unggah & Terapkan Logo</span>
                </button>
            </form>

            @if(!empty($settings['app_logo']))
            <form action="{{ route('admin.config.logo.destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mereset logo kustom ke logo bawaan?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-600 font-semibold text-xs transition border border-slate-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    <span>Reset ke Logo Bawaan</span>
                </button>
            </form>
            @endif
        </div>
    </div>
    @endif

    <!-- TAB 2: DEPARTEMEN & DIVISI -->
    @if($activeTab === 'departments')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add New Department Form -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5 h-fit">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Tambah Departemen / Divisi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftarkan divisi baru untuk klasifikasi posisi lowongan</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
            </div>

            <form action="{{ route('admin.config.departments.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Nama Departemen / Divisi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Technology & Engineering"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-xs font-semibold text-slate-900 transition">
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Kode Divisi (Opsional)
                    </label>
                    <input type="text" name="code" value="{{ old('code') }}"
                           placeholder="e.g. TECH, HRD, FIN"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-xs font-semibold text-slate-900 transition uppercase">
                    @error('code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Deskripsi / Keterangan
                    </label>
                    <textarea name="description" rows="3"
                              placeholder="Deskripsi singkat fungsi departemen..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 text-xs text-slate-900 transition">{{ old('description') }}</textarea>
                    @error('description') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Simpan Departemen Baru</span>
                </button>
            </form>
        </div>

        <!-- Department List Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Daftar Departemen & Divisi Aktif</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Total {{ count($departments) }} departemen terdaftar dalam organisasi</p>
                </div>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-5">Departemen</th>
                            <th class="py-3 px-4">Kode</th>
                            <th class="py-3 px-4">Jumlah Lowongan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($departments as $dept)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-5">
                                <div class="font-bold text-slate-900">{{ $dept->name }}</div>
                                @if($dept->description)
                                    <div class="text-[11px] text-slate-500 mt-0.5 max-w-sm truncate">{{ $dept->description }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($dept->code)
                                    <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 font-mono text-[11px] font-bold text-slate-700 border border-slate-200">
                                        {{ $dept->code }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-50 text-indigo-700">
                                    {{ $dept->positions_count ?? 0 }} Posisi
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <form action="{{ route('admin.config.departments.destroy', $dept->id) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus departemen \'{{ $dept->name }}\'?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Departemen">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-slate-400">
                                <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <p class="text-xs font-semibold">Belum ada departemen yang ditambahkan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- TAB 3: BACKUP & RESTORE SISTEM -->
    @if($activeTab === 'backup')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Backup Actions Card -->
        <div class="space-y-6">
            <!-- Create Snapshot -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Cadangkan Database</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Buat snapshot instan data rekrutmen</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                    </div>
                </div>

                <div class="text-xs text-slate-600 bg-slate-50 p-3.5 rounded-xl border border-slate-200">
                    <p class="font-semibold text-slate-800 mb-1">Koneksi Database Aktif: <span class="font-mono text-indigo-600 uppercase">{{ $dbConnection }}</span></p>
                    <p class="text-[11px] text-slate-500 leading-relaxed">Snapshot mencakup seluruh data pengguna, pelamar, penilaian ujian, wawancara, offering letter, dan onboarding.</p>
                </div>

                <form action="{{ route('admin.config.backup.create') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>Buat File Backup Baru</span>
                    </button>
                </form>
            </div>

            <!-- Restore Database -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Restore Sistem</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pulihkan database dari file cadangan</p>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                </div>

                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 text-[11px] leading-relaxed">
                    <strong>Peringatan:</strong> Proses restore akan menimpa data yang ada saat ini. Pastikan Anda telah membuat cadangan terbaru sebelum melakukan tindakan ini.
                </div>

                <form action="{{ route('admin.config.backup.restore') }}" method="POST" enctype="multipart/form-data"
                      onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin memulihkan database dari file ini? Seluruh data yang belum dicadangkan akan ditimpa!');"
                      class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Pilih File Cadangan (.sql / .sqlite • Max 50MB)
                        </label>
                        <input type="file" name="backup_file" required accept=".sql,.sqlite"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100 transition border border-slate-200 rounded-xl cursor-pointer">
                        @error('backup_file') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md shadow-amber-600/30 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        <span>Pulihkan Database (Restore)</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Backup Files Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Berkas Cadangan (Backup Snapshots)</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar arsip database yang tersimpan di server</p>
                </div>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-5">Nama Berkas</th>
                            <th class="py-3 px-4">Ukuran</th>
                            <th class="py-3 px-4">Waktu Dibuat</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($backupFiles as $file)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="font-mono font-semibold text-slate-800">{{ $file['filename'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-slate-600 font-medium">
                                {{ $file['size'] }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                                {{ $file['created_at']->format('d M Y, H:i') }} WIB
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.config.backup.download', $file['filename']) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 font-semibold text-[11px] transition" title="Unduh File">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        <span>Unduh</span>
                                    </a>

                                    <form action="{{ route('admin.config.backup.destroy', $file['filename']) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus file backup ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" title="Hapus Cadangan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-slate-400">
                                <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                </svg>
                                <p class="text-xs font-semibold">Belum ada file backup di server. Klik "Buat File Backup Baru" untuk memulai.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- TAB 4: PEMBERSIHAN CACHE -->
    @if($activeTab === 'cache')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cache Clearing Options -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Pembersihan Cache Sistem & Optimasi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Segarkan memori cache aplikasi setelah melakukan perubahan konfigurasi atau rute</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- All Cache Clear (Recommended) -->
                <div class="p-5 rounded-xl border-2 border-indigo-200 bg-indigo-50/50 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-xs text-indigo-950 uppercase tracking-wider">Bersihkan Seluruh Cache (All)</h4>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-600 text-white">Rekomendasi</span>
                        </div>
                        <p class="text-xs text-indigo-900/70 mb-4 leading-relaxed">
                            Menghapus seluruh file kompilasi view blade, routing cache, config cache, dan memori data sementara (optimize:clear).
                        </p>
                    </div>
                    <form action="{{ route('admin.config.cache.clear') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="all">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Bersihkan Seluruh Cache</span>
                        </button>
                    </form>
                </div>

                <!-- View Cache Clear -->
                <div class="p-5 rounded-xl border border-slate-200 bg-slate-50 flex flex-col justify-between">
                    <div>
                        <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider mb-2">Cache Tampilan (Blade Views)</h4>
                        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                            Membersihkan cache hasil render file Blade template. Sangat berguna jika perubahan tampilan belum muncul.
                        </p>
                    </div>
                    <form action="{{ route('admin.config.cache.clear') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="views">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 font-bold text-xs transition shadow-sm">
                            <span>Bersihkan Cache Views</span>
                        </button>
                    </form>
                </div>

                <!-- Route Cache Clear -->
                <div class="p-5 rounded-xl border border-slate-200 bg-slate-50 flex flex-col justify-between">
                    <div>
                        <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider mb-2">Cache Rute (Route Cache)</h4>
                        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                            Membersihkan registrasi tabel rute URL aplikasi jika terdapat penambahan endpoint rute baru.
                        </p>
                    </div>
                    <form action="{{ route('admin.config.cache.clear') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="routes">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 font-bold text-xs transition shadow-sm">
                            <span>Bersihkan Cache Routes</span>
                        </button>
                    </form>
                </div>

                <!-- Config Cache Clear -->
                <div class="p-5 rounded-xl border border-slate-200 bg-slate-50 flex flex-col justify-between">
                    <div>
                        <h4 class="font-bold text-xs text-slate-800 uppercase tracking-wider mb-2">Cache Konfigurasi (Config Cache)</h4>
                        <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                            Memuat ulang seluruh file `.env` dan direktori `config/` ke dalam cache runtime sistem.
                        </p>
                    </div>
                    <form action="{{ route('admin.config.cache.clear') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="config">
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 font-bold text-xs transition shadow-sm">
                            <span>Bersihkan Cache Config</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Environment Specs Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5 h-fit">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Informasi Server</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Spesifikasi lingkungan runtime sistem</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                    </svg>
                </div>
            </div>

            <div class="space-y-3 text-xs">
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <span class="text-slate-500 font-medium">Laravel Framework</span>
                    <span class="font-mono font-bold text-slate-900">{{ $laravelVersion }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <span class="text-slate-500 font-medium">PHP Engine</span>
                    <span class="font-mono font-bold text-slate-900">{{ $phpVersion }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <span class="text-slate-500 font-medium">Database Driver</span>
                    <span class="font-mono font-bold text-indigo-600 uppercase">{{ $dbConnection }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50">
                    <span class="text-slate-500 font-medium">Environment</span>
                    <span class="font-mono font-bold text-emerald-600 uppercase">{{ app()->environment() }}</span>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
