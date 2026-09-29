<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Penawaran Kerja - {{ $offering->candidate?->full_name ?? 'Kandidat' }} - {{ $offering->position?->title }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <style>
        /* Base Reset & Setup */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #1e293b;
            background-color: #f1f5f9;
            font-size: 11pt;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* Screen Wrapper */
        .screen-toolbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #0f172a;
            color: #ffffff;
            padding: 12px 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .screen-toolbar .actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: background-color 0.2s, transform 0.1s;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
        }
        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-secondary {
            background-color: #334155;
            color: #f8fafc;
        }
        .btn-secondary:hover {
            background-color: #475569;
        }

        .paper-container {
            max-width: 820px;
            margin: 28px auto 48px auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            padding: 48px 56px;
            border-radius: 4px;
        }

        /* Letterhead / Kop Surat */
        .letterhead {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 16px;
            border-bottom: 3px double #0f172a;
            margin-bottom: 24px;
        }

        .company-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .company-logo-box {
            width: 56px;
            height: 56px;
            background: #0f172a;
            color: #ffffff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 900;
            letter-spacing: -1px;
            flex-shrink: 0;
        }

        .company-info h1 {
            font-size: 18pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        .company-info p {
            font-size: 9pt;
            color: #64748b;
            margin-top: 2px;
            line-height: 1.4;
        }

        .doc-meta {
            text-align: right;
            font-size: 9.5pt;
            color: #475569;
        }

        .doc-meta .confidential-badge {
            display: inline-block;
            background: #fee2e2;
            color: #991b1b;
            font-size: 8pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 2px 8px;
            border-radius: 4px;
            border: 1px solid #fecaca;
            margin-bottom: 4px;
        }

        .doc-meta .doc-number {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 700;
            color: #0f172a;
            font-size: 10pt;
        }

        /* Document Subject */
        .doc-subject {
            margin-bottom: 24px;
            padding: 12px 16px;
            background: #f8fafc;
            border-left: 4px solid #0f172a;
            border-radius: 0 6px 6px 0;
        }

        .doc-subject h2 {
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-subject p {
            font-size: 9pt;
            color: #64748b;
            margin-top: 2px;
        }

        /* Recipient Box */
        .recipient-box {
            margin-bottom: 20px;
            font-size: 10.5pt;
            line-height: 1.5;
        }

        .recipient-box .recipient-name {
            font-size: 12pt;
            font-weight: 800;
            color: #0f172a;
        }

        /* Content Paragraphs */
        .letter-body {
            font-size: 10.5pt;
            color: #334155;
            text-align: justify;
            margin-bottom: 20px;
        }

        .letter-body p {
            margin-bottom: 12px;
        }

        /* Specification Table */
        .specs-table {
            width: 100%;
            border-collapse: collapse;
            margin: 18px 0 24px 0;
            font-size: 10pt;
        }

        .specs-table th,
        .specs-table td {
            border: 1px solid #cbd5e1;
            padding: 9px 14px;
            vertical-align: top;
        }

        .specs-table td.label-col {
            width: 32%;
            background-color: #f8fafc;
            font-weight: 700;
            color: #334155;
        }

        .specs-table td.val-col {
            width: 68%;
            color: #0f172a;
        }

        .specs-table td.val-highlight {
            font-weight: 800;
            color: #047857;
            font-size: 11pt;
        }

        /* Additional Sections */
        .section-box {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 10.5pt;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        .section-content {
            font-size: 9.5pt;
            color: #334155;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
            line-height: 1.6;
            white-space: pre-line;
        }

        /* Signatures Grid */
        .signatures-container {
            margin-top: 36px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            page-break-inside: avoid;
        }

        .sig-col {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 150px;
        }

        .sig-col.right {
            text-align: right;
        }

        .sig-role {
            font-size: 9.5pt;
            color: #64748b;
            font-weight: 600;
        }

        .sig-name {
            font-size: 11pt;
            font-weight: 800;
            color: #0f172a;
            text-decoration: underline;
        }

        .sig-title {
            font-size: 9pt;
            color: #475569;
            margin-top: 2px;
        }

        .stamp-mark {
            display: inline-block;
            margin-top: 4px;
            border: 2px solid #047857;
            color: #047857;
            padding: 4px 10px;
            font-size: 8pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
            transform: rotate(-3deg);
        }

        /* Footer watermark/note */
        .doc-footer {
            margin-top: 32px;
            padding-top: 12px;
            border-top: 1px solid #e2e8f0;
            font-size: 8pt;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* ========================================================================= */
        /* PRINT SPECIFIC STYLES (A4 Clean Output)                                   */
        /* ========================================================================= */
        @media print {
            @page {
                size: A4 portrait;
                margin: 14mm 16mm;
            }

            html, body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 10pt !important;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .screen-toolbar {
                display: none !important;
            }

            .paper-container {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .specs-table th,
            .specs-table td {
                border-color: #64748b !important;
            }

            .specs-table td.label-col {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .section-content {
                background: #f8fafc !important;
                border-color: #cbd5e1 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none !important;
            }

            .page-break-inside-avoid {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    @php
        $companyName = \App\Models\SystemSetting::get('company_name', 'PT Rekrutmen Indonesia Bersatu');
        $appName = \App\Models\SystemSetting::get('app_name', 'RecruitPro');
        $companyAddress = \App\Models\SystemSetting::get('company_address', 'Cyber 2 Tower Lt. 18, Jl. HR Rasuna Said Blok X-5 No. 13, Jakarta Selatan');
        $companyCity = \App\Models\SystemSetting::get('company_city', 'Jakarta');
        $companyPhone = \App\Models\SystemSetting::get('company_phone', '+62 21 555-0199');
        $companyEmail = \App\Models\SystemSetting::get('company_email', 'talent@recruitment.test');
        $companyWebsite = \App\Models\SystemSetting::get('company_website', 'www.recruitment.test');

        $docNumber = 'OFF/' . $offering->created_at->format('Y') . '/' . str_pad($offering->id, 5, '0', STR_PAD_LEFT);
        $candidateApp = $offering->application;
        $appIdStr = $candidateApp ? 'APP-' . str_pad((string)$candidateApp->id, 5, '0', STR_PAD_LEFT) : 'REG-2026';
    @endphp

    <!-- Screen Toolbar (Only shown on screen) -->
    <div class="screen-toolbar no-print">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="font-weight: 800; font-size: 14px; color: #38bdf8;">Dokumen Cetak / PDF Resmi</span>
            <span style="color: #64748b;">|</span>
            <span style="font-size: 12px; color: #cbd5e1;">Surat Penawaran: {{ $docNumber }}</span>
        </div>

        <div class="actions">
            <button onclick="window.print()" class="btn btn-primary" title="Cetak surat atau simpan sebagai file PDF">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak / Simpan PDF (Ctrl+P)</span>
            </button>

            <button onclick="window.close(); if(history.length > 1){ history.back(); }" class="btn btn-secondary">
                <span>&larr; Tutup / Kembali</span>
            </button>
        </div>
    </div>

    <!-- Official A4 Paper Container -->
    <div class="paper-container">

        <!-- Kop Surat / Letterhead -->
        <div class="letterhead">
            <div class="company-brand">
                <div class="company-logo-box">
                    {{ strtoupper(substr($companyName, 0, 1)) }}
                </div>
                <div class="company-info">
                    <h1>{{ $companyName }}</h1>
                    <p><strong>Divisi Human Capital &amp; Talent Acquisition</strong></p>
                    <p>{{ $companyAddress }}</p>
                    <p>Telepon: {{ $companyPhone }} &bull; Email: {{ $companyEmail }} &bull; Website: {{ $companyWebsite }}</p>
                </div>
            </div>

            <div class="doc-meta">
                <div class="confidential-badge">CONFIDENTIAL</div>
                <div class="doc-number">No: {{ $docNumber }}</div>
                <div style="margin-top: 4px; font-weight: 600;">{{ $companyCity }}, {{ $offering->created_at->translatedFormat('d F Y') }}</div>
            </div>
        </div>

        <!-- Perihal & Sifat Surat -->
        <div class="doc-subject">
            <h2>SURAT PENAWARAN KERJA RESMI (JOB OFFER LETTER)</h2>
            <p>Sifat: Sangat Rahasia (Confidential) &bull; Lampiran: Rincian Kompensasi &amp; Benefit Kerja</p>
        </div>

        <!-- Penerima Surat -->
        <div class="recipient-box">
            <p style="color: #64748b; font-size: 9.5pt;">Kepada Yang Terhormat:</p>
            <div class="recipient-name">{{ $candidate?->full_name }}</div>
            <p>ID Pelamar: <strong>{{ $appIdStr }}</strong> &bull; Email: {{ $candidate?->email }} &bull; Kontak: {{ $candidate?->phone_number ?? '-' }}</p>
            <p style="margin-top: 2px;">Di Tempat</p>
        </div>

        <!-- Paragraf Pembuka -->
        <div class="letter-body">
            <p>Dengan hormat,</p>
            <p>
                Sehubungan dengan rangkaian proses seleksi penerimaan karyawan yang telah Anda ikuti dengan hasil yang sangat memuaskan—meliputi seleksi berkas/CV, asesmen kompetensi online, serta wawancara kerja—dengan ini Manajemen <strong>{{ $companyName }}</strong> secara resmi menyampaikan <strong>Penawaran Kesempatan Berkarir (Job Offer)</strong> kepada Anda untuk bergabung bersama perusahaan kami dengan ketentuan sebagai berikut:
            </p>
        </div>

        <!-- Tabel Rincian Posisi & Kompensasi -->
        <table class="specs-table">
            <tbody>
                <tr>
                    <td class="label-col">Posisi / Jabatan</td>
                    <td class="val-col" style="font-weight: 800; font-size: 11pt;">{{ $offering->position?->title }}</td>
                </tr>
                <tr>
                    <td class="label-col">Departemen / Divisi</td>
                    <td class="val-col">{{ $offering->department ?: ($offering->position?->department ?? 'General') }}</td>
                </tr>
                <tr>
                    <td class="label-col">Status Ikatan Kerja</td>
                    <td class="val-col" style="font-weight: 600;">{{ $offering->employment_type ?: 'Full-time' }}</td>
                </tr>
                <tr>
                    <td class="label-col">Gaji Pokok yang Ditawarkan</td>
                    <td class="val-col val-highlight">
                        Rp {{ number_format($offering->effective_salary, 0, ',', '.') }}
                        <span style="font-size: 9pt; font-weight: 500; color: #64748b;">/ Bulan (Gross)</span>
                    </td>
                </tr>
                <tr>
                    <td class="label-col">Tanggal Efektif Bekerja (Join Date)</td>
                    <td class="val-col" style="font-weight: 700;">
                        {{ $offering->effective_join_date ? \Carbon\Carbon::parse($offering->effective_join_date)->translatedFormat('d F Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="label-col">Lokasi Penempatan Kerja</td>
                    <td class="val-col">{{ $offering->position?->location ?? 'Kantor Pusat' }} ({{ $offering->position?->type ?? 'On-site' }})</td>
                </tr>
                <tr>
                    <td class="label-col">Masa Percobaan (Probation)</td>
                    <td class="val-col">3 (Tiga) Bulan terhitung sejak tanggal efektif mulai bekerja</td>
                </tr>
            </tbody>
        </table>

        <!-- Tunjangan & Fasilitas -->
        @if($offering->benefits)
            <div class="section-box">
                <div class="section-title">Tunjangan, Fasilitas &amp; Benefit Kerja:</div>
                <div class="section-content">{{ $offering->benefits }}</div>
            </div>
        @else
            <div class="section-box">
                <div class="section-title">Tunjangan, Fasilitas &amp; Benefit Standar:</div>
                <div class="section-content">1. BPJS Kesehatan &amp; BPJS Ketenagakerjaan (JKK, JKM, JHT, JP).
2. Tunjangan Hari Raya (THR) sesuai peraturan ketenagakerjaan Republik Indonesia.
3. Hak Cuti Tahunan sebanyak 12 (dua belas) hari kerja setelah masa kerja 1 tahun.
4. Perangkat kerja (laptop) &amp; akses sistem enterprise perusahaan.</div>
            </div>
        @endif

        <!-- Ketentuan Tambahan & Onboarding -->
        <div class="section-box">
            <div class="section-title">Ketentuan Penting &amp; Persyaratan Onboarding:</div>
            <div class="section-content">1. Kerahasiaan (Confidentiality): Seluruh rincian penawaran gaji dan paket kompensasi ini bersifat sangat rahasia.
2. Persyaratan Berkas Onboarding: Kelulusan penawaran ini mensyaratkan kelengkapan unggah dokumen resmi (KTP, NPWP, BPJS, Rekening Bank, Ijazah Asli/Legalisir, Pas Foto Formal, dan Penandatanganan Kontrak Kerja Resmi).
3. Konfirmasi Penerimaan: Mohon memberikan tanda persetujuan penerimaan melalui portal pelamar resmi sebelum tanggal mulai bekerja.{{ $offering->notes ? "\n\nCatatan Tambahan: " . $offering->notes : '' }}</div>
        </div>

        <!-- Paragraf Penutup -->
        <div class="letter-body">
            <p>
                Demikian Surat Penawaran Kerja ini kami sampaikan. Kami sangat meyakini bahwa kemampuan, pengalaman, dan integritas yang Anda miliki akan memberikan kontribusi signifikan bagi kemajuan bersama. Selamat bergabung dalam tim kami.
            </p>
        </div>

        <!-- Kolom Tanda Tangan Resmi -->
        <div class="signatures-container page-break-inside-avoid">
            <!-- Pihak Perusahaan -->
            <div class="sig-col">
                <div>
                    <div class="sig-role">Diterbitkan Oleh Manajemen,</div>
                    <div style="font-weight: 800; color: #0f172a; font-size: 10.5pt; margin-top: 2px;">{{ $companyName }}</div>
                </div>

                <div style="margin-top: 48px;">
                    <div class="stamp-mark">TERVERIFIKASI RESMI</div>
                    <div class="sig-name" style="margin-top: 8px;">Direktur Human Capital Management</div>
                    <div class="sig-title">Head of People &amp; Talent Acquisition</div>
                </div>
            </div>

            <!-- Pihak Kandidat -->
            <div class="sig-col right">
                <div>
                    <div class="sig-role">Pernyataan Penerimaan Kandidat,</div>
                    <div style="font-size: 9pt; color: #64748b; margin-top: 2px;">
                        "Saya menyetujui seluruh ketentuan penawaran di atas"
                    </div>
                </div>

                <div style="margin-top: 48px;">
                    @if($offering->status === 'accepted')
                        <div class="stamp-mark" style="border-color: #2563eb; color: #2563eb;">
                            DISETUJUI DIGITAL: {{ $offering->responded_at ? $offering->responded_at->format('d/m/Y H:i') : now()->format('d/m/Y') }}
                        </div>
                    @elseif($offering->status === 'rejected')
                        <div class="stamp-mark" style="border-color: #dc2626; color: #dc2626;">
                            DITOLAK OLEH KANDIDAT
                        </div>
                    @else
                        <div style="height: 24px;"></div>
                    @endif
                    <div class="sig-name" style="margin-top: 8px;">{{ $candidate?->full_name }}</div>
                    <div class="sig-title">Tanda Tangan &amp; Nama Jelas Pelamar</div>
                </div>
            </div>
        </div>

        <!-- Dokumen Footer Watermark -->
        <div class="doc-footer">
            <span>Dokumen Resmi Diterbitkan oleh Sistem {{ $appName }} &bull; {{ $companyName }}</span>
            <span>Halaman 1 dari 1 &bull; Dokumen Sah Tanpa Tanda Tangan Basah jika Diverifikasi Digital</span>
        </div>

    </div>

    <!-- Auto print if requested -->
    @if(request()->boolean('autoprint'))
        <script>
            window.addEventListener('load', function() {
                setTimeout(function() {
                    window.print();
                }, 400);
            });
        </script>
    @endif

</body>
</html>
