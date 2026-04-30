@extends('admin.layouts.default')

@section('title', 'Google Maps Settings')

@section('content')

    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-0">Google Maps Settings</h5>
            <nav style="font-size:13px;color:#6b7280;margin-top:4px;display:flex;align-items:center;gap:6px;">
                <a href="{{ route('admin.settings.index') }}" style="color:#9ca3af;text-decoration:none;">Settings</a>
                <svg style="width:12px;height:12px;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>Google Maps</span>
            </nav>
        </div>
    </div>

    @if($isDemo ?? false)
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 18px;background:#fffbeb;border:1px solid #fde68a;border-left:4px solid #f59e0b;border-radius:10px;margin-bottom:20px;">
            <svg style="width:18px;height:18px;color:#d97706;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
            <div>
                <div style="font-weight:700;font-size:13px;color:#92400e;">Demo Mode — Read Only</div>
                <div style="font-size:13px;color:#78350f;margin-top:2px;">These settings are stored in the server's <code style="background:#fef3c7;padding:1px 5px;border-radius:4px;font-size:12px;">.env</code> file and cannot be changed during a demo session.</div>
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
            <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <div style="flex:1;">
                <div style="font-weight:600;font-size:13px;color:#15803d;">Settings Saved</div>
                <div style="font-size:13px;color:#166534;">{{ session('success') }}</div>
            </div>
            <button @click="show=false" style="color:#86efac;background:none;border:none;cursor:pointer;font-size:18px;line-height:1;padding:0;">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#fef2f2;border:1px solid #fecaca;border-left:4px solid #dc2626;border-radius:10px;margin-bottom:20px;">
            <svg style="width:18px;height:18px;color:#dc2626;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <div>
                <div style="font-weight:600;font-size:13px;color:#dc2626;margin-bottom:4px;">Please fix the following:</div>
                <ul style="margin:0;padding-left:16px;font-size:12px;color:#991b1b;">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        </div>
    @endif

    <div x-data="{ showKey: false }" style="max-width:700px;margin:0 auto;">

        <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,0.06);">

            {{-- Card header --}}
            <div style="background:linear-gradient(135deg,#1d4ed8 0%,#3b82f6 100%);padding:20px 24px;display:flex;align-items:center;gap:14px;">
                <div style="width:46px;height:46px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:22px;height:22px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div style="flex:1;">
                    <div style="color:white;font-weight:600;font-size:16px;">Google Maps</div>
                    <div style="color:rgba(255,255,255,0.75);font-size:13px;margin-top:2px;">Interactive maps, address geocoding &amp; nearby places on property listings</div>
                </div>
                @if($current['api_key_set'])
                    <span style="background:rgba(255,255,255,0.2);color:white;font-size:12px;padding:4px 12px;border-radius:9999px;font-weight:500;border:1px solid rgba(255,255,255,0.3);flex-shrink:0;">
                        <svg style="width:12px;height:12px;display:inline;margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Configured
                    </span>
                @else
                    <span style="background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.85);font-size:12px;padding:4px 12px;border-radius:9999px;font-weight:500;border:1px solid rgba(255,255,255,0.25);flex-shrink:0;">Not Configured</span>
                @endif
            </div>

            {{-- How-to info panel --}}
            <div style="background:#eff6ff;border-bottom:1px solid #dbeafe;padding:14px 24px;">
                <div style="display:flex;align-items:flex-start;gap:10px;">
                    <svg style="width:15px;height:15px;color:#2563eb;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <div style="font-size:12.5px;font-weight:600;color:#1d4ed8;margin-bottom:6px;">How to get your Google Maps API key:</div>
                        <ol style="margin:0;padding-left:16px;font-size:12px;color:#1e40af;line-height:1.8;">
                            <li>Go to <code style="background:#dbeafe;padding:1px 5px;border-radius:4px;">console.cloud.google.com</code> and sign in</li>
                            <li>Create or select a project, then go to <strong>APIs &amp; Services → Library</strong></li>
                            <li>Enable the <strong>Maps JavaScript API</strong></li>
                            <li>Go to <strong>APIs &amp; Services → Credentials</strong> and click <strong>+ Create Credentials → API Key</strong></li>
                            <li>Copy the key and paste it below</li>
                        </ol>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.settings.maps.save') }}" style="padding:24px;">
                @csrf

                <div style="margin-bottom:20px;">
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:14px;display:flex;align-items:center;gap:7px;">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                        API Key
                    </div>

                    <div>
                        <label style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:6px;">
                            Google Maps API Key
                            @if($current['api_key_set'])
                                <span style="background:#dbeafe;color:#1e40af;font-size:10px;font-weight:600;padding:2px 7px;border-radius:9999px;margin-left:6px;display:inline-flex;align-items:center;gap:3px;">
                                    <svg style="width:9px;height:9px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>Set
                                </span>
                            @endif
                        </label>
                        <div style="position:relative;">
                            <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);color:#9ca3af;">
                                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </span>
                            <input :type="showKey ? 'text' : 'password'" name="maps_api_key"
                                placeholder="{{ $current['api_key_set'] ? '••••••••  (leave blank to keep current)' : 'AIzaSy...' }}"
                                style="width:100%;padding:9px 38px 9px 34px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-family:monospace;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                                onfocus="this.style.borderColor='#2563eb';this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)'"
                                onblur="this.style.borderColor='#d1d5db';this.style.boxShadow='none'">
                            <button type="button" @click="showKey=!showKey"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;padding:2px;display:flex;">
                                <svg x-show="!showKey" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showKey" style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        @if($current['api_key_set'])
                            <p style="font-size:11px;color:#9ca3af;margin-top:4px;">Leave blank to keep the current key. Stored in .env as <span style="font-family:monospace;">GOOGLE_MAP_API_KEY</span>.</p>
                        @else
                            <p style="font-size:11px;color:#9ca3af;margin-top:4px;">Stored in .env as <span style="font-family:monospace;">GOOGLE_MAP_API_KEY</span>. Powers property maps, geocoding, and nearby places.</p>
                        @endif
                    </div>
                </div>

                {{-- Footer --}}
                <div style="display:flex;align-items:center;justify-content:space-between;padding-top:16px;border-top:1px solid #f3f4f6;">
                    <a href="{{ route('admin.settings.index') }}" style="font-size:13px;color:#6b7280;text-decoration:none;display:inline-flex;align-items:center;gap:6px;"
                        onmouseover="this.style.color='#374151'" onmouseout="this.style.color='#6b7280'">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back to Settings
                    </a>
                    <button type="submit"
                        style="display:inline-flex;align-items:center;gap:7px;padding:8px 20px;background:#2563eb;color:white;border:none;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.15s;"
                        onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

@stop

@push('scripts')
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
