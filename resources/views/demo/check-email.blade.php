<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check Your Email — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
</head>
<body class="min-h-screen flex items-center justify-center" style="background: linear-gradient(135deg, #1e3a5f 0%, #0f2340 50%, #162d4a 100%); font-family: system-ui, sans-serif;">

    <div class="text-center px-6" style="max-width: 480px; width: 100%;">

        {{-- Icon --}}
        <div style="display:inline-flex;align-items:center;justify-content:center;width:80px;height:80px;background:rgba(59,130,246,.2);border:2px solid rgba(59,130,246,.4);border-radius:50%;margin-bottom:28px;">
            <i class="fa fa-envelope-open-text" style="font-size:2rem;color:#60a5fa;"></i>
        </div>

        <h1 style="font-size:1.8rem;font-weight:800;color:#fff;margin-bottom:12px;">Check Your Inbox</h1>

        <p style="font-size:1rem;color:#93c5fd;line-height:1.7;margin-bottom:10px;">
            We've sent your demo credentials to<br>
            <strong style="color:#fff;">{{ session('demo_lead_email') }}</strong>
        </p>

        <p style="font-size:0.875rem;color:#64748b;margin-bottom:32px;">
            Click the login buttons in the email to activate your demo session, then enter your credentials on the login page.
        </p>

        {{-- Tips --}}
        <div style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:20px 24px;text-align:left;margin-bottom:28px;">
            <p style="font-size:0.78rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#475569;margin-bottom:12px;">What to do next</p>
            <div style="display:flex;flex-direction:column;gap:10px;">
                <div style="display:flex;align-items:flex-start;gap:12px;">
                    <span style="flex-shrink:0;width:22px;height:22px;background:#2563eb;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;">1</span>
                    <span style="font-size:0.85rem;color:#94a3b8;">Open the email from <strong style="color:#cbd5e1;">{{ config('app.name') }}</strong></span>
                </div>
                <div style="display:flex;align-items:flex-start;gap:12px;">
                    <span style="flex-shrink:0;width:22px;height:22px;background:#2563eb;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;">2</span>
                    <span style="font-size:0.85rem;color:#94a3b8;">Click <strong style="color:#cbd5e1;">"Login as Admin"</strong> or <strong style="color:#cbd5e1;">"Login as Agent"</strong></span>
                </div>
                <div style="display:flex;align-items:flex-start;gap:12px;">
                    <span style="flex-shrink:0;width:22px;height:22px;background:#2563eb;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;">3</span>
                    <span style="font-size:0.85rem;color:#94a3b8;">Enter the credentials from the email and start exploring</span>
                </div>
            </div>
        </div>

        <p style="font-size:0.78rem;color:#475569;">
            Didn't receive it? Check your spam folder or
            <a href="{{ route('demo.landing') }}" style="color:#60a5fa;text-decoration:underline;">start a new demo</a>.
        </p>
    </div>

</body>
</html>
