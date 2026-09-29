@extends('layouts.dashboard')

@section('title', 'Tambah Pengguna Baru')
@section('role-label', 'Administrator')
@section('page-title', 'Formulir Tambah Akun Pengguna')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Breadcrumb & Action Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 font-medium">Admin Dashboard</a>
                <span>/</span>
                <a href="{{ route('admin.users.index') }}" class="hover:text-indigo-600 font-medium">Pengguna Sistem</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Tambah Pengguna</span>
            </div>
            <h2 class="text-xl font-bold text-slate-900">Buat Akun Pengguna Baru</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftarkan akun untuk Administrator, HR Recruiter, Interviewer, atau Kandidat</p>
        </div>

        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Basic Information -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    Informasi Profil & Kredensial
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Full Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 placeholder-slate-400 font-medium"
                               placeholder="Contoh: Budi Pratama, S.Kom">
                        @error('name')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block font-semibold text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 placeholder-slate-400"
                               placeholder="nama@perusahaan.com">
                        @error('email')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone_number" class="block font-semibold text-slate-700 mb-1">Nomor HP / WhatsApp</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 placeholder-slate-400"
                               placeholder="08123456789">
                        @error('phone_number')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIK (Optional on create, required for candidate) -->
                    <div class="sm:col-span-2">
                        <label for="nik" class="block font-semibold text-slate-700 mb-1">Nomor Induk Kependudukan (NIK KTP) <span class="text-[11px] text-blue-600 font-normal">(1 NIK untuk 1 akun kandidat)</span></label>
                        <input type="text" name="nik" id="nik" value="{{ old('nik') }}" maxlength="16"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 placeholder-slate-400 font-mono"
                               placeholder="16 digit NIK KTP resmi jika membuat akun kandidat">
                        @error('nik')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

            <!-- Section 2: Role & Permission Assignment -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    Peran (Role) & Hak Akses
                </h3>

                <div class="text-xs space-y-3">
                    <label class="block font-semibold text-slate-700 mb-1">Pilih Peran Sistem <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($roles as $role)
                            <label class="relative flex items-start p-3.5 rounded-xl border border-slate-200 hover:border-indigo-300 cursor-pointer transition bg-slate-50/50 hover:bg-white has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/40 has-[:checked]:ring-1 has-[:checked]:ring-indigo-600">
                                <input type="radio" name="role_id" value="{{ $role->id }}" {{ old('role_id', 2) == $role->id ? 'checked' : '' }}
                                       class="mt-0.5 text-indigo-600 focus:ring-indigo-500">
                                <div class="ml-3">
                                    <div class="font-bold text-slate-900">{{ $role->display_name }}</div>
                                    <p class="text-[11px] text-slate-500 mt-0.5 leading-snug">{{ $role->description }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('role_id')
                        <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Section 3: Password & Security -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    Pengaturan Keamanan Sandi
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label for="password" class="block font-semibold text-slate-700 mb-1">Kata Sandi (Min. 8 Karakter) <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900"
                               placeholder="••••••••">
                        @error('password')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block font-semibold text-slate-700 mb-1">Ulangi Kata Sandi <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900"
                               placeholder="••••••••">
                    </div>
                </div>

                <!-- Active status checkbox -->
                <div class="mt-4 pt-2">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked
                               class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                        <span class="text-xs font-semibold text-slate-700">Aktifkan akun ini segera (dapat langsung masuk ke sistem)</span>
                    </label>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="pt-4 border-t border-slate-200/80 flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan & Buat Akun
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
