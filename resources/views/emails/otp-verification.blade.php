<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi E-Arsip DLH</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f1f5f9; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); padding: 40px 32px; text-align: center; }
        .header-icon { width: 64px; height: 64px; background: rgba(255,255,255,0.15); border-radius: 16px; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { color: rgba(255,255,255,0.75); font-size: 13px; margin-top: 4px; }
        .body { padding: 40px 32px; }
        .greeting { font-size: 16px; color: #1e293b; font-weight: 600; margin-bottom: 12px; }
        .message { font-size: 14px; color: #64748b; line-height: 1.7; margin-bottom: 32px; }
        .otp-box { background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 16px; padding: 28px; text-align: center; margin-bottom: 28px; }
        .otp-label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 16px; }
        .otp-code { font-size: 48px; font-weight: 900; letter-spacing: 12px; color: #4f46e5; font-family: 'Courier New', monospace; line-height: 1; }
        .otp-timer { font-size: 12px; color: #94a3b8; margin-top: 14px; }
        .otp-timer span { color: #ef4444; font-weight: 700; }
        .divider { height: 1px; background: #f1f5f9; margin: 28px 0; }
        .warning { background: #fef9ec; border-left: 3px solid #f59e0b; border-radius: 8px; padding: 14px 16px; font-size: 13px; color: #92400e; line-height: 1.6; }
        .footer { padding: 24px 32px; background: #f8fafc; text-align: center; border-top: 1px solid #f1f5f9; }
        .footer p { font-size: 12px; color: #94a3b8; line-height: 1.7; }
        .footer strong { color: #4f46e5; }
    </style>
</head>
<body>
    <div class="wrapper">
        {{-- Header --}}
        <div class="header">
            <div class="header-icon" style="margin: 0 auto 16px; width:64px; height:64px; background:rgba(255,255,255,0.15); border-radius:16px; display:flex; align-items:center; justify-content:center;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <h1>{{ ($purpose ?? 'register') === 'change_email' ? 'Verifikasi Perubahan Email' : 'Verifikasi Email Anda' }}</h1>
            <p>Sistem Informasi Arsip · E-Arsip DLH</p>
        </div>

        {{-- Body --}}
        <div class="body">
            <p class="greeting">Halo, {{ $userName }}! 👋</p>
            @if(($purpose ?? 'register') === 'change_email')
            <p class="message">
                Anda baru saja mengajukan permohonan untuk mengganti alamat email akun Anda di <strong>E-Arsip DLH</strong>. Untuk menyelesaikan dan mengonfirmasi perubahan ke alamat email ini, masukkan kode OTP verifikasi berikut pada pengaturan profil Anda:
            </p>
            @else
            <p class="message">
                Terima kasih telah mendaftar di <strong>E-Arsip DLH</strong>. Untuk mengaktifkan akun Anda, masukkan kode verifikasi berikut di halaman konfirmasi:
            </p>
            @endif

            {{-- OTP Code Box --}}
            <div class="otp-box">
                <p class="otp-label">Kode Verifikasi OTP</p>
                <p class="otp-code">{{ $otp }}</p>
                <p class="otp-timer">Kode berlaku selama <span>15 menit</span></p>
            </div>

            <div class="warning">
                ⚠️ <strong>Jangan bagikan kode ini kepada siapapun.</strong> Tim E-Arsip DLH tidak pernah meminta kode verifikasi Anda. Jika Anda tidak merasa mengajukan perubahan ini, segera amankan akun Anda.
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem <strong>E-Arsip DLH</strong>.<br>
            Dinas Lingkungan Hidup · Jangan balas email ini.</p>
        </div>
    </div>
</body>
</html>
