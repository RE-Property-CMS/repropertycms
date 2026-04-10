@extends('admin.layouts.default')

@section('title', 'Stripe Settings')

@section('content')

    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-0">Stripe Settings</h5>
            <nav style="font-size:13px;color:#6b7280;margin-top:4px;display:flex;align-items:center;gap:6px;">
                <a href="{{ route('admin.settings.index') }}" style="color:#9ca3af;text-decoration:none;">Settings</a>
                <svg style="width:12px;height:12px;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>Stripe</span>
            </nav>
        </div>
    </div>

    @if($isDemo ?? false)
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 18px;background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:10px;margin-bottom:20px;">
            <svg style="width:18px;height:18px;color:#d97706;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
            <div>
                <div style="font-weight:700;font-size:13px;color:#92400e;">Demo Mode — Read Only</div>
                <div style="font-size:13px;color:#78350f;margin-top:2px;">These settings are stored in the server's <code style="background:#fef3c7;padding:1px 5px;border-radius:4px;font-size:12px;">.env</code> file and cannot be changed during a demo session. The fields are shown empty for security. To configure integrations, log in as a real admin.</div>
            </div>
        </div>
    @endif

    @if(session('demo_notice'))
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:10px;margin-bottom:20px;">
            <svg style="width:18px;height:18px;color:#d97706;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
            <div style="font-size:13px;color:#78350f;">{{ session('demo_notice') }}</div>
        </div>
    @endif

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
        secret: '',
        showSecret: false,
        showWebhook: false,
        testing: false, tested: false, testPassed: false, testMessage: '',
        async runTest() {
            this.testing = true; this.tested = false;
            try {
                const r = await fetch('{{ route('admin.settings.stripe.test') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ stripe_secret: this.secret })
                });
                const d = await r.json();
                this.testPassed = d.success; this.testMessage = d.message;
            } catch(e) { this.testPassed = false; this.testMessage = 'Request failed.'; }
            this.tested = true; this.testing = false;
        }
    }" style="max-width:700px;margin:0 auto;">

        <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.06);">

            {{-- Card header --}}
            <div style="background:linear-gradient(135deg,#635bff 0%,#7c73ff 100%);padding:20px 24px;display:flex;align-items:center;gap:14px;">
                <div style="width:46px;height:46px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:22px;height:22px;color:white;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/>
                    </svg>
                </div>
                <div style="flex:1;">
                    <div style="color:white;font-weight:600;font-size:16px;">Stripe Payment Gateway</div>
                    <div style="color:rgba(255,255,255,0.75);font-size:13px;margin-top:2px;">API keys for agent subscriptions and payment processing</div>
                </div>
                @if($isConfigured)
                    <span style="background:rgba(255,255,255,0.2);color:white;font-size:12px;padding:4px 12px;border-radius:9999px;font-weight:500;border:1px solid rgba(255,255,255,0.3);flex-shrink:0;">
                        <svg style="width:12px;height:12px;display:inline;margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Configured
                    </span>
                @else
                    <span style="background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.85);font-size:12px;padding:4px 12px;border-radius:9999px;font-weight:500;border:1px solid rgba(255,255,255,0.25);flex-shrink:0;">Not Configured</span>
                @endif
            </div>

            {{-- Info bar --}}
            <div style="background:#f5f3ff;border-bottom:1px solid #ede9fe;padding:10px 24px;display:flex;align-items:center;gap:8px;font-size:12.5px;color:#5b21b6;">
                <svg style="width:14px;height:14px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Keys are stored in <code style="background:#ede9fe;padding:1px 5px;border-radius:4px;font-size:11px;">.env</code> only — never written to the database. Secret keys are always masked.</span>
            </div>

            <form method="POST" action="{{ route('admin.settings.stripe.save') }}" style="padding:24px;">
                @csrf

                {{-- API Keys section --}}
                <div style="margin-bottom:20px;">
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px;display:flex;align-items:center;gap:7px;">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        API Keys
                    </div>

                    {{-- Publishable Key --}}
                    <div style="margin-bottom:14px;">
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                            Publishable Key
                            <span style="font-size:11px;font-weight:400;color:#9ca3af;margin-left:6px;">Safe to expose in the browser</span>
                        </label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;">
                                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                            </span>
                            <input type="text" name="stripe_key" value="{{ $current['key'] }}" placeholder="pk_live_... or pk_test_..."
                                style="width:100%;padding:9px 12px 9px 34px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-family:monospace;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                                onfocus="this.style.borderColor='#635bff';this.style.boxShadow='0 0 0 3px rgba(99,91,255,0.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                        </div>
                    </div>

                    {{-- Secret Key --}}
                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                            Secret Key
                            @if($current['secret_set'])
                                <span style="background:#d1fae5;color:#065f46;font-size:10px;font-weight:600;padding:2px 7px;border-radius:9999px;margin-left:6px;display:inline-flex;align-items:center;gap:3px;">
                                    <svg style="width:9px;height:9px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Set
                                </span>
                            @endif
                        </label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;">
                                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </span>
                            <input :type="showSecret ? 'text' : 'password'" name="stripe_secret" x-model="secret"
                                placeholder="{{ $current['secret_set'] ? '••••••••  (leave blank to keep current)' : 'sk_live_... or sk_test_...' }}"
                                style="width:100%;padding:9px 38px 9px 34px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-family:monospace;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                                onfocus="this.style.borderColor='#635bff';this.style.boxShadow='0 0 0 3px rgba(99,91,255,0.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <button type="button" @click="showSecret=!showSecret"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;padding:2px;display:flex;">
                                <svg x-show="!showSecret" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showSecret" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        @if($current['secret_set'])
                            <p style="font-size:11px;color:#9ca3af;margin-top:4px;">Leave blank to keep the current secret key</p>
                        @endif
                    </div>
                </div>

                {{-- Webhook section --}}
                <div style="border-top:1px solid #f3f4f6;padding-top:20px;margin-bottom:20px;">
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px;display:flex;align-items:center;gap:7px;">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Webhook
                    </div>
                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                            Webhook Secret
                            @if($current['webhook_set'])
                                <span style="background:#d1fae5;color:#065f46;font-size:10px;font-weight:600;padding:2px 7px;border-radius:9999px;margin-left:6px;display:inline-flex;align-items:center;gap:3px;">
                                    <svg style="width:9px;height:9px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Set
                                </span>
                            @endif
                        </label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;">
                                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </span>
                            <input :type="showWebhook ? 'text' : 'password'" name="stripe_webhook"
                                placeholder="{{ $current['webhook_set'] ? '••••••••  (leave blank to keep current)' : 'whsec_...' }}"
                                style="width:100%;padding:9px 38px 9px 34px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-family:monospace;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                                onfocus="this.style.borderColor='#635bff';this.style.boxShadow='0 0 0 3px rgba(99,91,255,0.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <button type="button" @click="showWebhook=!showWebhook"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;padding:2px;display:flex;">
                                <svg x-show="!showWebhook" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showWebhook" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        @if($current['webhook_set'])
                            <p style="font-size:11px;color:#9ca3af;margin-top:4px;">Leave blank to keep the current webhook secret</p>
                        @else
                            <p style="font-size:11px;color:#9ca3af;margin-top:4px;">Found in Stripe Dashboard → Developers → Webhooks</p>
                        @endif
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
                        <button type="button" @click="runTest()" :disabled="testing || !secret"
                            :style="{
                                display:'inline-flex','align-items':'center','justify-content':'center',
                                gap:'7px',padding:'10px 30px','min-width':'140px','white-space':'nowrap',
                                border:'1px solid #635bff',color:'#635bff',background:'white',
                                'border-radius':'8px','font-size':'13px','font-weight':'500',
                                cursor:(testing||!secret)?'not-allowed':'pointer',
                                transition:'background 0.15s','box-sizing':'border-box',
                                opacity:(testing||!secret)?'0.5':'1'
                            }"
                            onmouseover="if(!this.disabled)this.style.background='#f5f3ff'" onmouseout="this.style.background='white'">
                            <svg x-show="testing" style="width:14px;height:14px;animation:spin 1s linear infinite;" fill="none" viewBox="0 0 24 24"><circle style="opacity:0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path style="opacity:0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <svg x-show="!testing" style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="testing ? 'Testing...' : 'Test Keys'"></span>
                        </button>
                        <button type="submit"
                            style="display:inline-flex;align-items:center;gap:7px;padding:10px 24px;white-space:nowrap;background:#635bff;color:white;border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.15s;"
                            onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#635bff'">
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
