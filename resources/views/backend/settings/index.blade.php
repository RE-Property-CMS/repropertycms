@extends('backend.layouts.default')

@section('title', 'Integration Settings')

@section('content')
    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <h5 class="mb-0">Integration Settings</h5>
        <p class="text-sm text-gray-500 mb-0">Manage credentials for external services. All keys are stored in <code>.env</code> — never in the database.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">

        {{-- Mail --}}
        <div class="card p-5 border rounded-xl bg-white shadow-sm">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:{{ $statuses['mail'] ? '#d1fae5' : '#fee2e2' }};border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-envelope" style="color:{{ $statuses['mail'] ? '#059669' : '#dc2626' }};"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 font-semibold">Mail (SMTP)</h6>
                        <p class="text-xs text-gray-500 mb-0">Email notifications & password resets</p>
                    </div>
                </div>
                @if($statuses['mail'])
                    <span class="badge" style="background:#d1fae5;color:#065f46;font-size:11px;padding:4px 8px;border-radius:9999px;">Configured</span>
                @else
                    <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:11px;padding:4px 8px;border-radius:9999px;">Not Configured</span>
                @endif
            </div>
            <a href="{{ route('backend.settings.mail') }}" class="button button-green button-sm w-full text-center" style="display:block;text-align:center;">
                {{ $statuses['mail'] ? 'Update Settings' : 'Configure Now' }}
            </a>
        </div>

        {{-- Stripe --}}
        <div class="card p-5 border rounded-xl bg-white shadow-sm">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:{{ $statuses['stripe'] ? '#d1fae5' : '#fee2e2' }};border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-credit-card" style="color:{{ $statuses['stripe'] ? '#059669' : '#dc2626' }};"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 font-semibold">Stripe Payments</h6>
                        <p class="text-xs text-gray-500 mb-0">Agent subscriptions & billing</p>
                    </div>
                </div>
                @if($statuses['stripe'])
                    <span class="badge" style="background:#d1fae5;color:#065f46;font-size:11px;padding:4px 8px;border-radius:9999px;">Configured</span>
                @else
                    <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:11px;padding:4px 8px;border-radius:9999px;">Not Configured</span>
                @endif
            </div>
            <a href="{{ route('backend.settings.stripe') }}" class="button button-green button-sm w-full text-center" style="display:block;text-align:center;">
                {{ $statuses['stripe'] ? 'Update Settings' : 'Configure Now' }}
            </a>
        </div>

        {{-- Storage --}}
        <div class="card p-5 border rounded-xl bg-white shadow-sm">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:{{ $statuses['storage'] ? '#d1fae5' : '#fee2e2' }};border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-server" style="color:{{ $statuses['storage'] ? '#059669' : '#dc2626' }};"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 font-semibold">File Storage</h6>
                        <p class="text-xs text-gray-500 mb-0">Local disk or AWS S3 cloud storage</p>
                    </div>
                </div>
                @if($statuses['storage'])
                    <span class="badge" style="background:#d1fae5;color:#065f46;font-size:11px;padding:4px 8px;border-radius:9999px;">Configured</span>
                @else
                    <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:11px;padding:4px 8px;border-radius:9999px;">Not Configured</span>
                @endif
            </div>
            <a href="{{ route('backend.settings.storage') }}" class="button button-green button-sm w-full text-center" style="display:block;text-align:center;">
                {{ $statuses['storage'] ? 'Update Settings' : 'Configure Now' }}
            </a>
        </div>

        {{-- reCAPTCHA --}}
        <div class="card p-5 border rounded-xl bg-white shadow-sm">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;background:{{ $statuses['captcha'] ? '#d1fae5' : '#fee2e2' }};border-radius:8px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-shield-alt" style="color:{{ $statuses['captcha'] ? '#059669' : '#dc2626' }};"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 font-semibold">reCAPTCHA</h6>
                        <p class="text-xs text-gray-500 mb-0">Bot protection for registration forms</p>
                    </div>
                </div>
                @if($statuses['captcha'])
                    <span class="badge" style="background:#d1fae5;color:#065f46;font-size:11px;padding:4px 8px;border-radius:9999px;">Configured</span>
                @else
                    <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:11px;padding:4px 8px;border-radius:9999px;">Not Configured</span>
                @endif
            </div>
            <a href="{{ route('backend.settings.captcha') }}" class="button button-green button-sm w-full text-center" style="display:block;text-align:center;">
                {{ $statuses['captcha'] ? 'Update Settings' : 'Configure Now' }}
            </a>
        </div>

    </div>
@stop
