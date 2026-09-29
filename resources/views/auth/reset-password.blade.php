<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Kata Sandi Baru — SisRec Rekrutmen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4f46e5; --primary-shadow: rgba(79,70,229,.30); }
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background: #f1f5f9; -webkit-font-smoothing: antialiased; }

        .auth-wrap { min-height: 100vh; display: flex; align-items: stretch; }

        /* Desktop cover */
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
        .badge-dot { width: 8px; height: 8px; border-radius: 50%; background: #34d399; animation: blink 2s infinite; }
        @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.4} }
        .auth-cover-badge span { font-size: 12px; font-weight: 600; color: rgba(255,255,255,.85); }
        .auth-cover h2 { font-size: 32px; font-weight: 800; color: #fff; margin: 0 0 12px; line-height: 1.25; letter-spacing: -.5px; }
        .auth-cover p { font-size: 15px; color: rgba(255,255,255,.65); margin: 0; line-height: 1.6; max-width: 360px; }

        /* Right panel */
        .auth-panel { flex: 0 0 100%; display: flex; flex-direction: column; justify-content: center; min-height: 100vh; padding: 32px 20px; background: #fff; }
        .auth-panel-inner { width: 100%; max-width: 440px; margin: 0 auto; }

        /* Desktop brand */
        .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 36px; }
        .brand-icon { width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg,#4f46e5,#7c3aed); display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 20px rgba(79,70,229,.3); flex-shrink: 0; }
        .brand-icon svg { width: 22px; height: 22px; color: #fff; }
        .brand-text h1 { font-size: 17px; font-weight: 800; color: #0f172a; margin: 0; }
        .brand-text p { font-size: 11px; color: #94a3b8; margin: 2px 0 0; }

        /* Mobile cover */
        .mobile-cover { display: block; width: 100%; height: 180px; object-fit: cover; object-position: center 30%; }
        .mobile-cover-wrap { position: relative; margin: -32px -20px 24px; overflow: hidden; border-radius: 0 0 28px 28px; }
        .mobile-cover-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(15,23,42,.3) 0%, rgba(15,23,42,.65) 100%); border-radius: 0 0 28px 28px; }
        .mobile-cover-brand { position: absolute; bottom: 18px; left: 20px; }
        .mobile-cover-brand h1 { font-size: 20px; font-weight: 800; color: #fff; margin: 0; }
        .mobile-cover-brand p { font-size: 11.5px; color: rgba(255,255,255,.7); margin: 3px 0 0; }

        /* Heading */
        .auth-heading { margin-bottom: 24px; }
        .auth-heading .icon-wrap { width: 52px; height: 52px; border-radius: 16px; background: linear-gradient(135deg,#ede9fe,#ddd6fe); display: flex; align-items: center; justify-content: center; margin-bottom: 16px; }
        .auth-heading .icon-wrap svg { width: 26px; height: 26px; color: #7c3aed; }
        .auth-heading h2 { font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 5px; letter-spacing: -.4px; }
        .auth-heading p { font-size: 13px; color: #64748b; margin: 0; line-height: 1.55; }

        /* Alerts */
        .alert { display: flex; align-items: flex-start; gap: 10px; padding: 12px 14px; border-radius: 12px; font-size: 13px; font-weight: 500; margin-bottom: 20px; }
        .alert svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }
        .alert-error { background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; }

        /* Form */
        .form-group { margin-bottom: 16px; }
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
        .form-input.no-icon { padding-left: 14px; }
        .form-input:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 3px rgba(79,70,229,.12); }
        .form-input::placeholder { color: #cbd5e1; font-weight: 400; }
        .field-error { font-size: 11px; color: #be123c; margin-top: 4px; font-weight: 500; }

        /* Password strength */
        .strength-bar { display: flex; gap: 4px; margin-top: 8px; }
        .strength-bar span { flex: 1; height: 4px; border-radius: 4px; background: #e2e8f0; transition: background .25s; }
        .strength-label { font-size: 11px; color: #94a3b8; margin-top: 4px; min-height: 16px; }

        /* Toggle pw button */
        .toggle-pw { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #94a3b8; padding: 4px; border-radius: 6px; display: flex; transition: color .15s; }
        .toggle-pw:hover { color: #475569; }
        .toggle-pw svg { width: 17px; height: 17px; }
        .form-input.has-toggle { padding-right: 44px; }

        /* CTA */
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

        .auth-foot { margin-top: 26px; text-align: center; font-size: 13.5px; color: #64748b; }
        .auth-foot a { color: var(--primary); font-weight: 700; text-decoration: none; }
        .auth-foot a:hover { text-decoration: underline; }

        /* Desktop responsive */
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
                <span class="badge-dot"></span>
                <span>Keamanan Akun Terjamin</span>
            </div>
            <h2>Buat Kata Sandi<br>Baru Anda</h2>
            <p>Masukkan kata sandi baru yang kuat. Pastikan minimal 8 karakter dan mudah Anda ingat.</p>
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
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h2>Buat Kata Sandi Baru</h2>
                <p>Kata sandi baru Anda harus berbeda dari kata sandi sebelumnya dan minimal 8 karakter.</p>
            </div>

            {{-- Error alerts --}}
            @if($errors->has('token'))
                <div class="alert alert-error">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ $errors->first('token') }} <a href="{{ route('password.request') }}" style="color:#be123c;font-weight:700;">Minta tautan baru &rarr;</a></span>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('password.update') }}" method="POST" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                {{-- Email (read-only, prefilled) --}}
                <div class="form-group">
                    <label class="form-label" for="email">Alamat Email</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email', $email) }}"
                           class="form-input no-icon @error('email') border-red-400 @enderror"
                           placeholder="nama@email.com"
                           autocomplete="email"
                           required>
                    @error('email')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- New password --}}
                <div class="form-group">
                    <label class="form-label" for="password">Kata Sandi Baru</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </span>
                        <input id="password" type="password" name="password"
                               class="form-input has-toggle @error('password') border-red-400 @enderror"
                               placeholder="Min. 8 karakter"
                               autocomplete="new-password"
                               required>
                        <button type="button" class="toggle-pw" id="toggle-pw1" aria-label="Tampilkan kata sandi">
                            <svg id="eye1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Strength indicator --}}
                    <div class="strength-bar" id="strength-bar">
                        <span id="s1"></span><span id="s2"></span><span id="s3"></span><span id="s4"></span>
                    </div>
                    <div class="strength-label" id="strength-label"></div>
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm password --}}
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </span>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="form-input has-toggle"
                               placeholder="Ulangi kata sandi baru"
                               autocomplete="new-password"
                               required>
                        <button type="button" class="toggle-pw" id="toggle-pw2" aria-label="Tampilkan konfirmasi">
                            <svg id="eye2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary" style="margin-top:8px;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Simpan Kata Sandi Baru</span>
                </button>
            </form>

            <div class="auth-foot">
                <a href="{{ route('login') }}">&larr; Kembali ke halaman masuk</a>
            </div>

        </div>
    </div>

</div>
<script>
    // Desktop brand
    function syncBrand() {
        const b = document.getElementById('desktop-brand');
        if (b) b.style.display = window.innerWidth >= 900 ? 'flex' : 'none';
    }
    syncBrand();
    window.addEventListener('resize', syncBrand);

    // Toggle password helpers
    const eyeOpen   = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    const eyeClosed = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;

    function makeToggle(btnId, inputId, eyeId) {
        const btn = document.getElementById(btnId);
        const input = document.getElementById(inputId);
        const eye = document.getElementById(eyeId);
        if (!btn || !input || !eye) return;
        btn.addEventListener('click', () => {
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            eye.innerHTML = show ? eyeClosed : eyeOpen;
        });
    }
    makeToggle('toggle-pw1', 'password', 'eye1');
    makeToggle('toggle-pw2', 'password_confirmation', 'eye2');

    // Password strength meter
    const pwInput = document.getElementById('password');
    const bars = [document.getElementById('s1'), document.getElementById('s2'), document.getElementById('s3'), document.getElementById('s4')];
    const label = document.getElementById('strength-label');
    const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
    const labels = ['Sangat lemah', 'Lemah', 'Cukup', 'Kuat'];

    if (pwInput) {
        pwInput.addEventListener('input', () => {
            const val = pwInput.value;
            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            bars.forEach((b, i) => {
                b.style.background = i < score ? colors[score - 1] : '#e2e8f0';
            });
            label.textContent = val.length > 0 ? labels[Math.max(0, score - 1)] : '';
            label.style.color = val.length > 0 ? colors[Math.max(0, score - 1)] : '#94a3b8';
        });
    }
</script>
</body>
</html>
