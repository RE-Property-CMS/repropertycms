<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Demo Access — {{ config('app.name') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #1e293b; }
        .wrapper { max-width: 580px; margin: 40px auto; }
        .card { background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
        .header { background: linear-gradient(135deg, #1e3a5f, #0f2340); padding: 36px 40px; text-align: center; }
        .header h1 { color: #fff; font-size: 22px; font-weight: 700; margin-top: 12px; }
        .header p { color: #93c5fd; font-size: 14px; margin-top: 6px; }
        .body { padding: 36px 40px; }
        .greeting { font-size: 16px; color: #334155; margin-bottom: 20px; line-height: 1.6; }
        .cred-block { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; margin-bottom: 16px; }
        .cred-block h3 { font-size: 13px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; margin-bottom: 14px; }
        .cred-row { display: flex; gap: 8px; align-items: baseline; margin-bottom: 8px; }
        .cred-label { font-size: 12px; color: #94a3b8; width: 70px; flex-shrink: 0; }
        .cred-value { font-size: 13px; font-weight: 600; color: #1e293b; word-break: break-all; }
        .cred-value.mono { font-family: 'Courier New', monospace; background: #e2e8f0; padding: 2px 8px; border-radius: 4px; }
        .btn { display: block; text-align: center; padding: 13px 24px; border-radius: 10px; font-size: 14px; font-weight: 700; text-decoration: none; margin-top: 14px; }
        .btn-admin { background: #7c3aed; color: #fff !important; }
        .btn-agent { background: #2563eb; color: #fff !important; }
        .note { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 18px; margin-top: 24px; font-size: 13px; color: #78350f; line-height: 1.6; }
        .note strong { display: block; margin-bottom: 4px; }
        .footer { padding: 24px 40px; border-top: 1px solid #f1f5f9; text-align: center; font-size: 12px; color: #94a3b8; }
        .footer a { color: #3b82f6; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">

        {{-- Header --}}
        <div class="header">
            <div style="display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;background:rgba(255,255,255,.15);border-radius:12px;">
                <span style="font-size:22px;">🏠</span>
            </div>
            <h1>Your Demo is Ready</h1>
            <p>{{ config('app.name') }} — Interactive Sandbox</p>
        </div>

        {{-- Body --}}
        <div class="body">
            <p class="greeting">
                Hi {{ $leadName ?: 'there' }},<br><br>
                Your private demo sandbox has been created. Below are your login credentials for both the <strong>Admin</strong> and <strong>Agent</strong> portals. Click a button to go to the login page, then enter your credentials manually.
            </p>

            {{-- Admin credentials --}}
            <div class="cred-block" style="border-left: 4px solid #7c3aed;">
                <h3 style="color: #7c3aed;">Super Admin Portal</h3>
                <div class="cred-row">
                    <span class="cred-label">Email</span>
                    <span class="cred-value mono">{{ $adminEmail }}</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Password</span>
                    <span class="cred-value mono">{{ $password }}</span>
                </div>
                <a href="{{ url('/demo/' . $token . '/enter/admin') }}" class="btn btn-admin">
                    Login as Admin &rarr;
                </a>
            </div>

            {{-- Agent credentials --}}
            <div class="cred-block" style="border-left: 4px solid #2563eb;">
                <h3 style="color: #2563eb;">Agent Portal</h3>
                <div class="cred-row">
                    <span class="cred-label">Email</span>
                    <span class="cred-value mono">{{ $agentEmail }}</span>
                </div>
                <div class="cred-row">
                    <span class="cred-label">Password</span>
                    <span class="cred-value mono">{{ $password }}</span>
                </div>
                <a href="{{ url('/demo/' . $token . '/enter/agent') }}" class="btn btn-agent">
                    Login as Agent &rarr;
                </a>
            </div>

            <div class="note">
                <strong>Important</strong>
                Click a button above to go to the login page — your demo session only activates via these links. The sandbox expires in <strong>{{ $duration }}</strong> and all data is automatically deleted after.
            </div>
        </div>

        <div class="footer">
            This email was sent because someone requested a demo at
            <a href="{{ url('/') }}">{{ config('app.name') }}</a>.<br>
            If this wasn't you, you can safely ignore this email.<br><br>
            Questions? Contact us at <a href="mailto:sales@repropertycms.com">sales@repropertycms.com</a>
        </div>
    </div>
</div>
</body>
</html>
