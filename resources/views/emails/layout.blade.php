<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? 'Auralis8' }}</title>
    <style>
        body { margin:0; padding:0; background:#f4f7fa; font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif; }
        .email-wrapper { max-width:600px; margin:0 auto; padding:24px 16px; }
        .email-header { text-align:center; padding:24px 0 16px; }
        .email-header img { max-width:160px; height:auto; }
        .email-body { background:#fff; border-radius:16px; padding:32px 28px; box-shadow:0 2px 8px rgba(0,0,0,0.06); }
        .email-body h1 { font-size:20px; font-weight:700; color:#111827; margin:0 0 12px; }
        .email-body p { font-size:15px; color:#4b5563; line-height:1.6; margin:0 0 12px; }
        .email-details { background:#f9fafb; border-radius:12px; padding:16px 20px; margin:16px 0; }
        .email-details table { width:100%; border-collapse:collapse; }
        .email-details td { padding:6px 0; font-size:14px; }
        .email-details td:first-child { color:#6b7280; width:120px; }
        .email-details td:last-child { font-weight:600; color:#111827; }
        .email-btn { display:inline-block; padding:12px 28px; background:#2563eb; color:#fff !important; text-decoration:none; border-radius:10px; font-weight:700; font-size:15px; margin:16px 0; }
        .email-footer { text-align:center; padding:20px 0; font-size:13px; color:#9ca3af; }
        .email-footer a { color:#2563eb; text-decoration:none; }
        .email-divider { height:1px; background:#e5e7eb; margin:20px 0; }
        .email-badge { display:inline-block; padding:4px 14px; border-radius:20px; font-size:13px; font-weight:600; }
        .email-badge-green { background:#dcfce7; color:#16a34a; }
        .email-badge-red { background:#fef2f2; color:#dc2626; }
        .email-badge-yellow { background:#fff7ed; color:#c2410c; }
        .email-badge-blue { background:#eff6ff; color:#1d4ed8; }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-header">
            <img src="{{ asset('images/logo.png') }}" alt="Auralis8" style="max-width:160px;height:auto;">
        </div>
        <div class="email-body">
            <h1>{{ $heading ?? '' }}</h1>
            {{ $slot }}
        </div>
        <div class="email-footer">
            <p>&copy; {{ date('Y') }} Auralis8. All rights reserved.</p>
            <p>Lahad Datu, Sabah, Malaysia</p>
        </div>
    </div>
</body>
</html>
