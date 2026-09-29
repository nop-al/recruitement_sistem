@extends('layouts.dashboard')

@section('title', 'Edit Profil Pengguna')
@section('role-label', 'Administrator')
@section('page-title', 'Edit Profil & Akses Pengguna')

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
                <span class="text-slate-800 font-semibold">Edit Profil</span>
            </div>
            <h2 class="text-xl font-bold text-slate-900">Edit Profil: {{ $user->name }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi akun, verifikasi NIK pelamar, serta status keamanan pengguna</p>
        </div>

        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    @php
        $candidate = $user->candidate;
        $profile = $candidate?->profile;
        $isSelf = (auth()->id() === $user->id);
    @endphp

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Basic Information -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    Informasi Profil & Kredensial Pengguna
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Full Name -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 placeholder-slate-400 font-medium">
                        @error('name')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block font-semibold text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 placeholder-slate-400">
                        @error('email')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone_number" class="block font-semibold text-slate-700 mb-1">Nomor HP / WhatsApp</label>
                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $candidate?->phone_number) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 placeholder-slate-400"
                               placeholder="08123456789">
                        @error('phone_number')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Role & Candidate Data -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    Peran (Role) & Verifikasi NIK
                </h3>

                <div class="text-xs space-y-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-2">Pilih Peran Sistem <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($roles as $role)
                                <label class="relative flex items-start p-3.5 rounded-xl border border-slate-200 hover:border-indigo-300 cursor-pointer transition bg-slate-50/50 hover:bg-white has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/40 has-[:checked]:ring-1 has-[:checked]:ring-indigo-600">
                                    <input type="radio" name="role_id" value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'checked' : '' }}
                                           onchange="toggleNikField(this.value)"
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

                    <!-- NIK Input (Relevant for candidate role) -->
                    <div id="nikFieldContainer" class="p-4 rounded-xl bg-blue-50/50 border border-blue-200">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="nik" class="block font-bold text-slate-800">Nomor Induk Kependudukan (NIK KTP)</label>
                            <span class="text-[10px] font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">1 NIK untuk 1 Akun</span>
                        </div>
                        <input type="text" name="nik" id="nik" maxlength="16"
                               value="{{ old('nik', $profile?->nik) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-slate-900 font-mono text-xs placeholder-slate-400"
                               placeholder="16 digit NIK KTP resmi kandidat">
                        <p class="text-[11px] text-slate-500 mt-1">Sesuai KTP (16 digit angka). Digunakan untuk validasi bahwa 1 kandidat hanya memiliki 1 akun dan melamar pekerjaan.</p>
                        @error('nik')
                            <p class="text-rose-600 text-[11px] mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Account Status & Blacklist -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    Status Akun & Keamanan (Blacklist)
                </h3>

                <div class="space-y-4 text-xs">
                    <!-- Is Active Toggle -->
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-100/60 transition">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                        <div>
                            <span class="font-bold text-slate-900 block">Akun Pengguna Aktif</span>
                            <span class="text-[11px] text-slate-500">Jika dinonaktifkan, pengguna tidak dapat masuk ke sistem.</span>
                        </div>
                    </label>

                    <!-- Blacklist Toggle -->
                    <div class="p-4 rounded-xl border border-rose-200 bg-rose-50/40 space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="is_blacklisted" id="isBlacklistedCheck" value="1"
                                   {{ old('is_blacklisted', $user->is_blacklisted) ? 'checked' : '' }}
                                   {{ $isSelf ? 'disabled' : '' }}
                                   onchange="document.getElementById('blacklistReasonBox').classList.toggle('hidden', !this.checked)"
                                   class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-rose-300 mt-0.5">
                            <div>
                                <span class="font-bold text-rose-900 block">Daftar Hitam (Blacklist) Pengguna Ini</span>
                                <span class="text-[11px] text-rose-700 leading-relaxed">
                                    Pengguna yang di-blacklist akan diblokir dari login dan tidak dapat mengajukan lamaran pekerjaan apapun.
                                    @if($isSelf)
                                        <strong class="text-rose-800 block mt-0.5">(Anda tidak dapat mem-blacklist akun Anda sendiri)</strong>
                                    @endif
                                </span>
                            </div>
                        </label>

                        <div id="blacklistReasonBox" class="{{ old('is_blacklisted', $user->is_blacklisted) ? '' : 'hidden' }} pt-2 border-t border-rose-200/80">
                            <label for="blacklist_reason" class="block font-bold text-rose-900 mb-1">Alasan Blacklist</label>
                            <textarea name="blacklist_reason" id="blacklist_reason" rows="2"
                                      class="w-full px-3.5 py-2 text-xs rounded-xl border border-rose-300 bg-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 placeholder-slate-400"
                                      placeholder="Tuliskan catatan alasan blacklist...">{{ old('blacklist_reason', $user->blacklist_reason) }}</textarea>
                            @if($user->blacklisted_at)
                                <p class="text-[10px] text-rose-600 mt-1">Ditandai blacklist sejak: {{ $user->blacklisted_at->format('d M Y, H:i') }} WIB</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md shadow-indigo-600/30 transition">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function toggleNikField(roleId) {
        // Find if selected role is candidate
        // Handled naturally by backend if submitted
    }
</script>
@endsection
