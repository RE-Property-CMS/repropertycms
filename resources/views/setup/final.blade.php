@php $currentStep = 9; @endphp
@extends('layouts.setup')

@section('title', 'Complete Installation')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Review &amp; Complete</h1>
        <p class="text-gray-500 mt-2">Your configuration summary. Click "Complete Installation" to finalize setup.</p>
    </div>

    {{-- Summary cards --}}
    <div class="space-y-3 mb-8">
        @php
            $items = [
                ['label' => 'Mail',       'configured' => !($setup['mail_skipped']    ?? true), 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['label' => 'Payments',   'configured' => !($setup['stripe_skipped']  ?? true), 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                ['label' => 'Storage',    'configured' => !($setup['storage_skipped'] ?? true), 'icon' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z'],
                ['label' => 'reCAPTCHA', 'configured' => !($setup['captcha_skipped'] ?? true), 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
            ];
        @endphp

        @foreach($items as $item)
            <div class="flex items-center justify-between p-4 rounded-lg border {{ $item['configured'] ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full {{ $item['configured'] ? 'bg-green-100' : 'bg-gray-200' }} flex items-center justify-center">
                        <svg class="w-4 h-4 {{ $item['configured'] ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="font-medium text-sm text-gray-800">{{ $item['label'] }}</span>
                </div>
                @if($item['configured'])
                    <span class="text-xs font-semibold text-green-700 bg-green-100 px-3 py-1 rounded-full">Configured</span>
                @else
                    <span class="text-xs font-semibold text-gray-500 bg-gray-200 px-3 py-1 rounded-full">Skipped</span>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Skipped items notice --}}
    @php $anySkipped = ($setup['mail_skipped'] ?? true) || ($setup['stripe_skipped'] ?? true) || ($setup['storage_skipped'] ?? true) || ($setup['captcha_skipped'] ?? true); @endphp
    @if($anySkipped)
        <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-amber-700 text-sm">
            <p class="font-medium">Some integrations were skipped.</p>
            <p class="mt-1 text-xs">You can configure them later from <strong>Admin Panel → Settings</strong>.</p>
        </div>
    @endif

    <div x-data="{ finishing: false, error: '' }">
        {{-- Finishing overlay --}}
        <div x-show="finishing" x-cloak class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 flex flex-col items-center justify-center gap-4">
            <svg class="w-10 h-10 animate-spin text-brand-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <p class="text-gray-700 font-medium">Finalizing installation… please wait.</p>
        </div>

        <div x-show="error" x-cloak class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm" x-text="error"></div>

        <button type="button" :disabled="finishing"
            @click="
                finishing = true; error = '';
                fetch('{{ route('setup.finish') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: '{}'
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) { window.location.href = data.redirect; }
                    else { error = data.message || 'An error occurred.'; finishing = false; }
                })
                .catch(e => { error = 'Request failed: ' + e.message; finishing = false; });
            "
            class="w-full bg-brand-600 hover:bg-brand-700 disabled:opacity-50 text-white font-bold py-4 rounded-lg text-base transition-colors duration-200 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Complete Installation
        </button>

        <div class="mt-4 text-center">
            <a href="{{ route('setup.branding') }}" class="text-sm text-gray-400 hover:text-gray-600">
                ← Go back and make changes
            </a>
        </div>
    </div>
@endsection

@section('illustration')
    <svg viewBox="0 0 200 200" class="w-44 h-44 mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="100" cy="100" r="60" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
        <circle cx="100" cy="100" r="45" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
        <path d="M78 100 L92 114 L122 86" stroke="rgba(134,239,172,0.9)" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="100" cy="100" r="59" stroke="rgba(255,255,255,0.2)" stroke-width="2" stroke-dasharray="8 4"/>
    </svg>
@endsection
@section('illustration_title', 'Almost Done!')
@section('illustration_text', 'Click "Complete Installation" to finish setup and access your admin panel.')
