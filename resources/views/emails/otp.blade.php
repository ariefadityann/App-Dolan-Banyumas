<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 40px 30px;
        }
        .content p {
            color: #333;
            line-height: 1.6;
            font-size: 16px;
        }
        .otp-box {
            background: #f8f9fa;
            border: 2px dashed #667eea;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-code {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 10px;
            font-family: 'Courier New', monospace;
        }
        .otp-label {
            color: #666;
            font-size: 14px;
            margin-top: 10px;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .warning p {
            margin: 0;
            color: #856404;
            font-size: 14px;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🏖️ Dolan Banyumas</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px;">
                @if($type === 'email_verification')
                    Verifikasi Email Anda
                @else
                    Reset Password
                @endif
            </p>
        </div>
        
        <div class="content">
            @if($type === 'email_verification')
                <p>Halo!</p>
                <p>Terima kasih telah mendaftar di <strong>Dolan Banyumas</strong>. Untuk melanjutkan, silakan verifikasi email Anda dengan memasukkan kode OTP berikut:</p>
            @else
                <p>Halo!</p>
                <p>Kami menerima permintaan untuk mereset password akun Anda di <strong>Dolan Banyumas</strong>. Gunakan kode OTP berikut untuk melanjutkan:</p>
            @endif
            
            <div class="otp-box">
                <div class="otp-code">{{ $otpCode }}</div>
                <div class="otp-label">Kode OTP Anda</div>
            </div>
            
            <div class="warning">
                <p><strong>⚠️ Penting:</strong></p>
                <p>• Kode ini berlaku selama <strong>10 menit</strong></p>
                <p>• Jangan bagikan kode ini kepada siapapun</p>
                <p>• Jika Anda tidak melakukan permintaan ini, abaikan email ini</p>
            </div>
            
            @if($type === 'email_verification')
                <p>Setelah verifikasi berhasil, Anda dapat langsung login dan menikmati layanan kami.</p>
            @else
                <p>Setelah verifikasi OTP, Anda akan diminta untuk membuat password baru.</p>
            @endif
            
            <p>Terima kasih,<br><strong>Tim Dolan Banyumas</strong></p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim otomatis, mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} Dolan Banyumas. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
