# Sistem Rekrutmen Karyawan & Applicant Tracking System (ATS)

Aplikasi web modern berbasis Laravel untuk digitalisasi dan otomatisasi seluruh siklus rekrutmen perusahaan: mulai dari publikasi lowongan kerja, pendaftaran kandidat, CV digital, tes seleksi online (CBT), evaluasi wawancara, penawaran kerja (Offering Letter), hingga pemberkasan onboarding karyawan baru.

---

## Daftar Isi

1. [Tentang Sistem](#tentang-sistem)
2. [Fitur Utama](#fitur-utama)
3. [Alur Siklus Rekrutmen](#alur-siklus-rekrutmen)
4. [Hak Akses Pengguna (RBAC)](#hak-akses-pengguna-rbac)
5. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
6. [Kebutuhan Sistem](#kebutuhan-sistem)
7. [Panduan Instalasi](#panduan-instalasi)
8. [Akun Demo Default](#akun-demo-default)
9. [Struktur Direktori Utama](#struktur-direktori-utama)
10. [Keamanan & Proteksi Data](#keamanan--proteksi-data)
11. [Lisensi](#lisensi)

---

## Tentang Sistem

**Sistem Rekrutmen & ATS** ini dibangun untuk mempermudah departemen Human Resources (HR) dan tim pewawancara dalam menyaring talenta terbaik secara transparan, terstruktur, dan efisien. Pelamar dapat mendaftar, melengkapi profil CV digital, memantau perkembangan lamaran secara mandiri (termasuk pengecekan cepat via NIK tanpa login), serta mengikuti tes seleksi online langsung dari sistem.

---

## Fitur Utama

### 1. Portal Publik & Karir

- **Katalog Lowongan Pekerjaan**: Menampilkan daftar lowongan aktif lengkap dengan rincian divisi, lokasi penempatan, rentang gaji, kualifikasi, dan batas akhir pendaftaran.
- **Cek Status Cepat via NIK**: Fitur praktis bagi pelamar untuk melacak status seleksi hanya dengan memasukkan Nomor Induk Kependudukan (NIK) tanpa perlu masuk ke akun. Dilengkapi pembatasan frekuensi akses (rate limiting).
- **Enkripsi ID Lowongan**: URL publik lowongan menggunakan slug terenkripsi untuk mencegah manipulasi ID (anti-IDOR).

### 2. Portal Kandidat / Pelamar

- **CV & Portofolio Digital**:
    - Manajemen riwayat pendidikan formal dan non-formal.
    - Riwayat pengalaman kerja profesional dan pencapaian.
    - Daftar keterampilan (skills) beserta tingkat kemahiran.
    - Unggah berkas resume / CV (PDF).
- **Proses Lamaran Cepat**: Melamar posisi pekerjaan hanya dengan 1 klik menggunakan profil data yang sudah terisi.
- **Computer-Based Test (CBT) Online**:
    - Pengerjaan soal tes asesmen daring dengan batasan durasi otomatis.
    - Navigasi nomor soal fleksibel dan konfirmasi pengiriman jawaban.
    - Perhitungan skor otomatis setelah tes selesai dikerjakan.
- **Manajemen Offering Letter**:
    - Notifikasi dan tinjauan surat penawaran kerja resmi.
    - Respon langsung (Terima / Tolak penawaran) dengan alasan opsional.
    - Cetak dokumen surat penawaran kerja ke format PDF / kertas.
- **Pemberkasan Onboarding Digital**:
    - Unggah dokumen pra-kerja: KTP, Ijazah, Transkrip Nilai, NPWP, BPJS, Kartu Keluarga, dan Buku Tabungan.
    - Pemantauan status verifikasi berkas oleh tim HR.

### 3. Portal Human Resources (HR)

- **Dashboard Analisis**: Metrik total pelamar, kandidat lolos per tahapan, wawancara mendatang, dan status onboarding.
- **Manajemen Lowongan**: Membuat, memperbarui status kuota, dan menutup lowongan pekerjaan.
- **Screening & Pipeline Pelamar**:
    - Pengelompokan status pelamar: Administrasi, Tes Online, Wawancara, Offering, Diterima, atau Ditolak.
    - **Pembaruan Status Massal via Excel**: Unduh template Excel, ubah status banyak pelamar sekaligus, dan unggah kembali untuk pembaruan instan.
    - **Ekspor Data ke Excel**: Mengunduh seluruh rekapitulasi data kandidat dan skor seleksi.
- **Pengaturan Tes Asesmen**:
    - Pembuatan modul tes dan bank soal (pilihan ganda).
    - Penentuan bobot penilaian, durasi ujian, dan nilai ambang batas (passing grade).
- **Penjadwalan Wawancara**: Penjadwalan sesi wawancara online/offline, penunjukan user interviewer, serta tautan video conference.
- **Penerbitan Surat Offering**: Pembuatan draf penawaran kompensasi, tunjangan, dan tanggal mulai bergabung.
- **Verifikasi Dokumen Onboarding**: Pemeriksaan keabsahan dokumen berkas yang dikirimkan kandidat baru.

### 4. Portal Interviewer / Tim Penilai

- **Jadwal Wawancara Khusus**: Melihat daftar pelamar yang ditugaskan untuk dinilai oleh pewawancara terkait.
- **Lembar Penilaian Terstruktur**: Pengisian formulir evaluasi kompetensi teknis, komunikasi, kepemimpinan, dan rekomendasi kelulusan.

### 5. Portal Super Administrator

- **Manajemen Pengguna & Otoritas**: Kontrol akun seluruh staf HR, interviewer, dan pelamar.
- **Fitur Blacklist**: Menonaktifkan dan memblokir akses pengguna tertentu yang melanggar ketentuan.
- **Pengaturan Identitas Perusahaan**: Konfigurasi nama instansi, logo, deskripsi, dan struktur departemen.
- **Backup & Restore Basis Data**: Pembuatan berkas cadangan database SQL dan pemulihan langsung dari antarmuka web.
- **Pembersihan Cache Sistem**: Optimalisasi performa aplikasi dengan sekali klik.

---

## Alur Siklus Rekrutmen

```
[ Publikasi Lowongan ]
         │
         ▼
[ Pendaftaran & Melamar ] ──► (Profil Kandidat / CV Digital)
         │
         ▼
[ Screening Administrasi ] ──► [ Tidak Lolos ]
         │ (Lolos)
         ▼
[ Tes Online / CBT ] ────────► [ Tidak Lolos ]
         │ (Lolos)
         ▼
[ Sesi Wawancara ] ──────────► [ Tidak Lolos ]
         │ (Direkomendasikan)
         ▼
[ Offering Letter ] ─────────► [ Ditolak Kandidat ]
         │ (Diterima Kandidat)
         ▼
[ Pemberkasan Onboarding ]
         │
         ▼
[ Karyawan Resmi Aktif ]
```

---

## Hak Akses Pengguna (RBAC)

Sistem menggunakan kontrol akses berbasis peran (Role-Based Access Control) melalui pustaka `spatie/laravel-permission`:

| Role            | Hak Akses Utama                                                                                                                |
| --------------- | ------------------------------------------------------------------------------------------------------------------------------ |
| **admin**       | Akses penuh sistem, manajemen akun pengguna, blacklist, pengaturan instansi, backup database, dan audit log.                   |
| **hr**          | Pengelolaan lowongan, screening pelamar, upload massal Excel, bank soal CBT, jadwal wawancara, surat offering, dan onboarding. |
| **interviewer** | Melihat jadwal wawancara yang ditugaskan, meninjau profil kandidat, dan mengisi lembar evaluasi penilaian.                     |
| **kandidat**    | Mencari lowongan, melamar, mengisi CV digital, mengikuti tes CBT, merespon offering letter, dan mengunggah dokumen onboarding. |

---

## Teknologi yang Digunakan

- **Kerangka Kerja**: Laravel (PHP 8.3+)
- **Basis Data**: MySQL / MariaDB
- **Manajemen Hak Akses**: Spatie Laravel-Permission
- **Antarmuka (Frontend)**: Tailwind CSS, Alpine.js, Blade Templating
- **Bundler Aset**: Vite
- **Pengolah Spreadsheet**: Maatwebsite Excel (Ekspor & Impor XLSX)
- **Keamanan**: Laravel Rate Limiting Throttle, Protected Document Streaming, CSRF Protection

---

## Kebutuhan Sistem

- PHP versi 8.3 atau yang lebih tinggi
- Ekstensi PHP: `pdo_mysql`, `mbstring`, `openssl`, `curl`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd` / `zip`
- Composer versi 2.x
- Node.js versi 18.x atau lebih baru dan NPM
- Database MySQL versi 8.0+ atau MariaDB versi 10.4+
- Web server Apache / Nginx (atau Laravel Artisan Serve)

---

## Panduan Instalasi

Ikuti langkah-langkah di bawah untuk menyiapkan dan menjalankan proyek di lingkungan lokal:

### 1. Masuk ke Direktori Proyek

```bash
cd c:/laragon/www/sistem-recruitment
```

### 2. Pasang Dependensi Backend (Composer)

```bash
composer install
```

### 3. Konfigurasi Lingkungan (`.env`)

Salin file konfigurasi contoh dan buat kunci enkripsi aplikasi:

```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan konfigurasi koneksi database di file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_recruitment
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Jalankan Migrasi & Data Awal (Seeding)

Perintah ini akan membuat seluruh tabel, struktur perizinan (roles & permissions), akun uji coba, serta data contoh lowongan:

```bash
php artisan migrate:fresh --seed
```

### 5. Buat Symbolic Link Direktori Storage

Langkah ini penting agar berkas logo, resume, dan dokumen pendukung dapat diakses sistem:

```bash
php artisan storage:link
```

### 6. Pasang Dependensi Frontend & Kompilasi Aset

```bash
npm install
npm run build
```

_(Untuk mode pengembangan aktif dengan auto-reload, jalankan `npm run dev`)_

### 7. Jalankan Server Lokal

```bash
php artisan serve
```

Akses aplikasi melalui browser di tautan: `http://localhost:8000` atau melalui URL Virtual Host Laragon Anda (misal `http://sistem-recruitment.test`).

---

## Akun Demo Default

Seluruh akun di bawah ini telah disiapkan secara otomatis oleh `DatabaseSeeder`:

| Role                   | Nama Akun | Email Login                   | Password Default |
| ---------------------- | --------- | ----------------------------- | ---------------- |
| **Super Admin**        | Nopal     | `Nopal@gmail.com`             | `password`       |
| **HR Officer**         | HR        | `hr@recruitment.com`          | `password`       |
| **Interviewer**        | Interview | `interviewer@recruitment.com` | `password`       |
| **Kandidat / Pelamar** | John Doe  | `kandidat@recruitment.com`    | `password`       |

---

## Struktur Direktori Utama

```
sistem-recruitment/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Kontroler per modul (Admin, HR, Interview, Kandidat, Auth)
│   │   └── Middleware/        # Middleware otentikasi, perizinan role, dan keamanan
│   └── Models/                # Model Eloquent (Candidate, Position, Assessment, Offering, Onboarding, dll.)
├── database/
│   ├── migrations/            # Skema tabel database rekrutmen
│   └── seeders/               # Seeder akun awal, role permissions, dan contoh lowongan
├── resources/
│   ├── css/                   # Konfigurasi Tailwind CSS
│   ├── js/                    # Skrip JavaScript / Alpine.js
│   └── views/                 # Template Blade (Admin, HR, Interviewer, Kandidat, Publik)
├── routes/
│   ├── web.php                # Rute web terproteksi per role
│   └── console.php            # Perintah konsol / jadwal otomatis
└── storage/
    └── app/                   # Penyimpanan privat berkas CV, resume, dan dokumen onboarding
```

---

## Keamanan & Proteksi Data

1. **Perlindungan Akses Dokumen Pribadi**: File resume dan dokumen identitas pelamar (KTP, NPWP, KK) tidak disimpan di folder public terbuka, melainkan dilindungi melalui route terotentikasi (`DocumentController`) dengan pembatasan hak akses ketat.
2. **Mitigasi Brute-Force**: Endpoint sensitif (Login, Pendaftaran, Cek NIK Cepat, Permohonan Reset Password) dilindungi oleh pembatas laju request (`throttle:5,1` dan `throttle:20,1`).
3. **Penyaringan Otoritas Controller**: Setiap aksi krusial diverifikasi ulang di tingkat controller untuk mencegah eskalasi hak akses (Privilege Escalation) antar peran.

---

## Lisensi

Aplikasi ini dikembangkan menggunakan kerangka kerja [Laravel](https://laravel.com) dan dilisensikan di bawah lisensi [MIT License](https://opensource.org/licenses/MIT).
