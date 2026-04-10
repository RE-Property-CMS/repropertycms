@extends('admin.layouts.default')

@section('title', 'Mail Settings')

@section('content')

    {{-- Page heading --}}
    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-0">Mail Settings</h5>
            <nav style="font-size:13px;color:#6b7280;margin-top:4px;display:flex;align-items:center;gap:6px;">
                <a href="{{ route('admin.settings.index') }}" style="color:#9ca3af;text-decoration:none;">Settings</a>
                <svg style="width:12px;height:12px;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>Mail</span>
            </nav>
        </div>
    </div>

    {{-- Demo mode notice --}}
    @if($isDemo ?? false)
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 18px;background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:10px;margin-bottom:20px;">
            <svg style="width:18px;height:18px;color:#d97706;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
            <div>
                <div style="font-weight:700;font-size:13px;color:#92400e;">Demo Mode — Read Only</div>
                <div style="font-size:13px;color:#78350f;margin-top:2px;">These settings are stored in the server's <code style="background:#fef3c7;padding:1px 5px;border-radius:4px;font-size:12px;">.env</code> file and cannot be changed during a demo session. The fields are shown empty for security. To configure integrations, log in as a real admin.</div>
            </div>
        </div>
    @endif

    {{-- Demo notice flash (e.g. if form was submitted anyway) --}}
    @if(session('demo_notice'))
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:10px;margin-bottom:20px;">
            <svg style="width:18px;height:18px;color:#d97706;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
            <div style="font-size:13px;color:#78350f;">{{ session('demo_notice') }}</div>
        </div>
    @endif

    {{-- Success alert --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
            style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-left:4px solid #16a34a;border-radius:10px;margin-bottom:20px;">
            <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <div style="flex:1;">
                <div style="font-weight:600;font-size:13px;color:#15803d;">Settings Saved</div>
                <div style="font-size:13px;color:#166534;">{{ session('success') }}</div>
            </div>
            <button @click="show=false" style="color:#86efac;background:none;border:none;cursor:pointer;font-size:18px;line-height:1;padding:0;">&times;</button>
        </div>
    @endif

    {{-- Error alert --}}
    @if($errors->any())
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#fef2f2;border:1px solid #fecaca;border-left:4px solid #dc2626;border-radius:10px;margin-bottom:20px;">
            <svg style="width:18px;height:18px;color:#dc2626;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div>
                <div style="font-weight:600;font-size:13px;color:#dc2626;margin-bottom:4px;">Please fix the following:</div>
                <ul style="margin:0;padding-left:16px;font-size:12px;color:#991b1b;">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <div x-data="{
        host: '{{ $current['host'] }}',
        port: '{{ $current['port'] }}',
        username: '{{ $current['username'] }}',
        encryption: '{{ $current['encryption'] }}',
        fromAddress: '{{ $current['from_address'] }}',
        fromName: '{{ $current['from_name'] }}',
        password: '',
        showPassword: false,
        testing: false, tested: false, testPassed: false, testMessage: '',
        async runTest() {
            this.testing = true; this.tested = false;
            try {
                const r = await fetch('{{ route('admin.settings.mail.test') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        mail_host: this.host, mail_port: this.port,
                        mail_username: this.username, mail_password: this.password,
                        mail_encryption: this.encryption,
                        mail_from_address: this.fromAddress, mail_from_name: this.fromName
                    })
                });
                const d = await r.json();
                this.testPassed = d.success; this.testMessage = d.message;
            } catch(e) { this.testPassed = false; this.testMessage = 'Request failed.'; }
            this.tested = true; this.testing = false;
        }
    }" style="max-width:700px;margin:0 auto;">

        {{-- Card --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.06);">

            {{-- Card header --}}
            <div style="background:linear-gradient(135deg,#1d4ed8 0%,#3b82f6 100%);padding:20px 24px;display:flex;align-items:center;gap:14px;">
                <div style="width:46px;height:46px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:22px;height:22px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div style="flex:1;">
                    <div style="color:white;font-weight:600;font-size:16px;">Mail Configuration</div>
                    <div style="color:rgba(255,255,255,0.75);font-size:13px;margin-top:2px;">SMTP settings for transactional emails and notifications</div>
                </div>
                @if($isConfigured)
                    <span style="background:rgba(255,255,255,0.2);color:white;font-size:12px;padding:4px 12px;border-radius:9999px;font-weight:500;border:1px solid rgba(255,255,255,0.3);flex-shrink:0;">
                        <svg style="width:12px;height:12px;display:inline;margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Configured
                    </span>
                @else
                    <span style="background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.85);font-size:12px;padding:4px 12px;border-radius:9999px;font-weight:500;border:1px solid rgba(255,255,255,0.25);flex-shrink:0;">
                        Not Configured
                    </span>
                @endif
            </div>

            {{-- Info bar --}}
            <div style="background:#eff6ff;border-bottom:1px solid #dbeafe;padding:10px 24px;display:flex;align-items:center;gap:8px;font-size:12.5px;color:#1d4ed8;">
                <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Credentials are stored in <code style="background:#dbeafe;padding:1px 5px;border-radius:4px;font-size:11px;">.env</code> only — never written to the database. Passwords are always masked.</span>
            </div>

            <form method="POST" action="{{ route('admin.settings.mail.save') }}" style="padding:24px;" class="space-y-0">
                @csrf

                {{-- SMTP Server section --}}
                <div style="margin-bottom:20px;">
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px;display:flex;align-items:center;gap:7px;">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                        SMTP Server
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 140px;gap:12px;margin-bottom:14px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Host</label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;">
                                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                </span>
                                <input type="text" name="mail_host" x-model="host" placeholder="smtp.mailtrap.io"
                                    style="width:100%;padding:9px 12px 9px 34px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-family:monospace;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                                    onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Port</label>
                            <input type="text" name="mail_port" x-model="port" placeholder="587"
                                style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-family:monospace;outline:none;box-sizing:border-box;transition:border-color 0.15s;text-align:center;"
                                onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                        </div>
                    </div>
                    <div style="margin-bottom:14px;">
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Username</label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;">
                                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </span>
                            <input type="text" name="mail_username" x-model="username"
                                style="width:100%;padding:9px 12px 9px 34px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-family:monospace;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                                onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                                Password
                                @if($current['password_set'])
                                    <span style="background:#d1fae5;color:#065f46;font-size:10px;font-weight:600;padding:2px 7px;border-radius:9999px;margin-left:6px;display:inline-flex;align-items:center;gap:3px;">
                                        <svg style="width:9px;height:9px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Set
                                    </span>
                                @endif
                            </label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;">
                                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </span>
                                <input :type="showPassword ? 'text' : 'password'" name="mail_password" x-model="password"
                                    placeholder="{{ $current['password_set'] ? '••••••••' : 'Enter password' }}"
                                    style="width:100%;padding:9px 38px 9px 34px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                                    onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <button type="button" @click="showPassword=!showPassword"
                                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;padding:2px;display:flex;">
                                    <svg x-show="!showPassword" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showPassword" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            @if($current['password_set'])
                                <p style="font-size:11px;color:#9ca3af;margin-top:4px;">Leave blank to keep current password</p>
                            @endif
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Encryption</label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;">
                                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 018 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                </span>
                                <select name="mail_encryption" x-model="encryption"
                                    style="width:100%;padding:9px 12px 9px 34px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;appearance:none;background-color:white;transition:border-color 0.15s;"
                                    onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                    <option value="tls">TLS (587)</option>
                                    <option value="ssl">SSL (465)</option>
                                    <option value="">None</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sender section --}}
                <div style="border-top:1px solid #f3f4f6;padding-top:20px;margin-bottom:20px;">
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px;display:flex;align-items:center;gap:7px;">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        Sender Identity
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">From Name</label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;">
                                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </span>
                                <input type="text" name="mail_from_name" x-model="fromName" placeholder="{{ config('app.name') }}"
                                    style="width:100%;padding:9px 12px 9px 34px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                                    onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            </div>
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">From Email</label>
                            <div style="position:relative;">
                                <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;">
                                    <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </span>
                                <input type="email" name="mail_from_address" x-model="fromAddress" placeholder="noreply@yourdomain.com"
                                    style="width:100%;padding:9px 12px 9px 34px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                                    onfocus="this.style.borderColor='#3b82f6';this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Test result --}}
                <div x-show="tested" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-1"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    :style="testPassed ? 'background:#f0fdf4;border-color:#bbf7d0;border-left-color:#16a34a;color:#15803d;' : 'background:#fef2f2;border-color:#fecaca;border-left-color:#dc2626;color:#dc2626;'"
                    style="padding:12px 16px;border-radius:8px;border:1px solid;border-left:4px solid;margin-bottom:16px;display:flex;align-items:flex-start;gap:10px;">
                    <svg x-show="testPassed" style="width:18px;height:18px;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    <svg x-show="!testPassed" style="width:18px;height:18px;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <div>
                        <div style="font-weight:600;font-size:13px;" x-text="testPassed ? 'Connection Successful' : 'Connection Failed'"></div>
                        <div style="font-size:12px;opacity:0.85;margin-top:2px;" x-text="testMessage"></div>
                    </div>
                </div>

                {{-- Footer --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:16px;border-top:1px solid #f3f4f6;">
                    <a href="{{ route('admin.settings.index') }}" style="font-size:13px;color:#6b7280;text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:color 0.15s;"
                        onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#6b7280'">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back to Settings
                    </a>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <button type="button" @click="runTest()" :disabled="testing"
                            style="display:inline-flex;align-items:center;gap:7px;padding:8px 18px;border:1px solid #1d4ed8;color:#1d4ed8;background:white;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.15s;"
                            :style="testing ? 'opacity:0.6;cursor:not-allowed;' : ''"
                            onmouseover="if(!this.disabled)this.style.background='#eff6ff'" onmouseout="this.style.background='white'">
                            <svg x-show="testing" style="width:14px;height:14px;animation:spin 1s linear infinite;" fill="none" viewBox="0 0 24 24"><circle style="opacity:0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path style="opacity:0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <svg x-show="!testing" style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8"/></svg>
                            <span x-text="testing ? 'Sending...' : 'Send Test Email'"></span>
                        </button>
                        <button type="submit"
                            style="display:inline-flex;align-items:center;gap:7px;padding:8px 20px;background:#1d4ed8;color:white;border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.15s;"
                            onmouseover="this.style.background='#1e40af'" onmouseout="this.style.background='#1d4ed8'">
                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@stop

@push('scripts')
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }</style>
@endpush
