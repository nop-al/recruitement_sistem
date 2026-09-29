<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Kandidat — SisRec Rekrutmen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-shadow: rgba(37, 99, 235, 0.30);
        }
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background: #f1f5f9;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== LAYOUT ===== */
        .auth-wrap {
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        /* ===== LEFT COVER (desktop only) ===== */
        .auth-cover {
            display: none;
            position: relative;
            flex: 1 1 0;
            overflow: hidden;
        }
        .auth-cover img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        .auth-cover-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(
                155deg,
                rgba(17, 24, 39, 0.72) 0%,
                rgba(30, 58, 138, 0.65) 55%,
                rgba(17, 24, 39, 0.80) 100%
            );
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            padding: 48px;
        }
        .auth-cover-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(8px);
            border-radius: 100px;
            padding: 6px 14px 6px 8px;
            width: fit-content;
            margin-bottom: 20px;
        }
        .auth-cover-badge-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #34d399;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.45; }
        }
        .auth-cover-badge span { font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.85); }
        .auth-cover h2 { font-size: 32px; font-weight: 800; color: #fff; margin: 0 0 12px; line-height: 1.25; letter-spacing: -0.5px; }
        .auth-cover p { font-size: 15px; color: rgba(255,255,255,0.65); margin: 0 0 28px; line-height: 1.6; max-width: 380px; }

        /* ===== Feature list (left panel) ===== */
        .feature-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; }
        .feature-list li { display: flex; align-items: center; gap: 10px; font-size: 14px; color: rgba(255,255,255,0.75); font-weight: 500; }
        .feature-check {
            width: 22px; height: 22px; border-radius: 50%;
            background: rgba(52, 211, 153, 0.2);
            border: 1.5px solid rgba(52, 211, 153, 0.5);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .feature-check svg { width: 12px; height: 12px; color: #34d399; }

        /* ===== RIGHT FORM PANEL ===== */
        .auth-panel {
            flex: 0 0 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 100vh;
            padding: 32px 20px;
            background: #fff;
        }
        .auth-panel-inner {
            width: 100%;
            max-width: 480px;
            margin: 0 auto;
        }

        /* ===== BRANDING (desktop) ===== */
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
        }
        .brand-icon {
            width: 44px; height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 20px rgba(37,99,235,0.3);
            flex-shrink: 0;
        }
        .brand-icon svg { width: 22px; height: 22px; color: #fff; }
        .brand-text h1 { font-size: 18px; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.3px; }
        .brand-text p { font-size: 11px; color: #94a3b8; margin: 2px 0 0; font-weight: 500; }

        /* ===== HEADING ===== */
        .auth-heading { margin-bottom: 24px; }
        .auth-heading h2 { font-size: 24px; font-weight: 800; color: #0f172a; margin: 0 0 6px; letter-spacing: -0.4px; }
        .auth-heading p { font-size: 13.5px; color: #64748b; margin: 0; }

        /* ===== ALERTS ===== */
        .alert {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 13px; font-weight: 500;
            margin-bottom: 20px;
        }
        .alert svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
        .alert-error { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }

        /* ===== FORM ===== */
        .form-group { margin-bottom: 16px; }
        .form-row-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px; }
        .form-label {
            display: block;
            font-size: 12.5px; font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }
        .form-label .req { color: #ef4444; }
        .form-label .badge { font-size: 10px; color: #3b82f6; font-weight: 600; margin-left: 6px; background: #eff6ff; border-radius: 4px; padding: 1px 6px; }
        .input-wrap { position: relative; }
        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 11px;
            font-size: 13.5px; font-weight: 500;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
            font-family: inherit;
        }
        .form-input:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .form-input.font-mono { font-family: 'Courier New', monospace; letter-spacing: 1px; }
        .form-input::placeholder { color: #cbd5e1; font-weight: 400; }
        .field-error { font-size: 11px; color: #be123c; margin-top: 4px; font-weight: 500; }
        .field-hint { font-size: 11px; color: #94a3b8; margin-top: 4px; }

        /* ===== Benefit card ===== */
        .benefit-card {
            padding: 12px 14px;
            border-radius: 12px;
            background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%);
            border: 1px solid #bfdbfe;
            margin-bottom: 18px;
        }
        .benefit-card ul { padding: 0; margin: 0; list-style: none; display: flex; flex-direction: column; gap: 6px; }
        .benefit-card li { display: flex; align-items: flex-start; gap: 8px; font-size: 12px; color: #374151; font-weight: 500; }
        .benefit-card li svg { width: 14px; height: 14px; color: #22c55e; flex-shrink: 0; margin-top: 1px; }

        /* ===== CTA BUTTON ===== */
        .btn-primary {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 14.5px; font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            box-shadow: 0 8px 24px var(--primary-shadow);
            cursor: pointer;
            transition: transform 0.12s ease, box-shadow 0.12s ease, filter 0.12s ease;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-family: inherit;
        }
        .btn-primary:hover { box-shadow: 0 12px 30px var(--primary-shadow); filter: brightness(1.06); }
        .btn-primary:active { transform: translateY(1px); }
        .btn-primary svg { width: 17px; height: 17px; }

        /* ===== FOOTER LINK ===== */
        .auth-foot {
            margin-top: 24px;
            text-align: center;
            font-size: 13.5px;
            color: #64748b;
        }
        .auth-foot a {
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
        }
        .auth-foot a:hover { text-decoration: underline; }

        /* ===== MOBILE: top cover image ===== */
        .mobile-cover {
            display: block;
            width: 100%;
            height: 180px;
            object-fit: cover;
            object-position: center top;
        }
        .mobile-cover-wrap {
            position: relative;
            margin: -32px -20px 24px;
            overflow: hidden;
            border-radius: 0 0 28px 28px;
        }
        .mobile-cover-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(15,23,42,0.3) 0%, rgba(15,23,42,0.65) 100%);
            border-radius: 0 0 28px 28px;
        }
        .mobile-cover-brand {
            position: absolute;
            bottom: 18px; left: 20px;
        }
        .mobile-cover-brand h1 { font-size: 20px; font-weight: 800; color: #fff; margin: 0; letter-spacing: -0.4px; }
        .mobile-cover-brand p { font-size: 11.5px; color: rgba(255,255,255,0.7); margin: 3px 0 0; }

        /* ===== RESPONSIVE ===== */
        @media (min-width: 900px) {
            body { background: #0f172a; }
            .auth-wrap { padding: 24px; gap: 0; }
            .auth-cover { display: flex; flex-direction: column; border-radius: 20px 0 0 20px; overflow: hidden; max-width: 500px; }
            .auth-panel {
                flex: 1 1 0;
                border-radius: 0 20px 20px 0;
                min-height: auto;
                overflow-y: auto;
                max-height: calc(100vh - 48px);
            }
            .mobile-cover-wrap { display: none; }
            .form-row-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 899px) {
            .form-row-grid { grid-template-columns: 1fr; }
        }
        @media (min-width: 1200px) {
            .auth-cover { max-width: 560px; }
        }
        @media (prefers-reduced-motion: reduce) {
            * { transition: none !important; animation: none !important; }
        }
    </style>
</head>
<body>
<div class="auth-wrap">

    {{-- ===== LEFT COVER (Desktop) ===== --}}
    <div class="auth-cover">
        <img src="{{ asset('perusahaan_page.jpg') }}" alt="SisRec Rekrutmen - Gedung Perusahaan">
        <div class="auth-cover-overlay">
            <div class="auth-cover-badge">
                <span class="auth-cover-badge-dot"></span>
                <span>Pendaftaran Terbuka</span>
            </div>
            <h2>Bergabung dan Raih<br>Karier Impian Anda</h2>
            <p>Buat akun pelamar dan mulai proses rekrutmen secara digital, transparan, dan efisien.</p>
            <ul class="feature-list">
                <li>
                    <span class="feature-check">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    Lamar lowongan secara online kapan saja
                </li>
                <li>
                    <span class="feature-check">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    Pantau status seleksi secara real-time
                </li>
                <li>
                    <span class="feature-check">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    Proses aman dan terenkripsi
                </li>
            </ul>
        </div>
    </div>

    {{-- ===== RIGHT FORM PANEL ===== --}}
    <div class="auth-panel">
        <div class="auth-panel-inner">

            {{-- Mobile: hero cover image --}}
            <div class="mobile-cover-wrap">
                <img class="mobile-cover" src="{{ asset('perusahaan_page.jpg') }}" alt="SisRec">
                <div class="mobile-cover-overlay"></div>
                <div class="mobile-cover-brand">
                    <h1>SisRec</h1>
                    <p>Sistem Rekrutmen Terpadu</p>
                </div>
            </div>

            {{-- Desktop: brand row --}}
            <div class="brand" style="display:none;" id="desktop-brand">
                <div class="brand-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <div class="brand-text">
                    <h1>SisRec</h1>
                    <p>Sistem Rekrutmen Terpadu</p>
                </div>
            </div>

            {{-- Page heading --}}
            <div class="auth-heading">
                <h2>Buat Akun Kandidat</h2>
                <p>Isi data diri Anda untuk membuat akun pelamar baru</p>
            </div>

            {{-- Validation errors --}}
            @if ($errors->any() && !$errors->has('email') && !$errors->has('password') && !$errors->has('first_name') && !$errors->has('last_name') && !$errors->has('phone_number') && !$errors->has('nik'))
                <div class="alert alert-error">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Terdapat kesalahan input. Silakan periksa kembali formulir di bawah.</span>
                </div>
            @endif

            {{-- Registration Form --}}
            <form action="{{ route('register') }}" method="POST" novalidate>
                @csrf

                {{-- Name row --}}
                <div class="form-row-grid">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="first_name">Nama Depan <span class="req">*</span></label>
                        <input id="first_name" type="text" name="first_name"
                               value="{{ old('first_name') }}"
                               class="form-input @error('first_name') border-red-400 @enderror"
                               placeholder="Ahmad"
                               autocomplete="given-name"
                               required autofocus>
                        @error('first_name')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="last_name">Nama Belakang</label>
                        <input id="last_name" type="text" name="last_name"
                               value="{{ old('last_name') }}"
                               class="form-input @error('last_name') border-red-400 @enderror"
                               placeholder="Fauzi"
                               autocomplete="family-name">
                        @error('last_name')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email Aktif <span class="req">*</span></label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}"
                           class="form-input @error('email') border-red-400 @enderror"
                           placeholder="nama@email.com"
                           autocomplete="email"
                           required>
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="form-group">
                    <label class="form-label" for="phone_number">Nomor HP / WhatsApp <span class="req">*</span></label>
                    <input id="phone_number" type="text" name="phone_number"
                           value="{{ old('phone_number') }}"
                           class="form-input @error('phone_number') border-red-400 @enderror"
                           placeholder="081234567890"
                           autocomplete="tel"
                           required>
                    @error('phone_number')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NIK --}}
                <div class="form-group">
                    <label class="form-label" for="nik">
                        Nomor Induk Kependudukan (NIK)
                        <span class="req">*</span>
                        <span class="badge">1 NIK = 1 Akun</span>
                    </label>
                    <input id="nik" type="text" name="nik"
                           value="{{ old('nik') }}"
                           maxlength="16"
                           class="form-input font-mono @error('nik') border-red-400 @enderror"
                           placeholder="16 digit NIK sesuai KTP"
                           required>
                    <p class="field-hint">Wajib 16 digit angka sesuai KTP. Digunakan sebagai identitas tunggal pelamar.</p>
                    @error('nik')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password row --}}
                <div class="form-row-grid">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="password">Kata Sandi <span class="req">*</span></label>
                        <input id="password" type="password" name="password"
                               class="form-input @error('password') border-red-400 @enderror"
                               placeholder="Min. 8 karakter"
                               autocomplete="new-password"
                               required>
                        @error('password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="password_confirmation">Ulangi Kata Sandi <span class="req">*</span></label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="form-input"
                               placeholder="••••••••"
                               autocomplete="new-password"
                               required>
                    </div>
                </div>

                {{-- Benefit card --}}
                <div class="benefit-card">
                    <ul>
                        <li>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Akun otomatis terdaftar dengan hak akses <strong>Kandidat Pelamar</strong>.
                        </li>
                        <li>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Dapat langsung melamar lowongan kerja dan mengunggah CV.
                        </li>
                    </ul>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary">
                    <span>Selesaikan Pendaftaran</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>

            {{-- Login link --}}
            <div class="auth-foot">
                Sudah memiliki akun?
                <a href="{{ route('login') }}">Masuk ke Sistem &rarr;</a>
            </div>

        </div>
    </div>

</div>

<script>
    // Show desktop brand on wide screens
    function checkWidth() {
        const brand = document.getElementById('desktop-brand');
        if (brand) brand.style.display = window.innerWidth >= 900 ? 'flex' : 'none';
    }
    checkWidth();
    window.addEventListener('resize', checkWidth);

    // Auto-format NIK: only allow digits, max 16
    const nikInput = document.getElementById('nik');
    if (nikInput) {
        nikInput.addEventListener('input', () => {
            nikInput.value = nikInput.value.replace(/\D/g, '').slice(0, 16);
        });
    }

    // Auto-format phone: only allow digits, + and spaces
    const phoneInput = document.getElementById('phone_number');
    if (phoneInput) {
        phoneInput.addEventListener('input', () => {
            phoneInput.value = phoneInput.value.replace(/[^0-9+\s-]/g, '');
        });
    }
</script>
</body>
</html>