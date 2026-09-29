@extends('layouts.dashboard')

@section('title', 'Portal Onboarding Karyawan Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Hero Header --}}
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-cyan-900 via-blue-950 to-slate-900 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-3xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 text-xs font-semibold border border-cyan-400/30 mb-3">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                Tahap Akhir Menuju Hari Pertama Kerja
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Portal Onboarding Karyawan</h1>
            <p class="text-xs sm:text-sm text-cyan-100/80 mt-1.5 leading-relaxed">
                Selamat atas keberhasilan Anda bergabung bersama kami! Silakan lengkapi 8 berkas dan informasi checklist di bawah ini untuk kelancaran administrasi HR, payroll gaji, dan asuransi ketenagakerjaan.
            </p>
        </div>
    </div>

    @if(! $onboarding)
        <div class="p-12 rounded-3xl bg-white border border-slate-200 text-center text-xs text-slate-500 shadow-sm">
            <div class="w-16 h-16 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">Tahap Onboarding Belum Dimulai</h3>
            <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                Anda perlu menerima surat penawaran kerja (Offering Letter) terlebih dahulu untuk membuka akses ke portal onboarding ini.
            </p>
            <a href="{{ route('kadidat.offerings.index') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-500">
                Lihat Penawaran Kerja &rarr;
            </a>
        </div>
    @else
        {{-- Progress Bar Card --}}
        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-black text-slate-900">Progres Kelengkapan Checklist</h3>
                    <p class="text-xs text-slate-500">Persentase dihitung otomatis berdasarkan 8 dokumen &amp; data yang Anda kirimkan.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xl font-black text-cyan-600">{{ $onboarding->checklist_percentage }}%</span>
                    <span class="text-xs font-bold text-slate-400">/ 100%</span>
                </div>
            </div>

            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                <div class="h-3 rounded-full transition-all duration-500
                    @if($onboarding->checklist_percentage >= 100) bg-emerald-500
                    @elseif($onboarding->checklist_percentage >= 50) bg-cyan-500
                    @else bg-blue-500 @endif"
                    style="width: {{ $onboarding->checklist_percentage }}%"></div>
            </div>

            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1">
                <span>Posisi: <strong class="text-slate-800">{{ $onboarding->position?->title }}</strong></span>
                <span>Mulai Masuk: <strong class="text-slate-800">{{ $onboarding->join_date ? \Carbon\Carbon::parse($onboarding->join_date)->translatedFormat('d F Y') : '-' }}</strong></span>
            </div>
        </div>

        {{-- Submission Form for all 8 Checklist Items --}}
        <form method="POST" action="{{ route('kadidat.onboarding.submit', $onboarding->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- 1. KTP --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-cyan-600 text-white font-black text-xs flex items-center justify-center">1</span>
                        <h3 class="text-sm font-black text-slate-900">Kartu Tanda Penduduk (KTP)</h3>
                    </div>
                    @if($onboarding->ktp_verified)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi HR</span>
                    @elseif($onboarding->ktp_number || $onboarding->ktp_file)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800">Sudah Diisi (Menunggu Verifikasi)</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Wajib Diisi</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="ktp_number" class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Induk Kependudukan (NIK / KTP)</label>
                        <input type="text" name="ktp_number" id="ktp_number" value="{{ old('ktp_number', $onboarding->ktp_number) }}"
                               placeholder="16 Digit NIK KTP..."
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="ktp_file" class="block text-xs font-bold text-slate-700 mb-1.5">Unggah Foto / Scan KTP (PDF/JPG/PNG, Max 4MB)</label>
                        <input type="file" name="ktp_file" id="ktp_file" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                        @if($onboarding->ktp_file)
                            <div class="mt-1 text-[11px]">
                                <a href="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'ktp', 'view' => 1]) }}" target="_blank" class="text-blue-600 font-semibold hover:underline">Lihat file KTP tersimpan &rarr;</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. NPWP --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-cyan-600 text-white font-black text-xs flex items-center justify-center">2</span>
                        <h3 class="text-sm font-black text-slate-900">Nomor Pokok Wajib Pajak (NPWP)</h3>
                    </div>
                    @if($onboarding->npwp_verified)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi HR</span>
                    @elseif($onboarding->npwp_number || $onboarding->npwp_file)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800">Sudah Diisi (Menunggu Verifikasi)</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Wajib Diisi</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="npwp_number" class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Kartu NPWP</label>
                        <input type="text" name="npwp_number" id="npwp_number" value="{{ old('npwp_number', $onboarding->npwp_number) }}"
                               placeholder="Contoh: 12.345.678.9-012.000"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="npwp_file" class="block text-xs font-bold text-slate-700 mb-1.5">Unggah Foto / Scan NPWP (PDF/JPG/PNG)</label>
                        <input type="file" name="npwp_file" id="npwp_file" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                        @if($onboarding->npwp_file)
                            <div class="mt-1 text-[11px]">
                                <a href="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'npwp', 'view' => 1]) }}" target="_blank" class="text-blue-600 font-semibold hover:underline">Lihat file NPWP tersimpan &rarr;</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 3. BPJS --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-cyan-600 text-white font-black text-xs flex items-center justify-center">3</span>
                        <h3 class="text-sm font-black text-slate-900">BPJS (Kesehatan / Ketenagakerjaan)</h3>
                    </div>
                    @if($onboarding->bpjs_verified)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi HR</span>
                    @elseif($onboarding->bpjs_number || $onboarding->bpjs_file)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800">Sudah Diisi</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Opsional / Diurus HR</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="bpjs_number" class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Kartu BPJS (Jika sudah memiliki)</label>
                        <input type="text" name="bpjs_number" id="bpjs_number" value="{{ old('bpjs_number', $onboarding->bpjs_number) }}"
                               placeholder="13 Digit Nomor BPJS..."
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="bpjs_file" class="block text-xs font-bold text-slate-700 mb-1.5">Unggah Foto Kartu BPJS (PDF/JPG/PNG)</label>
                        <input type="file" name="bpjs_file" id="bpjs_file" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                        @if($onboarding->bpjs_file)
                            <div class="mt-1 text-[11px]">
                                <a href="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'bpjs', 'view' => 1]) }}" target="_blank" class="text-blue-600 font-semibold hover:underline">Lihat file BPJS tersimpan &rarr;</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 4. Rekening Bank --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-cyan-600 text-white font-black text-xs flex items-center justify-center">4</span>
                        <h3 class="text-sm font-black text-slate-900">Rekening Bank untuk Payroll Gaji</h3>
                    </div>
                    @if($onboarding->bank_verified)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi HR</span>
                    @elseif($onboarding->bank_account_number)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800">Sudah Diisi</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Wajib Diisi</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="bank_name" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Bank</label>
                        <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $onboarding->bank_name) }}"
                               placeholder="BCA / Mandiri / BNI / BRI..."
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="bank_account_number" class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Rekening</label>
                        <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', $onboarding->bank_account_number) }}"
                               placeholder="1234567890"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="bank_account_holder" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Pemilik Rekening</label>
                        <input type="text" name="bank_account_holder" id="bank_account_holder" value="{{ old('bank_account_holder', $onboarding->bank_account_holder ?? $candidate->full_name) }}"
                               placeholder="Sesuai Buku Tabungan..."
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                </div>

                <div>
                    <label for="bank_book_file" class="block text-xs font-bold text-slate-700 mb-1.5">Unggah Foto Halaman Depan Buku Tabungan / Bukti Rekening (Opsional)</label>
                    <input type="file" name="bank_book_file" id="bank_book_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                </div>
            </div>

            {{-- 5. Ijazah --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-cyan-600 text-white font-black text-xs flex items-center justify-center">5</span>
                        <h3 class="text-sm font-black text-slate-900">Ijazah &amp; Transkrip Nilai Terakhir</h3>
                    </div>
                    @if($onboarding->ijazah_verified)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi HR</span>
                    @elseif($onboarding->ijazah_file)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800">Sudah Diunggah</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Wajib Diunggah</span>
                    @endif
                </div>

                <div>
                    <label for="ijazah_file" class="block text-xs font-bold text-slate-700 mb-1.5">Unggah Berkas Scan Ijazah / Transkrip (PDF/JPG/PNG)</label>
                    <input type="file" name="ijazah_file" id="ijazah_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                    @if($onboarding->ijazah_file)
                        <div class="mt-1 text-[11px]">
                            <a href="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'ijazah', 'view' => 1]) }}" target="_blank" class="text-blue-600 font-semibold hover:underline">Lihat file Ijazah tersimpan &rarr;</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 6. Pas Foto --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-cyan-600 text-white font-black text-xs flex items-center justify-center">6</span>
                        <h3 class="text-sm font-black text-slate-900">Pas Foto Formal untuk ID Card</h3>
                    </div>
                    @if($onboarding->pas_foto_verified)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi HR</span>
                    @elseif($onboarding->pas_foto_file)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800">Sudah Diunggah</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Wajib Diunggah</span>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    @if($onboarding->pas_foto_file)
                        <img src="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'pas_foto', 'view' => 1]) }}" alt="Foto Onboarding" class="w-16 h-20 object-cover rounded-xl border border-slate-200 shadow-sm">
                    @endif
                    <div class="flex-1">
                        <label for="pas_foto_file" class="block text-xs font-bold text-slate-700 mb-1.5">Unggah Pas Foto Formal (Latar Merah / Biru, JPG/PNG)</label>
                        <input type="file" name="pas_foto_file" id="pas_foto_file" accept=".jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                    </div>
                </div>
            </div>

            {{-- 7. Kontrak Kerja --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-cyan-600 text-white font-black text-xs flex items-center justify-center">7</span>
                        <h3 class="text-sm font-black text-slate-900">Kontrak Kerja (Bertandatangan)</h3>
                    </div>
                    @if($onboarding->contract_signed)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi HR</span>
                    @elseif($onboarding->contract_file)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800">Sudah Diunggah</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Dokumen Kontrak</span>
                    @endif
                </div>

                <div>
                    <p class="text-xs text-slate-500 mb-2">
                        Unduh draf kontrak kerja atau offering letter resmi Anda, bubuhkan tanda tangan (digital atau basah), lalu unggah kembali berkas hasil tanda tangan.
                    </p>
                    <input type="file" name="contract_file" id="contract_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                    @if($onboarding->contract_file)
                        <div class="mt-1 text-[11px]">
                            <a href="{{ route('documents.onboarding', ['onboarding' => $onboarding->id, 'type' => 'contract', 'view' => 1]) }}" target="_blank" class="text-blue-600 font-semibold hover:underline">Lihat kontrak bertandatangan tersimpan &rarr;</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 8. Emergency Contact --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-xl bg-cyan-600 text-white font-black text-xs flex items-center justify-center">8</span>
                        <h3 class="text-sm font-black text-slate-900">Emergency Contact (Kontak Darurat)</h3>
                    </div>
                    @if($onboarding->emergency_verified)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">✓ Terverifikasi HR</span>
                    @elseif($onboarding->emergency_contact_name && $onboarding->emergency_contact_phone)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800">Sudah Diisi</span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">Wajib Diisi</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="emergency_contact_name" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Kontak Darurat</label>
                        <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $onboarding->emergency_contact_name) }}"
                               placeholder="Nama Orang Tua / Pasangan / Saudara..."
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="emergency_contact_relation" class="block text-xs font-bold text-slate-700 mb-1.5">Hubungan Keluarga</label>
                        <input type="text" name="emergency_contact_relation" id="emergency_contact_relation" value="{{ old('emergency_contact_relation', $onboarding->emergency_contact_relation) }}"
                               placeholder="Orang Tua / Pasangan / Kakak..."
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>

                    <div>
                        <label for="emergency_contact_phone" class="block text-xs font-bold text-slate-700 mb-1.5">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $onboarding->emergency_contact_phone) }}"
                               placeholder="081234567890"
                               class="w-full px-3.5 py-2.5 text-xs rounded-xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    </div>
                </div>
            </div>

            {{-- Submit Action Bar --}}
            <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h4 class="text-xs font-bold text-slate-800">Simpan Progres Dokumen Onboarding</h4>
                    <p class="text-[11px] text-slate-400">Anda dapat mencicil pengunggahan dokumen kapan saja sebelum tanggal masuk kerja.</p>
                </div>

                <button type="submit" class="px-6 py-3 rounded-2xl bg-cyan-600 hover:bg-cyan-500 text-white font-extrabold text-xs shadow-lg shadow-cyan-600/30 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan &amp; Perbarui Berkas Onboarding</span>
                </button>
            </div>
        </form>
    @endif

</div>
@endsection
