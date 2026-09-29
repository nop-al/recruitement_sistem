@extends('layouts.dashboard')

@section('title', 'Terbitkan Surat Penawaran Kerja (Offering)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @php
        $routePrefix = auth()->user()?->role?->name === 'admin' ? 'admin' : 'hr';
    @endphp

    {{-- Breadcrumbs --}}
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route($routePrefix . '.offerings.index') }}" class="hover:text-emerald-600 transition">&larr; Kembali ke Daftar Offering</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Terbitkan Offering Baru</span>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h1 class="text-xl font-black text-slate-900">Form Surat Penawaran Kerja (Offering Letter)</h1>
            <p class="text-xs text-slate-500 mt-1">Lengkapi rincian gaji, posisi, tanggal mulai bergabung, fasilitas/benefit, dan ketentuan kerja resmi.</p>
        </div>

        <form method="POST" action="{{ route($routePrefix . '.offerings.store') }}" class="space-y-6">
            @csrf

            @if($selectedApplication)
                <input type="hidden" name="candidate_application_id" value="{{ $selectedApplication->id }}">
                <input type="hidden" name="candidate_id" value="{{ $selectedApplication->candidate_id }}">
                <input type="hidden" name="position_id" value="{{ $selectedApplication->position_id }}">

                {{-- Selected Candidate & Position Info Callout --}}
                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-sm shadow-emerald-600/30">
                            {{ strtoupper(substr($selectedApplication->candidate->full_name, 0, 2)) }}
                        </div>
                        <div>
                            <span class="text-[10px] uppercase tracking-wider text-emerald-800 font-bold block">Kandidat Terpilih:</span>
                            <h3 class="text-sm font-black text-slate-900">{{ $selectedApplication->candidate->full_name }}</h3>
                            <p class="text-xs text-slate-500">{{ $selectedApplication->position->title }} &bull; {{ $selectedApplication->position->department }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-200 text-emerald-900">
                        Dari Lamaran Terverifikasi
                    </span>
                </div>
            @else
                {{-- Row: Candidate & Position Selection --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="candidate_id" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Pilih Kandidat Pelamar <span class="text-rose-500">*</span>
                        </label>
                        <select name="candidate_id" id="candidate_id" required
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Pilih Kandidat Terdaftar --</option>
                            @foreach($candidates as $cand)
                                <option value="{{ $cand->id }}" {{ old('candidate_id') == $cand->id ? 'selected' : '' }}>
                                    {{ $cand->full_name }} ({{ $cand->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('candidate_id')
                            <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="position_id" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Posisi Pekerjaan (Position) <span class="text-rose-500">*</span>
                        </label>
                        <select name="position_id" id="position_id" required
                                onchange="autoFillDepartment(this)"
                                class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Pilih Posisi Lowongan --</option>
                            @foreach($positions as $pos)
                                <option value="{{ $pos->id }}" data-dept="{{ $pos->department }}" {{ old('position_id') == $pos->id ? 'selected' : '' }}>
                                    {{ $pos->title }} ({{ $pos->department }})
                                </option>
                            @endforeach
                        </select>
                        @error('position_id')
                            <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif

            {{-- Row: Department & Employment Type --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="department" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Departemen / Divisi Kerja <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="department" id="department"
                           value="{{ old('department', $selectedApplication?->position?->department ?? '') }}" required
                           placeholder="Contoh: Technology & Engineering"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @error('department')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="employment_type" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Tipe Ikatan Kerja (Employment Type) <span class="text-rose-500">*</span>
                    </label>
                    <select name="employment_type" id="employment_type" required
                            class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @foreach($employmentTypes as $type)
                            <option value="{{ $type }}" {{ old('employment_type', 'Full-time') === $type ? 'selected' : '' }}>
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
                        <input type="number" name="salary" id="salary" value="{{ old('salary') }}" min="0" step="100000" required
                               placeholder="15000000"
                               class="w-full pl-10 pr-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    @error('salary')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="join_date" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Tanggal Mulai Bekerja (Join Date) <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="join_date" id="join_date" value="{{ old('join_date', \Carbon\Carbon::now()->addDays(14)->format('Y-m-d')) }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    @error('join_date')
                        <p class="text-[11px] text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Benefits & Facilities --}}
            <div>
                <label for="benefits" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Fasilitas, Tunjangan &amp; Benefit Karyawan
                </label>
                <textarea name="benefits" id="benefits" rows="4"
                          placeholder="Tuliskan daftar benefit, misalnya:
- BPJS Kesehatan & Ketenagakerjaan
- Asuransi Rawat Inap Swasta (Keluarga)
- Tunjangan Transportasi & Internet
- Laptop Perusahaan MacBook Pro
- Bonus Tahunan Berdasarkan Kinerja"
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('benefits', "- BPJS Kesehatan & Ketenagakerjaan\n- Asuransi Rawat Inap Swasta\n- Tunjangan Transportasi & Komunikasi\n- Laptop Kerja Perusahaan\n- Bonus Kinerja Tahunan") }}</textarea>
            </div>

            {{-- Notes / Terms --}}
            <div>
                <label for="notes" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Catatan Khusus / Ketentuan Tambahan
                </label>
                <textarea name="notes" id="notes" rows="3"
                          placeholder="Penawaran ini berlaku hingga 7 hari kerja sejak diterbitkan. Kandidat wajib menandatangani surat penawaran dan melengkapi berkas onboarding..."
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('notes') }}</textarea>
            </div>

            {{-- Status Initializer --}}
            <div>
                <label for="status" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Status Dokumen Penawaran <span class="text-rose-500">*</span>
                </label>
                <select name="status" id="status" required
                        class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft (Disimpan Internal Terlebih Dahulu)</option>
                    <option value="sent" {{ old('status') === 'sent' ? 'selected' : '' }}>Sent (Langsung Kirimkan ke Portal Kandidat)</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route($routePrefix . '.offerings.index') }}" class="px-4 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/30 transition">
                    Simpan &amp; Terbitkan Offering &rarr;
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function autoFillDepartment(selectEl) {
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const dept = selectedOption.getAttribute('data-dept');
        const deptInput = document.getElementById('department');
        if (dept && deptInput && !deptInput.value) {
            deptInput.value = dept;
        }
    }
</script>
@endsection
