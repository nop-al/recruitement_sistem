@extends('layouts.dashboard')

@section('title', 'Edit Tes Assessment - ' . $assessment->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    @php
        $routePrefix = auth()->user()?->role?->name === 'admin' ? 'admin' : 'hr';
    @endphp

    {{-- Breadcrumbs --}}
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route($routePrefix . '.assessments.index') }}" class="hover:text-indigo-600 transition">&larr; Kembali ke Daftar Assessment</a>
        <span>/</span>
        <a href="{{ route($routePrefix . '.assessments.show', $assessment->id) }}" class="hover:text-indigo-600 transition">{{ $assessment->title }}</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Edit Tes</span>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h1 class="text-xl font-black text-slate-900">Edit Konfigurasi Tes Assessment</h1>
            <p class="text-xs text-slate-500 mt-1">Perbarui informasi umum, durasi ujian, passing score, atau target posisi lowongan.</p>
        </div>

        <form method="POST" action="{{ route($routePrefix . '.assessments.update', $assessment->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Title --}}
            <div>
                <label for="title" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Judul Modul Tes <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title', $assessment->title) }}" required
                       class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('title')
                    <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Row: Test Type & Target Position --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="test_type" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Jenis / Kategori Tes <span class="text-rose-500">*</span>
                    </label>
                    <select name="test_type" id="test_type" required
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($testTypes as $type)
                            <option value="{{ $type }}" {{ old('test_type', $assessment->test_type) === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="position_id" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Posisi Sasaran (Opsional)
                    </label>
                    <select name="position_id" id="position_id"
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Terbuka Untuk Semua Posisi / Umum --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}" {{ old('position_id', $assessment->position_id) == $pos->id ? 'selected' : '' }}>
                                {{ $pos->title }} ({{ $pos->department }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Row: Duration & Passing Score --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="duration" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Durasi Waktu Ujian (Menit) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="duration" id="duration" value="{{ old('duration', $assessment->duration) }}" min="5" max="180" required
                               class="w-full pl-3.5 pr-14 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-medium">Menit</span>
                    </div>
                </div>

                <div>
                    <label for="passing_score" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Passing Score Kelulusan (%) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="passing_score" id="passing_score" value="{{ old('passing_score', $assessment->passing_score) }}" min="1" max="100" required
                               class="w-full pl-3.5 pr-10 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold">%</span>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Petunjuk &amp; Instruksi Pengerjaan Tes
                </label>
                <textarea name="description" id="description" rows="4"
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $assessment->description) }}</textarea>
            </div>

            {{-- Active Status Toggle --}}
            <div class="flex items-center gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $assessment->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                <label for="is_active" class="text-xs font-semibold text-slate-700 select-none cursor-pointer">
                    Status Aktif (Dapat diakses oleh kandidat)
                </label>
            </div>

            {{-- Actions --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route($routePrefix . '.assessments.show', $assessment->id) }}" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
