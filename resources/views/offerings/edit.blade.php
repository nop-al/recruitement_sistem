@extends('layouts.dashboard')

@section('title', 'Edit Surat Penawaran - ' . $offering->candidate?->full_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @php
        $routePrefix = auth()->user()?->role?->name === 'admin' ? 'admin' : 'hr';
    @endphp

    {{-- Breadcrumbs --}}
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route($routePrefix . '.offerings.index') }}" class="hover:text-emerald-600 transition">&larr; Kembali ke Daftar Offering</a>
        <span>/</span>
        <a href="{{ route($routePrefix . '.offerings.show', $offering->id) }}" class="hover:text-emerald-600 transition">Offering {{ $offering->candidate?->full_name }}</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Edit Penawaran</span>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h1 class="text-xl font-black text-slate-900">Perbarui Surat Penawaran Kerja</h1>
            <p class="text-xs text-slate-500 mt-1">Sesuaikan nominal gaji, tanggal masuk kerja, benefit, atau ubah status penawaran.</p>
        </div>

        <form method="POST" action="{{ route($routePrefix . '.offerings.update', $offering->id) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Candidate and Position Read-only Preview --}}
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold block">Kandidat Pelamar:</span>
                    <h3 class="text-sm font-black text-slate-900">{{ $offering->candidate?->full_name }}</h3>
                    <p class="text-xs text-slate-500">{{ $offering->candidate?->email }} &bull; Posisi: {{ $offering->position?->title }}</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-200 text-slate-700">
                    ID #OFF-{{ str_pad($offering->id, 5, '0', STR_PAD_LEFT) }}
                </span>
            </div>

            {{-- Row: Department & Employment Type --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="department" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Departemen / Divisi Kerja <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="department" id="department"
                           value="{{ old('department', $offering->department ?: $offering->position?->department) }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label for="employment_type" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Tipe Ikatan Kerja (Employment Type) <span class="text-rose-500">*</span>
                    </label>
                    <select name="employment_type" id="employment_type" required
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @foreach($employmentTypes as $type)
                            <option value="{{ $type }}" {{ old('employment_type', $offering->employment_type) === $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Row: Salary & Join Date --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="salary" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Besaran Gaji Pokok (Gross / Bulan) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" name="salary" id="salary"
                               value="{{ old('salary', (int)$offering->effective_salary) }}" min="0" step="100000" required
                               class="w-full pl-10 pr-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label for="join_date" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Tanggal Mulai Bekerja (Join Date) <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="join_date" id="join_date"
                           value="{{ old('join_date', $offering->effective_join_date ? \Carbon\Carbon::parse($offering->effective_join_date)->format('Y-m-d') : '') }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            {{-- Benefits & Facilities --}}
            <div>
                <label for="benefits" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Fasilitas, Tunjangan &amp; Benefit Karyawan
                </label>
                <textarea name="benefits" id="benefits" rows="4"
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('benefits', $offering->benefits) }}</textarea>
            </div>

            {{-- Notes / Terms --}}
            <div>
                <label for="notes" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Catatan Khusus / Ketentuan Tambahan
                </label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('notes', $offering->notes) }}</textarea>
            </div>

            {{-- Status --}}
            <div>
                <label for="status" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Status Offering <span class="text-rose-500">*</span>
                </label>
                <select name="status" id="status" required
                        class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" {{ old('status', $offering->status) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Actions --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route($routePrefix . '.offerings.show', $offering->id) }}" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/30 transition">
                    Simpan Perubahan &rarr;
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
