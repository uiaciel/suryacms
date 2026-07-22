<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pesan Baru dari Form Kontak Website [{{ config('app.name') }}]</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.6; background-color: #f4f7f6; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #e1e8ed; }
        .header { background-color: #2c3e50; color: #ffffff; padding: 25px; text-align: center; }
        .header h2 { margin: 0; font-size: 20px; font-weight: 600; letter-spacing: 0.5px; }
        .alert-important { background-color: #fff3cd; border-left: 5px solid #ffc107; padding: 15px; margin: 20px; color: #856404; font-weight: bold; border-radius: 4px; }
        .content { padding: 30px; }
        .section-title { font-size: 16px; font-weight: bold; color: #2c3e50; border-bottom: 2px solid #ecf0f1; padding-bottom: 8px; margin-bottom: 15px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .info-table td { padding: 10px 0; border-bottom: 1px solid #f8f9fa; }
        .info-table td.label { width: 30%; color: #7f8c8d; font-weight: 600; }
        .info-table td.value { color: #2c3e50; }
        .message-block { background-color: #f8f9fa; border-radius: 6px; padding: 20px; border: 1px solid #e9ecef; margin-bottom: 25px; }
        .message-title { font-weight: bold; margin-bottom: 10px; color: #34495e; }
        .footer { background-color: #f8f9fa; padding: 20px; font-size: 12px; color: #95a5a6; border-top: 1px solid #ecf0f1; }
        .footer p { margin: 5px 0; }
        .btn-reply { display: inline-block; background-color: #3498db; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Pesan Baru dari Form Kontak Website</h2>
            <p style="margin: 5px 0 0 0; opacity: 0.8;">{{ config('app.name') }}</p>
        </div>

        @if($contact->is_important)
        <div class="alert-important">
            ⚠️ PESAN PRIORITAS / PENTING
        </div>
        @endif

        <div class="content">
            <div class="section-title">Detail Pengirim</div>
            <table class="info-table">
                <tr>
                    <td class="label">Nama</td>
                    <td class="value">{{ $contact->name }}</td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td class="value">{{ $contact->email }}</td>
                </tr>
                <tr>
                    <td class="label">Telepon</td>
                    <td class="value">{{ $contact->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Dikirim Pada</td>
                    <td class="value">{{ $contact->created_at->format('d M Y, H:i') }}</td>
                </tr>
            </table>

            <div class="section-title">Isi Pesan</div>
            <div class="message-block">
                <div class="message-title">Subjek: {{ $contact->subject }}</div>
                <div style="white-space: pre-wrap;">{!! nl2br(e($contact->message)) !!}</div>
            </div>

            <div style="text-align: center;">
                <a href="mailto:{{ $contact->email }}?subject=Re: {{ $contact->subject }}" class="btn-reply">Balas Langsung ke Pengirim</a>
            </div>
        </div>

        <div class="footer">
            <p><strong>Metadata Sistem:</strong></p>
            <p>Referrer URL: {{ $contact->referrer ?? 'Direct' }}</p>
            <p>IP Address: {{ $contact->ip_address }}</p>
            <p>User Agent: {{ $contact->user_agent }}</p>
            <hr style="border: 0; border-top: 1px solid #ecf0f1; margin: 15px 0;">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>
