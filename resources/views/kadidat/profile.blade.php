@extends('layouts.dashboard')

@section('title', 'Lengkapi Profil & Upload CV')
@section('role-label', 'Pelamar / Job Applicant')
@section('page-title', 'Pengaturan Profil Kandidat & Berkas CV')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    <!-- Hero Header -->
    <div class="rounded-2xl bg-gradient-to-r from-blue-900 via-indigo-950 to-slate-900 p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-semibold mb-3 border border-blue-400/30">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Dokumen & Riwayat Karir</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Kelengkapan Profil & Berkas Lamaran</h2>
                <p class="text-blue-100/80 text-xs sm:text-sm mt-1 max-w-xl">
                    Lengkapi biodata diri, upload CV terbaru, dan tambahkan pengalaman kerja Anda agar tim rekruter dapat mengevaluasi lamaran Anda dengan maksimal.
                </p>
            </div>
            <a href="{{ route('kadidat.dashboard') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition self-start sm:self-center shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Main Biodata & CV Upload Form -->
    <form method="POST" action="{{ route('kadidat.profile.update') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- SECTION 1: Upload CV / Resume & Foto Profil -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="pb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs">1</span>
                    Dokumen Curriculum Vitae (CV) & Foto Profil
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Unggah dokumen CV Anda dalam format PDF, DOC, atau DOCX dan pasang foto profil formal.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- CV Upload Box -->
                <div class="p-5 rounded-2xl border-2 border-dashed border-blue-200 bg-blue-50/30 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-900">Berkas CV / Resume Utama *</label>
                            <span class="text-[10px] font-semibold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">PDF, DOC, DOCX (Maks 10MB)</span>
                        </div>

                        <!-- Current CV Status if uploaded -->
                        @if($profile->resume_path)
                            <div class="mt-3 p-3 rounded-xl bg-white border border-blue-200 flex items-center justify-between shadow-sm">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="p-2 rounded-lg bg-rose-50 text-rose-600 shrink-0">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="truncate">
                                        <div class="text-xs font-bold text-slate-800 truncate">{{ basename($profile->resume_path) }}</div>
                                        <div class="text-[10px] text-emerald-600 font-semibold flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            <span>Berkas aktif terunggah</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('documents.resume', ['candidate' => $candidate->id, 'view' => 1]) }}" target="_blank"
                                   class="px-2.5 py-1 text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-lg transition shrink-0 ml-2">
                                    Unduh
                                </a>
                            </div>
                        @else
                            <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span>Anda belum mengunggah berkas CV. Silakan pilih file dokumen CV Anda di bawah ini.</span>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1.5">Pilih / Ganti File Dokumen CV:</label>
                        <input type="file" name="resume" accept=".pdf,.doc,.docx"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                        @error('resume')
                            <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Profile Photo Upload Box -->
                <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-900">Foto Profil Resmi</label>
                            <span class="text-[10px] font-semibold text-slate-500 bg-slate-200 px-2 py-0.5 rounded-full">JPG, PNG, WEBP (Maks 2MB)</span>
                        </div>

                        <div class="mt-3 flex items-center gap-4">
                            @if($profile->photo)
                                <img src="{{ asset('storage/' . $profile->photo) }}" alt="Foto Profil" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-blue-500/30 shadow-sm shrink-0">
                            @else
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-700 flex items-center justify-center text-white font-black text-lg shadow-sm shrink-0">
                                    {{ strtoupper(substr($candidate->first_name, 0, 1) . substr($candidate->last_name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="text-xs text-slate-600">
                                <span class="font-bold text-slate-800">{{ $candidate->full_name }}</span>
                                <p class="text-[11px] text-slate-400 mt-0.5">Gunakan pas foto formal atau semi-formal dengan pencahayaan jelas.</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1.5">Unggah Foto Baru:</label>
                        <input type="file" name="photo" accept="image/*"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white hover:file:bg-slate-900 cursor-pointer">
                        @error('photo')
                            <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        <!-- SECTION 2: Biodata Diri & Kontak -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="pb-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs">2</span>
                    Biodata Lengkap & Informasi Kontak
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Pastikan nomor telepon WhatsApp dan email Anda aktif agar mudah dihubungi HR.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- First Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Depan *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $candidate->first_name) }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    @error('first_name') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Last Name -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Belakang</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $candidate->last_name) }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    @error('last_name') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email (Readonly) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email Terdaftar (Akun)</label>
                    <input type="email" value="{{ $candidate->email }}" disabled
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed">
                    <p class="text-[10px] text-slate-400 mt-1">Email akun login Anda</p>
                </div>

                <!-- Phone / WhatsApp -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp / HP *</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number', $candidate->phone_number) }}" required placeholder="Contoh: 081234567890"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    @error('phone_number') <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- NIK KTP -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold text-slate-700">Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span></label>
                        <span class="text-[10px] text-blue-600 font-medium">1 NIK untuk 1 akun</span>
                    </div>
                    <input type="text" name="nik" value="{{ old('nik', $profile->nik) }}" maxlength="16" required placeholder="16 digit NIK KTP"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition font-mono">
                    <p class="text-[10px] text-slate-400 mt-1">Sesuai KTP (16 digit angka). Wajib diisi dan hanya dapat digunakan untuk 1 akun.</p>
                    @error('nik') <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p> @enderror
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Kelamin</label>
                    <select name="gender" class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male" {{ old('gender', $profile->gender) === 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $profile->gender) === 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <!-- Date of Birth -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Lahir</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $profile->date_of_birth?->format('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <!-- Place of Birth -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tempat Lahir</label>
                    <input type="text" name="place_of_birth" value="{{ old('place_of_birth', $profile->place_of_birth) }}" placeholder="Kota kelahiran"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <!-- City -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kota Domisili</label>
                    <input type="text" name="city" value="{{ old('city', $profile->city) }}" placeholder="Contoh: Jakarta Selatan"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <!-- Province -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Provinsi</label>
                    <input type="text" name="province" value="{{ old('province', $profile->province) }}" placeholder="Contoh: DKI Jakarta"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <!-- Postal Code -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kode Pos</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code) }}" placeholder="Contoh: 12810"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <!-- LinkedIn URL -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">URL Profil LinkedIn</label>
                    <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $profile->linkedin_url) }}" placeholder="https://linkedin.com/in/username"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <!-- Portfolio URL -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">URL Portofolio / GitHub</label>
                    <input type="url" name="portfolio_url" value="{{ old('portfolio_url', $profile->portfolio_url) }}" placeholder="https://github.com/username atau website"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <!-- Full Address -->
                <div class="sm:col-span-2 lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Domisili Lengkap</label>
                    <input type="text" name="address" value="{{ old('address', $profile->address) }}" placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan"
                           class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>
            </div>

            <!-- Summary / Bio -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Ringkasan Diri / Bio Profesional</label>
                <textarea name="summary" rows="4" placeholder="Ceritakan latar belakang profesional Anda, keahlian utama, dan pencapaian kerja yang relevan..."
                          class="w-full px-3.5 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">{{ old('summary', $profile->summary) }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Ringkasan yang menarik dan jelas akan langsung dibaca oleh HR saat screening berkas.</p>
            </div>

            <!-- Submit Button for Section 1 & 2 -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/30 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Simpan Biodata & Berkas CV</span>
                </button>
            </div>
        </div>
    </form>

    <!-- SECTION 3: Riwayat Pendidikan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs">3</span>
                    Riwayat Pendidikan
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Tambahkan riwayat pendidikan formal terakhir Anda (S1, S2, Diploma, SMA/SMK).</p>
            </div>
            <button type="button" onclick="document.getElementById('form-add-education').classList.toggle('hidden')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs transition border border-blue-200 self-start sm:self-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>+ Tambah Pendidikan</span>
            </button>
        </div>

        <!-- Add Education Form (Collapsible) -->
        <div id="form-add-education" class="hidden p-5 rounded-2xl bg-blue-50/50 border border-blue-200">
            <h4 class="text-xs font-bold text-blue-900 mb-3">Formulir Tambah Riwayat Pendidikan Baru</h4>
            <form method="POST" action="{{ route('kadidat.profile.education.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @csrf
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Universitas / Sekolah *</label>
                    <input type="text" name="institution_name" required placeholder="Contoh: Universitas Indonesia"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Jenjang Pendidikan *</label>
                    <select name="degree" required class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="Sarjana (S1)">Sarjana (S1)</option>
                        <option value="Magister (S2)">Magister (S2)</option>
                        <option value="Doktor (S3)">Doktor (S3)</option>
                        <option value="Diploma (D3/D4)">Diploma (D3/D4)</option>
                        <option value="SMA / SMK">SMA / SMK</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Jurusan / Program Studi *</label>
                    <input type="text" name="field_of_study" required placeholder="Contoh: Teknik Informatika"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nilai IPK (GPA)</label>
                    <input type="number" step="0.01" min="0" max="4.00" name="gpa" placeholder="Contoh: 3.75"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Tanggal Lulus / Selesai</label>
                    <input type="date" name="end_date"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div class="sm:col-span-2 lg:col-span-3 flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('form-add-education').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow transition">
                        Simpan Pendidikan
                    </button>
                </div>
            </form>
        </div>

        <!-- List of Education Cards -->
        <div class="space-y-3">
            @forelse($candidate->education as $edu)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">{{ $edu->institution_name }}</h4>
                        <div class="text-xs text-slate-600 mt-0.5">{{ $edu->degree }} &bull; {{ $edu->field_of_study }}</div>
                        <div class="text-[11px] text-slate-400 mt-1">
                            Lulus: {{ $edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('Y') : 'Sedang Menempuh' }}
                            @if($edu->gpa) &bull; <span class="text-emerald-700 font-bold">IPK {{ $edu->gpa }}</span> @endif
                        </div>
                    </div>
                    <form method="POST" action="{{ route('kadidat.profile.education.destroy', $edu->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pendidikan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-rose-600 hover:bg-rose-50 border border-rose-200 text-xs font-bold transition">
                            Hapus
                        </button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-slate-400 italic py-2">Belum ada data pendidikan yang ditambahkan. Klik tombol "+ Tambah Pendidikan" di atas.</p>
            @endforelse
        </div>
    </div>

    <!-- SECTION 4: Riwayat Pengalaman Kerja -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs">4</span>
                    Pengalaman Kerja
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Cantumkan pekerjaan terakhir, magang, atau posisi penting yang pernah Anda jalani.</p>
            </div>
            <button type="button" onclick="document.getElementById('form-add-experience').classList.toggle('hidden')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs transition border border-blue-200 self-start sm:self-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>+ Tambah Pengalaman</span>
            </button>
        </div>

        <!-- Add Experience Form (Collapsible) -->
        <div id="form-add-experience" class="hidden p-5 rounded-2xl bg-blue-50/50 border border-blue-200">
            <h4 class="text-xs font-bold text-blue-900 mb-3">Formulir Tambah Pengalaman Kerja Baru</h4>
            <form method="POST" action="{{ route('kadidat.profile.experience.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Perusahaan / Organisasi *</label>
                    <input type="text" name="company_name" required placeholder="Contoh: PT Teknologi Nusantara"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Posisi / Jabatan Pekerjaan *</label>
                    <input type="text" name="position" required placeholder="Contoh: Full-Stack Developer"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Tanggal Mulai Bekerja</label>
                    <input type="date" name="start_date"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date"
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <label class="inline-flex items-center gap-2 text-[11px] text-slate-600 mt-1 cursor-pointer">
                        <input type="checkbox" name="is_current" value="1" class="rounded text-blue-600">
                        <span>Masih bekerja di posisi ini saat ini</span>
                    </label>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Deskripsi Tugas & Pencapaian</label>
                    <textarea name="description" rows="3" placeholder="Jelaskan peran Anda, teknologi yang digunakan, serta kontribusi penting..."
                              class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
                </div>

                <div class="sm:col-span-2 flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('form-add-experience').classList.add('hidden')"
                            class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow transition">
                        Simpan Pengalaman
                    </button>
                </div>
            </form>
        </div>

        <!-- List of Experience Cards -->
        <div class="space-y-3">
            @forelse($candidate->experiences as $exp)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-bold text-slate-900">{{ $exp->position }}</h4>
                            <span class="text-[10px] text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">
                                {{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('M Y') : '' }} -
                                {{ $exp->is_current ? 'Sekarang' : ($exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : '') }}
                            </span>
                        </div>
                        <div class="text-xs text-blue-700 font-semibold mt-0.5">{{ $exp->company_name }}</div>
                        @if($exp->description)
                            <p class="text-xs text-slate-600 mt-2 leading-relaxed">{{ $exp->description }}</p>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('kadidat.profile.experience.destroy', $exp->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengalaman kerja ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-rose-600 hover:bg-rose-50 border border-rose-200 text-xs font-bold transition shrink-0">
                            Hapus
                        </button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-slate-400 italic py-2">Belum ada riwayat pengalaman kerja yang ditambahkan. Klik tombol "+ Tambah Pengalaman" di atas.</p>
            @endforelse
        </div>
    </div>

    <!-- SECTION 5: Keahlian & Kemampuan (Skills) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6" id="skills">
        <div class="pb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <span class="p-1.5 rounded-lg bg-blue-50 text-blue-600 font-bold text-xs">5</span>
                Keahlian & Kemampuan Teknis (Skills)
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Tambahkan keahlian teknis atau soft-skills yang relevan dengan posisi kerja impian Anda.</p>
        </div>

        <!-- Add Skill Form -->
        <form method="POST" action="{{ route('kadidat.profile.skill.store') }}" class="p-4 rounded-xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            @csrf
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Keahlian / Tools *</label>
                <input type="text" name="skill_name" required placeholder="Contoh: Laravel, React, Figma, SQL, UI/UX..."
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Tingkat Kemampuan</label>
                <select name="proficiency_level" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="beginner">Pemula (Beginner)</option>
                    <option value="intermediate" selected>Menengah (Intermediate)</option>
                    <option value="advanced">Mahir (Advanced)</option>
                    <option value="expert">Ahli (Expert)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Tahun Pengalaman</label>
                    <input type="number" name="years_of_experience" min="0" max="50" value="1" required
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow transition mt-auto">
                    + Tambah
                </button>
            </div>
        </form>

        <!-- Skill Badges List -->
        <div class="flex flex-wrap gap-2 pt-2">
            @forelse($candidate->skills as $skill)
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 text-xs border border-slate-200 font-medium">
                    <span>{{ $skill->skill_name }}</span>
                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-blue-100 text-blue-800">
                        {{ $skill->proficiency_level }} ({{ $skill->years_of_experience }} thn)
                    </span>
                    <form method="POST" action="{{ route('kadidat.profile.skill.destroy', $skill->id) }}" class="inline" onsubmit="return confirm('Hapus keahlian ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-slate-400 hover:text-rose-600 text-sm font-bold ml-1" title="Hapus keahlian">&times;</button>
                    </form>
                </div>
            @empty
                <p class="text-xs text-slate-400 italic">Belum ada keahlian yang ditambahkan.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
