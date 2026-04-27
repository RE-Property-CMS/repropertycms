@php $currentStep = 9; @endphp
@extends('layouts.setup')
@section('title', 'Setup Complete')
@section('content')
    @include('demo.wizard._banner')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">You're All Set</h1>
        <p class="text-gray-500 mt-2">The setup wizard is complete. Your CMS is ready to use.</p>
    </div>

    {{-- Summary --}}
    <div class="space-y-3 mb-8">
        @php
            $summary = [
                ['label' => 'Database',      'status' => 'Configured', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'],
                ['label' => 'Admin Account', 'status' => 'Created',    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                ['label' => 'Mail',          'status' => 'Configured', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ['label' => 'Payments',      'status' => 'Configured', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                ['label' => 'Storage',       'status' => 'Local',      'icon' => 'M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z'],
                ['label' => 'reCAPTCHA',     'status' => 'Configured', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['label' => 'Branding',      'status' => 'Configured', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ];
        @endphp
        @foreach($summary as $item)
        <div class="flex items-center justify-between p-3.5 rounded-lg border border-green-200 bg-green-50">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-800">{{ $item['label'] }}</span>
            </div>
            <span class="text-xs font-semibold text-green-700 bg-green-100 px-3 py-1 rounded-full">{{ $item['status'] }}</span>
        </div>
        @endforeach
    </div>

    <a href="{{ route('demo.wizard.finish') }}"
        class="w-full flex items-center justify-center gap-2 px-6 py-4 bg-brand-600 hover:bg-brand-700 text-white font-bold text-base rounded-xl transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        Enter Admin Panel
    </a>

    <div class="mt-4 text-center">
        <a href="{{ route('demo.wizard.branding') }}" class="text-sm text-gray-400 hover:text-gray-600">
            ← Back to branding
        </a>
    </div>
@endsection
@section('illustration')
    <svg viewBox="0 0 200 200" class="w-44 h-44 mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="100" cy="100" r="60" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
        <circle cx="100" cy="100" r="45" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
        <path d="M78 100 L92 114 L122 86" stroke="rgba(134,239,172,0.9)" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@endsection
@section('illustration_title', 'Installation Complete!')
@section('illustration_text', 'This is what your buyer sees right before entering their new admin panel for the first time.')
