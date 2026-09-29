<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Kata Sandi — SisRec Rekrutmen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4f46e5; --primary-shadow: rgba(79,70,229,.30); }
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: #f1f5f9; -webkit-font-smoothing: antialiased; }

        /* ── Layout ── */
        .auth-wrap { min-height: 100vh; display: flex; align-items: stretch; }

        /* ── Desktop left cover ── */
        .auth-cover { display: none; position: relative; flex: 1 1 0; overflow: hidden; }
        .auth-cover img { width: 100%; height: 100%; object-fit: cover; object-position: center; display: block; }
        .auth-cover-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(155deg, rgba(17,24,39,.70) 0%, rgba(49,46,129,.65) 55%, rgba(17,24,39,.80) 100%);
            display: flex; flex-direction: column; justify-content: flex-end; padding: 48px;
        }
        .auth-cover-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
            backdrop-filter: blur(8px); border-radius: 100px;
            padding: 6px 14px 6px 8px; width: fit-content; margin-bottom: 20px;
        }
        .auth-cover-badge-dot { width: 8px; height: 8px; border-radius: 50%; background: #34d399; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.4} }
        .auth-cover-badge span { font-size: 12px; font-weight: 600; color: rgba(255,255,255,.85); }
        .auth-cover h2 { font-size: 32px; font-weight: 800; color: #fff; margin: 0 0 12px; line-height: 1.25; letter-spacing: -.5px; }
        .auth-cover p { font-size: 15px; color: rgba(255,255,255,.65); margin: 0; line-height: 1.6; max-width: 360px; }

        /* ── Right panel ── */
        .auth-panel { flex: 0 0 100%; display: flex; flex-direction: column; justify-content: center; min-height: 100vh; padding: 32px 20px; background: #fff; }
        .auth-panel-inner { width: 100%; max-width: 440px; margin: 0 auto; }

        /* ── Desktop brand ── */
        .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 36px; }
        .brand-icon { width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg,#4f46e5,#7c3aed); display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(79,70,229,.3); flex-shrink: 0; }
        .brand-icon svg { width: 22px; height: 22px; color: #fff; }
        .brand-text h1 { font-size: 17px; font-weight: 800; color: #0f172a; margin: 0; }
        .brand-text p { font-size: 11px; color: #94a3b8; margin: 2px 0 0; }

        /* ── Mobile cover ── */
        .mobile-cover { display: block; width: 100%; height: 180px; object-fit: cover; object-position: center 30%; }
        .mobile-cover-wrap { position: relative; margin: -32px -20px 24px; overflow: hidden; border-radius: 0 0 28px 28px; }
        .mobile-cover-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(15,23,42,.3) 0%, rgba(15,23,42,.65) 100%); border-radius: 0 0 28px 28px; }
        .mobile-cover-brand { position: absolute; bottom: 18px; left: 20px; }
        .mobile-cover-brand h1 { font-size: 20px; font-weight: 800; color: #fff; margin: 0; }
        .mobile-cover-brand p { font-size: 11.5px; color: rgba(255,255,255,.7); margin: 3px 0 0; }

        /* ── Page heading ── */
        .auth-heading { margin-bottom: 24px; }
        .auth-heading .icon-wrap { width: 52px; height: 52px; border-radius: 16px; background: linear-gradient(135deg,#ede9fe,#ddd6fe); display: flex; align-items: center; justify-content: center; margin-bottom: 16px; }
        .auth-heading .icon-wrap svg { width: 26px; height: 26px; color: #7c3aed; }
        .auth-heading h2 { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 5px; letter-spacing: -.4px; }
        .auth-heading p { font-size: 13px; color: #64748b; margin: 0; line-height: 1.55; }

        /* ── Alerts ── */
        .alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border-radius: 12px; font-size: 13px; font-weight: 500; margin-bottom: 20px; }
        .alert svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
        .alert-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
        .alert-error { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }

        /* ── Form ── */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12.5px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .input-wrap { position: relative; }
        .input-icon { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); color: #94a3b8; display: flex; pointer-events: none; }
        .input-icon svg { width: 16px; height: 16px; }
        .form-input {
            width: 100%; padding: 12px 14px 12px 40px;
            border: 1.5px solid #e2e8f0; border-radius: 12px;
            font-size: 14px; font-weight: 500; color: #0f172a; background: #f8fafc;
            outline: none; transition: border-color .18s, box-shadow .18s, background .18s;
            font-family: inherit; -webkit-appearance: none;
        }
        .form-input:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
        .form-input::placeholder { color: #cbd5e1; font-weight: 400; }
        .field-error { font-size: 11px; color: #be123c; margin-top: 4px; font-weight: 500; }

        /* ── CTA ── */
        .btn-primary {
            width: 100%; padding: 14px; border: none; border-radius: 12px;
            font-size: 15px; font-weight: 700; color: #fff;
            background: linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);
            box-shadow: 0 8px 22px var(--primary-shadow);
            cursor: pointer; transition: transform .12s, box-shadow .12s, filter .12s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            font-family: inherit; -webkit-appearance: none;
        }
        .btn-primary:hover { filter: brightness(1.06); box-shadow: 0 12px 28px var(--primary-shadow); }
        .btn-primary:active { transform: translateY(1px); }
        .btn-primary svg { width: 17px; height: 17px; }

        /* ── Back link ── */
        .auth-foot { margin-top: 26px; text-align: center; font-size: 13.5px; color: #64748b; }
        .auth-foot a { color: var(--primary); font-weight: 700; text-decoration: none; }
        .auth-foot a:hover { text-decoration: underline; }

        /* ── Info box ── */
        .info-box { background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 12px; padding: 14px 16px; margin-bottom: 20px; font-size: 12.5px; color: #0369a1; line-height: 1.6; }
        .info-box strong { color: #0c4a6e; }

        /* ── Desktop responsive ── */
        @media (min-width: 900px) {
            body { background: #0f172a; }
            .auth-wrap { padding: 24px; }
            .auth-cover { display: flex; flex-direction: column; border-radius: 20px 0 0 20px; overflow: hidden; max-width: 520px; }
            .auth-panel { flex: 1 1 0; border-radius: 0 20px 20px 0; min-height: auto; overflow-y: auto; max-height: calc(100vh - 48px); }
            .mobile-cover-wrap { display: none; }
        }
        @media (min-width: 1200px) { .auth-cover { max-width: 580px; } }
        @media (prefers-reduced-motion: reduce) { * { transition: none !important; animation: none !important; } }
    </style>
</head>
<body>
<div class="auth-wrap">

    {{-- Desktop Left Cover --}}
    <div class="auth-cover">
        <img src="{{ asset('perusahaan_page.jpg') }}" alt="SisRec — Gedung Perusahaan">
        <div class="auth-cover-overlay">
            <div class="auth-cover-badge">
                <span class="auth-cover-badge-dot"></span>
                <span>Keamanan Akun Terjamin</span>
            </div>
            <h2>Reset Kata Sandi<br>dengan Aman</h2>
            <p>Masukkan email terdaftar Anda. Kami akan mengirimkan tautan untuk membuat kata sandi baru yang aman.</p>
        </div>
    </div>

    {{-- Right Form Panel --}}
    <div class="auth-panel">
        <div class="auth-panel-inner">

            {{-- Mobile hero --}}
            <div class="mobile-cover-wrap">
                <img class="mobile-cover" src="{{ asset('perusahaan_page.jpg') }}" alt="SisRec">
                <div class="mobile-cover-overlay"></div>
                <div class="mobile-cover-brand">
                    <h1>SisRec</h1>
                    <p>Sistem Rekrutmen Terpadu</p>
                </div>
            </div>

            {{-- Desktop brand --}}
            <div class="brand" style="display:none;" id="desktop-brand">
                <div class="brand-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="brand-text">
                    <h1>SisRec</h1>
                    <p>Sistem Rekrutmen Terpadu</p>
                </div>
            </div>

            {{-- Heading --}}
            <div class="auth-heading">
                <div class="icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <h2>Lupa Kata Sandi?</h2>
                <p>Tidak masalah! Masukkan email terdaftar Anda dan kami akan mengirimkan tautan reset kata sandi.</p>
            </div>

            {{-- Status alert --}}
            @if(session('status'))
                <div class="alert alert-success">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->has('token'))
                <div class="alert alert-error">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>{{ $errors->first('token') }}</span>
                </div>
            @endif

            {{-- Info box --}}
            <div class="info-box">
                📧 <strong>Cek kotak masuk Anda</strong> setelah mengirim permintaan. Tautan reset berlaku selama <strong>60 menit</strong>. Jika tidak muncul, periksa folder <em>spam</em>.
            </div>

            {{-- Form --}}
            <form action="{{ route('password.email') }}" method="POST" novalidate>
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email Terdaftar</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </span>
                        <input id="email" type="email" name="email"
                               value="{{ old('email') }}"
                               class="form-input @error('email') border-red-400 @enderror"
                               placeholder="nama@email.com"
                               autocomplete="email"
                               required autofocus>
                    </div>
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Kirim Tautan Reset</span>
                </button>
            </form>

            <div class="auth-foot">
                <a href="{{ route('login') }}">&larr; Kembali ke halaman masuk</a>
            </div>

        </div>
    </div>

</div>
<script>
    function syncBrand() {
        const b = document.getElementById('desktop-brand');
        if (b) b.style.display = window.innerWidth >= 900 ? 'flex' : 'none';
    }
    syncBrand();
    window.addEventListener('resize', syncBrand);
</script>
</body>
</html>
