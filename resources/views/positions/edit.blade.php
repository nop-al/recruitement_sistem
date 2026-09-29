@extends('layouts.dashboard')

@php
    $userRole = auth()->user()?->role?->name ?? 'admin';
    $rolePrefix = match($userRole) {
        'admin' => 'admin',
        'hr' => 'hr',
        default => 'admin',
    };
    $roleLabel = match($userRole) {
        'admin' => 'Administrator',
        'hr' => 'HR Recruiter',
        default => 'Administrator',
    };
@endphp

@section('title', 'Edit Lowongan: ' . $position->title)
@section('role-label', $roleLabel)
@section('page-title', 'Edit Lowongan Pekerjaan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Breadcrumb & Action Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route($rolePrefix . '.dashboard') }}" class="hover:text-indigo-600 font-medium">Dashboard</a>
                <span>/</span>
                <a href="{{ route($rolePrefix . '.positions.index') }}" class="hover:text-indigo-600 font-medium">Lowongan Kerja</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Edit Lowongan</span>
            </div>
            <h2 class="text-xl font-bold text-slate-900">Edit Lowongan: {{ $position->title }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui rincian lowongan kerja, batas pendaftaran, kuota, atau status publikasi</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route($rolePrefix . '.positions.show', $position->id) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <span>Lihat Detail</span>
            </a>
            <a href="{{ route($rolePrefix . '.positions.index') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form action="{{ route($rolePrefix . '.positions.update', $position->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Section 1: Position General Details -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    Informasi Utama Pekerjaan
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <!-- Title -->
                    <div class="sm:col-span-2">
                        <label for="title" class="block font-semibold text-slate-700 mb-1">Judul Posisi Lowongan <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $position->title) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 placeholder-slate-400 font-medium"
                               placeholder="Contoh: Senior Full-Stack Developer (Laravel + React)">
                        @error('title')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department" class="block font-semibold text-slate-700 mb-1">Departemen / Divisi <span class="text-rose-500">*</span></label>
                        <select name="department" id="department" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 bg-white">
                            <option value="">-- Pilih Departemen --</option>
                            @php
                                $deptList = collect($departments ?? []);
                            @endphp
                            @foreach($deptList as $dept)
                                <option value="{{ $dept }}" {{ old('department', $position->department) == $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                            @if($position->department && !$deptList->contains($position->department))
                                <option value="{{ $position->department }}" selected>{{ $position->department }}</option>
                            @endif
                        </select>
                        @error('department')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block font-semibold text-slate-700 mb-1">Lokasi Kerja <span class="text-rose-500">*</span></label>
                        <input type="text" name="location" id="location" value="{{ old('location', $position->location) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 placeholder-slate-400"
                               placeholder="Contoh: Jakarta Selatan (Hybrid) / Remote">
                        @error('location')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Employment Type -->
                    <div>
                        <label for="type" class="block font-semibold text-slate-700 mb-1">Tipe Pekerjaan <span class="text-rose-500">*</span></label>
                        <select name="type" id="type" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 bg-white">
                            <option value="Full-time" {{ old('type', $position->type) == 'Full-time' ? 'selected' : '' }}>Penuh Waktu (Full-time)</option>
                            <option value="Part-time" {{ old('type', $position->type) == 'Part-time' ? 'selected' : '' }}>Paruh Waktu (Part-time)</option>
                            <option value="Contract" {{ old('type', $position->type) == 'Contract' ? 'selected' : '' }}>Kontrak (Contract)</option>
                            <option value="Internship" {{ old('type', $position->type) == 'Internship' ? 'selected' : '' }}>Magang (Internship)</option>
                        </select>
                        @error('type')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Experience Level -->
                    <div>
                        <label for="experience_level" class="block font-semibold text-slate-700 mb-1">Jenjang Pengalaman <span class="text-rose-500">*</span></label>
                        <select name="experience_level" id="experience_level" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 bg-white">
                            <option value="Entry Level (0-1 Tahun)" {{ old('experience_level', $position->experience_level) == 'Entry Level (0-1 Tahun)' ? 'selected' : '' }}>Entry Level (0-1 Tahun)</option>
                            <option value="Mid Level (2-4 Tahun)" {{ old('experience_level', $position->experience_level) == 'Mid Level (2-4 Tahun)' ? 'selected' : '' }}>Mid Level (2-4 Tahun)</option>
                            <option value="Senior Level (4+ Tahun)" {{ old('experience_level', $position->experience_level) == 'Senior Level (4+ Tahun)' ? 'selected' : '' }}>Senior Level (4+ Tahun)</option>
                            <option value="Lead / Manager (5+ Tahun)" {{ old('experience_level', $position->experience_level) == 'Lead / Manager (5+ Tahun)' ? 'selected' : '' }}>Lead / Manager (5+ Tahun)</option>
                        </select>
                        @error('experience_level')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Internal Vacancy Checkbox -->
                    <div class="sm:col-span-2 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="is_internal" value="1" {{ old('is_internal', $position->is_internal) ? 'checked' : '' }}
                                   class="mt-0.5 rounded text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                            <div>
                                <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    Lowongan Kerja Internal (Internal Recruitment / Mutasi)
                                </span>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Centang jika posisi ini dikhususkan bagi karyawan internal. Pelamar yang sudah mencapai tahap <strong>Offering</strong> tetap diperbolehkan melamar posisi internal ini.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section 2: Salary, Quota, and Timeline -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    Kompensasi, Kuota, & Tenggat Waktu
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                    <!-- Salary Min -->
                    <div>
                        <label for="salary_min" class="block font-semibold text-slate-700 mb-1">Gaji Minimal (Rp)</label>
                        <input type="number" name="salary_min" id="salary_min" value="{{ old('salary_min', $position->salary_min) }}" min="0" step="500000"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 font-mono"
                               placeholder="10000000">
                        @error('salary_min')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Salary Max -->
                    <div>
                        <label for="salary_max" class="block font-semibold text-slate-700 mb-1">Gaji Maksimal (Rp)</label>
                        <input type="number" name="salary_max" id="salary_max" value="{{ old('salary_max', $position->salary_max) }}" min="0" step="500000"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900 font-mono"
                               placeholder="18000000">
                        @error('salary_max')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quota -->
                    <div>
                        <label for="quota" class="block font-semibold text-slate-700 mb-1">Kuota Posisi <span class="text-rose-500">*</span></label>
                        <input type="number" name="quota" id="quota" value="{{ old('quota', $position->quota) }}" min="1" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900"
                               placeholder="1">
                        @error('quota')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deadline -->
                    <div>
                        <label for="deadline" class="block font-semibold text-slate-700 mb-1">Batas Akhir Lamaran</label>
                        @php
                            $formattedDeadline = $position->deadline ? \Carbon\Carbon::parse($position->deadline)->format('Y-m-d') : '';
                        @endphp
                        <input type="date" name="deadline" id="deadline" value="{{ old('deadline', $formattedDeadline) }}"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900">
                        @error('deadline')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Detailed Descriptions & Requirements -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    Deskripsi & Kualifikasi
                </h3>

                <div class="space-y-4 text-xs">
                    <!-- Description -->
                    <div>
                        <label for="description" class="block font-semibold text-slate-700 mb-1">Deskripsi Tugas & Tanggung Jawab <span class="text-rose-500">*</span></label>
                        <textarea name="description" id="description" rows="4" required
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900"
                                  placeholder="Jelaskan peran kerja, tanggung jawab utama, dan lingkungan kerja tim...">{{ old('description', $position->description) }}</textarea>
                        @error('description')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Requirements -->
                    <div>
                        <label for="requirements" class="block font-semibold text-slate-700 mb-1">Kualifikasi & Persyaratan Teknis</label>
                        <textarea name="requirements" id="requirements" rows="4"
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 text-slate-900"
                                  placeholder="Daftar keahlian, bahasa pemrograman, sertifikasi, atau pengalaman kerja yang dibutuhkan...">{{ old('requirements', $position->requirements) }}</textarea>
                        @error('requirements')
                            <p class="text-rose-500 text-[11px] mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="pt-1">
                        <label class="block font-semibold text-slate-700 mb-1.5">Status Publikasi Lowongan <span class="text-rose-500">*</span></label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="active" {{ old('status', $position->status) == 'active' ? 'checked' : '' }}
                                       class="text-emerald-600 focus:ring-emerald-500">
                                <span class="font-bold text-emerald-700">Aktif (Menerima Pelamar)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="draft" {{ old('status', $position->status) == 'draft' ? 'checked' : '' }}
                                       class="text-slate-600 focus:ring-slate-500">
                                <span class="font-medium text-slate-700">Draft (Tersimpan Sementara)</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="closed" {{ old('status', $position->status) == 'closed' ? 'checked' : '' }}
                                       class="text-rose-600 focus:ring-rose-500">
                                <span class="font-medium text-rose-700">Ditutup</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="pt-4 border-t border-slate-200/80 flex items-center justify-end gap-3">
                <a href="{{ route($rolePrefix . '.positions.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
