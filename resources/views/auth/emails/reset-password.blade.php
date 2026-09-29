<!DOCTYPE html>
<html lang="id" style="margin:0;padding:0;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Kata Sandi — SisRec</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:'Segoe UI',Arial,sans-serif;-webkit-font-smoothing:antialiased;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:40px 20px;">
    <tr>
        <td align="center">
            <table width="100%" style="max-width:560px;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">
                <!-- Header -->
                <tr>
                    <td style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);padding:36px 40px 28px;text-align:center;">
                        <div style="display:inline-flex;align-items:center;justify-content:center;width:52px;height:52px;background:rgba(255,255,255,0.15);border-radius:14px;margin:0 auto 16px;">
                            <span style="font-size:26px;">🔑</span>
                        </div>
                        <h1 style="margin:0;font-size:22px;font-weight:800;color:#ffffff;letter-spacing:-0.4px;">Reset Kata Sandi</h1>
                        <p style="margin:6px 0 0;font-size:13px;color:rgba(255,255,255,0.75);">SisRec — Sistem Rekrutmen Terpadu</p>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:36px 40px;">
                        <p style="margin:0 0 18px;font-size:15px;color:#374151;line-height:1.6;">
                            Halo, <strong style="color:#0f172a;">{{ $user->name }}</strong>!
                        </p>
                        <p style="margin:0 0 28px;font-size:14px;color:#6b7280;line-height:1.65;">
                            Kami menerima permintaan untuk mereset kata sandi akun Anda yang terdaftar dengan email
                            <strong style="color:#374151;">{{ $user->email }}</strong>.
                            Klik tombol di bawah untuk membuat kata sandi baru:
                        </p>

                        <!-- CTA Button -->
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td align="center" style="padding:0 0 28px;">
                                    <a href="{{ $resetUrl }}"
                                       style="display:inline-block;padding:14px 36px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;border-radius:12px;letter-spacing:-0.2px;box-shadow:0 8px 20px rgba(79,70,229,0.35);">
                                        Reset Kata Sandi Sekarang &rarr;
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <!-- Link fallback -->
                        <div style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:12px;padding:16px;margin-bottom:24px;">
                            <p style="margin:0 0 6px;font-size:12px;font-weight:600;color:#374151;">Tombol tidak berfungsi? Salin tautan ini ke browser:</p>
                            <p style="margin:0;font-size:11px;color:#6b7280;word-break:break-all;font-family:'Courier New',monospace;">{{ $resetUrl }}</p>
                        </div>

                        <!-- Warning -->
                        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:14px 16px;">
                            <p style="margin:0;font-size:12.5px;color:#92400e;line-height:1.55;">
                                ⚠️ <strong>Tautan ini hanya berlaku selama {{ $expiresInMinutes }} menit.</strong><br>
                                Jika Anda tidak meminta reset kata sandi, abaikan email ini — kata sandi Anda tidak akan berubah.
                            </p>
                        </div>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#f8fafc;border-top:1px solid #e2e8f0;padding:20px 40px;text-align:center;">
                        <p style="margin:0;font-size:11.5px;color:#9ca3af;">
                            Email ini dikirim otomatis oleh <strong>SisRec Rekrutmen</strong>.<br>
                            Mohon tidak membalas email ini.
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
