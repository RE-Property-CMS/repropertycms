@extends('admin.layouts.default')

@section('title', 'Storage Settings')

@section('content')

    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-0">Storage Settings</h5>
            <nav style="font-size:13px;color:#6b7280;margin-top:4px;display:flex;align-items:center;gap:6px;">
                <a href="{{ route('admin.settings.index') }}" style="color:#9ca3af;text-decoration:none;">Settings</a>
                <svg style="width:12px;height:12px;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>Storage</span>
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
        driver: '{{ $current['driver'] }}',
        awsKey: '{{ $current['aws_key'] }}',
        awsSecret: '',
        awsRegion: '{{ $current['aws_region'] }}',
        awsBucket: '{{ $current['aws_bucket'] }}',
        showAwsSecret: false,
        testing: false, tested: false, testPassed: false, testMessage: '',
        async runTest() {
            this.testing = true; this.tested = false;
            try {
                const r = await fetch('{{ route('admin.settings.storage.test') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        driver: this.driver, aws_key: this.awsKey,
                        aws_secret: this.awsSecret, aws_region: this.awsRegion,
                        aws_bucket: this.awsBucket
                    })
                });
                const d = await r.json();
                this.testPassed = d.success; this.testMessage = d.message;
            } catch(e) { this.testPassed = false; this.testMessage = 'Request failed.'; }
            this.tested = true; this.testing = false;
        }
    }" style="max-width:700px;margin:0 auto;">

        <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.06);">

            {{-- Card header --}}
            <div style="background:linear-gradient(135deg,#c2410c 0%,#f97316 100%);padding:20px 24px;display:flex;align-items:center;gap:14px;">
                <div style="width:46px;height:46px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:22px;height:22px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                </div>
                <div style="flex:1;">
                    <div style="color:white;font-weight:600;font-size:16px;">File Storage</div>
                    <div style="color:rgba(255,255,255,0.75);font-size:13px;margin-top:2px;">Store uploaded files locally or in AWS S3 cloud storage</div>
                </div>
                @if($isConfigured)
                    <span style="background:rgba(255,255,255,0.2);color:white;font-size:12px;padding:4px 12px;border-radius:9999px;font-weight:500;border:1px solid rgba(255,255,255,0.3);flex-shrink:0;">
                        <svg style="width:12px;height:12px;display:inline;margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Configured
                    </span>
                @else
                    <span style="background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.85);font-size:12px;padding:4px 12px;border-radius:9999px;font-weight:500;border:1px solid rgba(255,255,255,0.25);flex-shrink:0;">Not Configured</span>
                @endif
            </div>

            <form method="POST" action="{{ route('admin.settings.storage.save') }}" style="padding:24px;">
                @csrf

                {{-- Driver selector --}}
                <div style="margin-bottom:20px;">
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px;display:flex;align-items:center;gap:7px;">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                        Storage Driver
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">

                        {{-- Local --}}
                        <label style="cursor:pointer;display:block;"
                            :style="driver==='local' ? 'border:2px solid #ea580c;background:#fff7ed;border-radius:10px;' : 'border:2px solid #e5e7eb;background:white;border-radius:10px;'"
                            style="padding:14px 16px;transition:all 0.15s;">
                            <input type="radio" name="driver" value="local" x-model="driver" style="display:none;">
                            <div style="display:flex;align-items:flex-start;gap:12px;">
                                <div :style="driver==='local' ? 'background:#ea580c;' : 'background:#e5e7eb;'"
                                    style="width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background 0.15s;">
                                    <svg style="width:18px;height:18px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div :style="driver==='local' ? 'color:#c2410c;' : 'color:#374151;'" style="font-weight:600;font-size:13px;transition:color 0.15s;">Local Storage</div>
                                    <div style="font-size:12px;color:#6b7280;margin-top:2px;">Files stored on your server disk</div>
                                    <div x-show="driver==='local'" style="margin-top:6px;font-size:11px;color:#ea580c;display:flex;align-items:center;gap:4px;">
                                        <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Selected
                                    </div>
                                </div>
                            </div>
                        </label>

                        {{-- S3 --}}
                        <label style="cursor:pointer;display:block;"
                            :style="driver==='s3' ? 'border:2px solid #ea580c;background:#fff7ed;border-radius:10px;' : 'border:2px solid #e5e7eb;background:white;border-radius:10px;'"
                            style="padding:14px 16px;transition:all 0.15s;">
                            <input type="radio" name="driver" value="s3" x-model="driver" style="display:none;">
                            <div style="display:flex;align-items:flex-start;gap:12px;">
                                <div :style="driver==='s3' ? 'background:#ea580c;' : 'background:#e5e7eb;'"
                                    style="width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background 0.15s;">
                                    <svg style="width:18px;height:18px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div :style="driver==='s3' ? 'color:#c2410c;' : 'color:#374151;'" style="font-weight:600;font-size:13px;transition:color 0.15s;">AWS S3</div>
                                    <div style="font-size:12px;color:#6b7280;margin-top:2px;">Scalable cloud object storage</div>
                                    <div x-show="driver==='s3'" style="margin-top:6px;font-size:11px;color:#ea580c;display:flex;align-items:center;gap:4px;">
                                        <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Selected
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- S3 fields --}}
                <div x-show="driver === 's3'" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    style="border-top:1px solid #f3f4f6;padding-top:20px;margin-bottom:20px;">
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px;display:flex;align-items:center;gap:7px;">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        AWS Credentials
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Access Key ID</label>
                            <input type="text" name="aws_key" x-model="awsKey" placeholder="AKIAIOSFODNN7EXAMPLE"
                                style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-family:monospace;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                                onfocus="this.style.borderColor='#ea580c';this.style.boxShadow='0 0 0 3px rgba(234,88,12,0.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                                Secret Access Key
                                @if($current['aws_secret_set'])
                                    <span style="background:#d1fae5;color:#065f46;font-size:10px;font-weight:600;padding:2px 7px;border-radius:9999px;margin-left:6px;display:inline-flex;align-items:center;gap:3px;">
                                        <svg style="width:9px;height:9px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Set
                                    </span>
                                @endif
                            </label>
                            <div style="position:relative;">
                                <input :type="showAwsSecret ? 'text' : 'password'" name="aws_secret" x-model="awsSecret"
                                    placeholder="{{ $current['aws_secret_set'] ? '••••••••  (leave blank to keep)' : 'Enter secret key' }}"
                                    style="width:100%;padding:9px 38px 9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-family:monospace;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                                    onfocus="this.style.borderColor='#ea580c';this.style.boxShadow='0 0 0 3px rgba(234,88,12,0.1)'"
                                    onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                                <button type="button" @click="showAwsSecret=!showAwsSecret"
                                    style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;padding:2px;display:flex;">
                                    <svg x-show="!showAwsSecret" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <svg x-show="showAwsSecret" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                </button>
                            </div>
                            @if($current['aws_secret_set'])
                                <p style="font-size:11px;color:#9ca3af;margin-top:4px;">Leave blank to keep current secret</p>
                            @endif
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Region</label>
                            <input type="text" name="aws_region" x-model="awsRegion" placeholder="us-east-1"
                                style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-family:monospace;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                                onfocus="this.style.borderColor='#ea580c';this.style.boxShadow='0 0 0 3px rgba(234,88,12,0.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                        </div>
                        <div>
                            <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">Bucket Name</label>
                            <input type="text" name="aws_bucket" x-model="awsBucket" placeholder="my-property-bucket"
                                style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-family:monospace;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                                onfocus="this.style.borderColor='#ea580c';this.style.boxShadow='0 0 0 3px rgba(234,88,12,0.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
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
                    <a href="{{ route('admin.settings.index') }}" style="font-size:13px;color:#6b7280;text-decoration:none;display:inline-flex;align-items:center;gap:6px;"
                        onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#6b7280'">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back to Settings
                    </a>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <button type="button" @click="runTest()" :disabled="testing"
                            style="display:inline-flex;align-items:center;gap:7px;padding:8px 18px;border:1px solid #ea580c;color:#ea580c;background:white;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.15s;"
                            :style="testing ? 'opacity:0.6;cursor:not-allowed;' : ''"
                            onmouseover="if(!this.disabled)this.style.background='#fff7ed'" onmouseout="this.style.background='white'">
                            <svg x-show="testing" style="width:14px;height:14px;animation:spin 1s linear infinite;" fill="none" viewBox="0 0 24 24"><circle style="opacity:0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path style="opacity:0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            <svg x-show="!testing" style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="testing ? 'Testing...' : 'Test Storage'"></span>
                        </button>
                        <button type="submit"
                            style="display:inline-flex;align-items:center;gap:7px;padding:8px 20px;background:#ea580c;color:white;border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.15s;"
                            onmouseover="this.style.background='#c2410c'" onmouseout="this.style.background='#ea580c'">
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
