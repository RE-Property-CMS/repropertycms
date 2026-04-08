@extends('admin.layouts.default')

@section('title', 'Integration Settings')

@section('content')

    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-0">Integration Settings</h5>
            <p class="text-sm text-gray-500 mb-0 mt-1">Manage credentials for external services. All keys are stored in <code>.env</code> — never in the database.</p>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
            style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-left:4px solid #16a34a;border-radius:10px;margin-bottom:20px;">
            <svg style="width:18px;height:18px;color:#16a34a;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <div style="flex:1;">
                <div style="font-weight:600;font-size:13px;color:#15803d;">Saved Successfully</div>
                <div style="font-size:13px;color:#166534;">{{ session('success') }}</div>
            </div>
            <button @click="show=false" style="color:#86efac;background:none;border:none;cursor:pointer;font-size:18px;line-height:1;padding:0;">&times;</button>
        </div>
    @endif

    @if(session('demo_success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition
            style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#eff6ff;border:1px solid #bfdbfe;border-left:4px solid #2563eb;border-radius:10px;margin-bottom:20px;">
            <svg style="width:18px;height:18px;color:#2563eb;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <div style="flex:1;">
                <div style="font-weight:600;font-size:13px;color:#1d4ed8;">Demo Data</div>
                <div style="font-size:13px;color:#1e40af;">{{ session('demo_success') }}</div>
            </div>
            <button @click="show=false" style="color:#93c5fd;background:none;border:none;cursor:pointer;font-size:18px;line-height:1;padding:0;">&times;</button>
        </div>
    @endif

    @if(session('demo_error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition
            style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#fef2f2;border:1px solid #fecaca;border-left:4px solid #dc2626;border-radius:10px;margin-bottom:20px;">
            <svg style="width:18px;height:18px;color:#dc2626;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            <div style="flex:1;">
                <div style="font-weight:600;font-size:13px;color:#991b1b;">Demo Data</div>
                <div style="font-size:13px;color:#7f1d1d;">{{ session('demo_error') }}</div>
            </div>
            <button @click="show=false" style="color:#fca5a5;background:none;border:none;cursor:pointer;font-size:18px;line-height:1;padding:0;">&times;</button>
        </div>
    @endif

    {{-- Status overview bar --}}
    @php
        $configured = collect($statuses)->filter()->count();
        $total = collect($statuses)->count();
    @endphp
    <div style="display:flex;align-items:center;gap:16px;padding:14px 18px;background:white;border:1px solid #e5e7eb;border-radius:10px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="display:flex;align-items:center;gap:10px;flex:1;">
            <div style="width:36px;height:36px;background:{{ $configured === $total ? '#d1fae5' : '#fef3c7' }};border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                @if($configured === $total)
                    <svg style="width:18px;height:18px;color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                @else
                    <svg style="width:18px;height:18px;color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                @endif
            </div>
            <div>
                <div style="font-size:13px;font-weight:600;color:#111827;">{{ $configured }} of {{ $total }} integrations configured</div>
                <div style="font-size:12px;color:#6b7280;">{{ $total - $configured > 0 ? ($total - $configured).' integration'.($total - $configured > 1 ? 's' : '').' still need setup' : 'All integrations are active' }}</div>
            </div>
        </div>
        <div style="display:flex;gap:6px;">
            @foreach($statuses as $key => $status)
                <div title="{{ ucfirst($key) }}: {{ $status ? 'Configured' : 'Not configured' }}"
                    style="width:8px;height:8px;border-radius:50%;background:{{ $status ? '#10b981' : '#e5e7eb' }};"></div>
            @endforeach
        </div>
    </div>

    {{-- Integration cards grid --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">

        {{-- Mail --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);transition:box-shadow 0.2s;"
            onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.05)'">
            <div style="padding:20px 20px 0;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:44px;height:44px;background:{{ $statuses['mail'] ? '#eff6ff' : '#fef2f2' }};border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:20px;height:20px;color:{{ $statuses['mail'] ? '#2563eb' : '#dc2626' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:14px;color:#111827;">Mail (SMTP)</div>
                            <div style="font-size:12px;color:#6b7280;margin-top:1px;">Email notifications & password resets</div>
                        </div>
                    </div>
                    @if($statuses['mail'])
                        <span style="background:#d1fae5;color:#065f46;font-size:11px;font-weight:500;padding:3px 10px;border-radius:9999px;flex-shrink:0;">Configured</span>
                    @else
                        <span style="background:#fee2e2;color:#991b1b;font-size:11px;font-weight:500;padding:3px 10px;border-radius:9999px;flex-shrink:0;">Not Set</span>
                    @endif
                </div>
            </div>
            <div style="padding:0 20px 20px;">
                <a href="{{ route('admin.settings.mail') }}"
                    style="display:flex;align-items:center;justify-content:center;gap:6px;width:100%;padding:8px 0;background:{{ $statuses['mail'] ? 'white' : '#2563eb' }};color:{{ $statuses['mail'] ? '#2563eb' : 'white' }};border:1px solid {{ $statuses['mail'] ? '#2563eb' : '#2563eb' }};border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;transition:all 0.15s;"
                    onmouseover="this.style.background='{{ $statuses['mail'] ? '#eff6ff' : '#1d4ed8' }}'" onmouseout="this.style.background='{{ $statuses['mail'] ? 'white' : '#2563eb' }}'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statuses['mail'] ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M12 4v16m8-8H4' }}"/>
                    </svg>
                    {{ $statuses['mail'] ? 'Update Settings' : 'Configure Now' }}
                </a>
            </div>
        </div>

        {{-- Stripe --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);transition:box-shadow 0.2s;"
            onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.05)'">
            <div style="padding:20px 20px 0;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:44px;height:44px;background:{{ $statuses['stripe'] ? '#f5f3ff' : '#fef2f2' }};border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:20px;height:20px;color:{{ $statuses['stripe'] ? '#7c3aed' : '#dc2626' }};" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:14px;color:#111827;">Stripe Payments</div>
                            <div style="font-size:12px;color:#6b7280;margin-top:1px;">Agent subscriptions & billing</div>
                        </div>
                    </div>
                    @if($statuses['stripe'])
                        <span style="background:#d1fae5;color:#065f46;font-size:11px;font-weight:500;padding:3px 10px;border-radius:9999px;flex-shrink:0;">Configured</span>
                    @else
                        <span style="background:#fee2e2;color:#991b1b;font-size:11px;font-weight:500;padding:3px 10px;border-radius:9999px;flex-shrink:0;">Not Set</span>
                    @endif
                </div>
            </div>
            <div style="padding:0 20px 20px;">
                <a href="{{ route('admin.settings.stripe') }}"
                    style="display:flex;align-items:center;justify-content:center;gap:6px;width:100%;padding:8px 0;background:{{ $statuses['stripe'] ? 'white' : '#7c3aed' }};color:{{ $statuses['stripe'] ? '#7c3aed' : 'white' }};border:1px solid #7c3aed;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;transition:all 0.15s;"
                    onmouseover="this.style.background='{{ $statuses['stripe'] ? '#f5f3ff' : '#6d28d9' }}'" onmouseout="this.style.background='{{ $statuses['stripe'] ? 'white' : '#7c3aed' }}'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statuses['stripe'] ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M12 4v16m8-8H4' }}"/>
                    </svg>
                    {{ $statuses['stripe'] ? 'Update Settings' : 'Configure Now' }}
                </a>
            </div>
        </div>

        {{-- Storage --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);transition:box-shadow 0.2s;"
            onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.05)'">
            <div style="padding:20px 20px 0;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:44px;height:44px;background:{{ $statuses['storage'] ? '#fff7ed' : '#fef2f2' }};border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:20px;height:20px;color:{{ $statuses['storage'] ? '#ea580c' : '#dc2626' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:14px;color:#111827;">File Storage</div>
                            <div style="font-size:12px;color:#6b7280;margin-top:1px;">Local disk or AWS S3 cloud storage</div>
                        </div>
                    </div>
                    @if($statuses['storage'])
                        <span style="background:#d1fae5;color:#065f46;font-size:11px;font-weight:500;padding:3px 10px;border-radius:9999px;flex-shrink:0;">Configured</span>
                    @else
                        <span style="background:#fee2e2;color:#991b1b;font-size:11px;font-weight:500;padding:3px 10px;border-radius:9999px;flex-shrink:0;">Not Set</span>
                    @endif
                </div>
            </div>
            <div style="padding:0 20px 20px;">
                <a href="{{ route('admin.settings.storage') }}"
                    style="display:flex;align-items:center;justify-content:center;gap:6px;width:100%;padding:8px 0;background:{{ $statuses['storage'] ? 'white' : '#ea580c' }};color:{{ $statuses['storage'] ? '#ea580c' : 'white' }};border:1px solid #ea580c;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;transition:all 0.15s;"
                    onmouseover="this.style.background='{{ $statuses['storage'] ? '#fff7ed' : '#c2410c' }}'" onmouseout="this.style.background='{{ $statuses['storage'] ? 'white' : '#ea580c' }}'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statuses['storage'] ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M12 4v16m8-8H4' }}"/>
                    </svg>
                    {{ $statuses['storage'] ? 'Update Settings' : 'Configure Now' }}
                </a>
            </div>
        </div>

        {{-- reCAPTCHA --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);transition:box-shadow 0.2s;"
            onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.05)'">
            <div style="padding:20px 20px 0;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:44px;height:44px;background:{{ $statuses['captcha'] ? '#f0fdf4' : '#fef2f2' }};border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:20px;height:20px;color:{{ $statuses['captcha'] ? '#16a34a' : '#dc2626' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:14px;color:#111827;">reCAPTCHA</div>
                            <div style="font-size:12px;color:#6b7280;margin-top:1px;">Bot protection for registration forms</div>
                        </div>
                    </div>
                    @if($statuses['captcha'])
                        <span style="background:#d1fae5;color:#065f46;font-size:11px;font-weight:500;padding:3px 10px;border-radius:9999px;flex-shrink:0;">Configured</span>
                    @else
                        <span style="background:#fee2e2;color:#991b1b;font-size:11px;font-weight:500;padding:3px 10px;border-radius:9999px;flex-shrink:0;">Not Set</span>
                    @endif
                </div>
            </div>
            <div style="padding:0 20px 20px;">
                <a href="{{ route('admin.settings.captcha') }}"
                    style="display:flex;align-items:center;justify-content:center;gap:6px;width:100%;padding:8px 0;background:{{ $statuses['captcha'] ? 'white' : '#16a34a' }};color:{{ $statuses['captcha'] ? '#16a34a' : 'white' }};border:1px solid #16a34a;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;transition:all 0.15s;"
                    onmouseover="this.style.background='{{ $statuses['captcha'] ? '#f0fdf4' : '#15803d' }}'" onmouseout="this.style.background='{{ $statuses['captcha'] ? 'white' : '#16a34a' }}'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $statuses['captcha'] ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M12 4v16m8-8H4' }}"/>
                    </svg>
                    {{ $statuses['captcha'] ? 'Update Settings' : 'Configure Now' }}
                </a>
            </div>
        </div>

        {{-- Brand & Appearance --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);transition:box-shadow 0.2s;grid-column:1/-1;"
            onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.05)'">
            <div style="padding:20px;display:flex;align-items:center;gap:16px;">
                <div style="width:44px;height:44px;background:#f5f3ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:20px;height:20px;color:#7c3aed;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                </div>
                <div style="flex:1;">
                    <div style="font-weight:600;font-size:14px;color:#111827;">Brand & Appearance</div>
                    <div style="font-size:12px;color:#6b7280;margin-top:1px;">Colors, fonts, logo, and visual identity — applied globally across all pages</div>
                </div>
                <span style="background:#ede9fe;color:#6d28d9;font-size:11px;font-weight:500;padding:3px 10px;border-radius:9999px;flex-shrink:0;">Always Active</span>
                <a href="{{ route('admin.settings.brand') }}"
                    style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:white;color:#7c3aed;border:1px solid #7c3aed;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;flex-shrink:0;transition:background 0.15s;"
                    onmouseover="this.style.background='#f5f3ff'" onmouseout="this.style.background='white'">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Customize
                </a>
            </div>
        </div>

        {{-- Demo Data --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.05);transition:box-shadow 0.2s;grid-column:1/-1;"
            onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.05)'">
            <div style="padding:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                <div style="width:44px;height:44px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:20px;height:20px;color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:200px;">
                    <div style="font-weight:600;font-size:14px;color:#111827;">Demo Data</div>
                    <div style="font-size:12px;color:#6b7280;margin-top:1px;">Populate the site with a sample agent and 8 luxury listings to preview the platform. Listings are created unpublished — subscription required to go live.</div>

                    @if($demoExists)
                    <div style="display:inline-flex;align-items:center;gap:16px;margin-top:12px;padding:10px 14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;flex-wrap:wrap;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;">Email</span>
                            <code style="font-size:12px;color:#1e40af;background:#eff6ff;padding:2px 8px;border-radius:4px;user-select:all;">demo.agent@demo.reproperty.local</code>
                        </div>
                        <div style="width:1px;height:16px;background:#e2e8f0;flex-shrink:0;"></div>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#94a3b8;">Password</span>
                            <code style="font-size:12px;color:#1e40af;background:#eff6ff;padding:2px 8px;border-radius:4px;user-select:all;">Demo@12345!</code>
                        </div>
                        <div style="width:1px;height:16px;background:#e2e8f0;flex-shrink:0;"></div>
                        <span style="font-size:11px;background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:9999px;font-weight:500;">Active</span>
                    </div>
                    @endif
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                    <form method="POST" action="{{ route('admin.demo.seed') }}" style="margin:0;"
                        onsubmit="return demoSubmit(event, 'This will create a demo agent with 8 high-end luxury listings (all unpublished). Continue?', 'Seeding demo data\u2026', 'Creating listings, images and amenities — this may take a few seconds.', '#2563eb')">
                        @csrf
                        <button type="submit" id="btn-seed"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:white;color:#2563eb;border:1px solid #2563eb;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.15s;"
                            onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='white'">
                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Seed Demo
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.demo.reset') }}" style="margin:0;"
                        onsubmit="return demoSubmit(event, 'This will permanently delete the demo agent and all their listings. Continue?', 'Resetting demo data\u2026', 'Removing the demo agent and all linked properties.', '#dc2626')">
                        @csrf
                        <button type="submit" id="btn-reset"
                            style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;background:white;color:#dc2626;border:1px solid #dc2626;border-radius:8px;font-size:13px;font-weight:500;cursor:pointer;transition:background 0.15s;"
                            onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='white'">
                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset Demo
                        </button>
                    </form>
                </div>

                {{-- Loading overlay --}}
                <div id="demo-overlay" style="display:none;position:fixed;inset:0;background:rgba(255,255,255,0.88);backdrop-filter:blur(3px);z-index:9999;flex-direction:column;align-items:center;justify-content:center;gap:14px;">
                    <div id="demo-spinner" style="width:46px;height:46px;border-radius:50%;border:4px solid #e5e7eb;border-top-color:#2563eb;animation:demo-spin .75s linear infinite;"></div>
                    <div id="demo-overlay-title" style="font-size:16px;font-weight:700;color:#111827;"></div>
                    <div id="demo-overlay-sub" style="font-size:13px;color:#6b7280;"></div>
                    <div style="font-size:12px;color:#9ca3af;margin-top:4px;">Please wait — do not close this page</div>
                </div>
                <style>@keyframes demo-spin { to { transform:rotate(360deg); } }</style>
            </div>
        </div>

    </div>

@stop

@push('scripts')
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function demoSubmit(event, confirmMsg, title, sub, accentColor) {
    if (!confirm(confirmMsg)) return false;

    // Disable both buttons to prevent double-submit
    ['btn-seed', 'btn-reset'].forEach(function(id) {
        var btn = document.getElementById(id);
        if (btn) { btn.disabled = true; btn.style.opacity = '0.5'; btn.style.cursor = 'not-allowed'; }
    });

    // Style spinner to match the action colour
    var spinner = document.getElementById('demo-spinner');
    if (spinner) spinner.style.borderTopColor = accentColor;

    document.getElementById('demo-overlay-title').textContent = title;
    document.getElementById('demo-overlay-sub').textContent = sub;

    var overlay = document.getElementById('demo-overlay');
    overlay.style.display = 'flex';

    return true; // allow form to submit
}
</script>
@endpush
