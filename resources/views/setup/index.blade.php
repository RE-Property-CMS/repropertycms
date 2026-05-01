@php $currentStep = 0; @endphp
@extends('layouts.setup')

@section('title', 'Verify Setup Key')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome to Setup</h1>
        <p class="text-gray-500 mt-2">Enter your setup key to begin the installation wizard.</p>
    </div>

    {{-- Flash message --}}
    @if(session('info'))
        <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm">
            {{ session('info') }}
        </div>
    @endif

    {{-- Error --}}
    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            {{ $errors->first('setup_key') }}
        </div>
    @endif

    <form method="POST" action="{{ route('setup.verify-key') }}" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Setup Key
            </label>
            <input
                type="password"
                name="setup_key"
                placeholder="Enter your setup key"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('setup_key') border-red-400 @enderror"
                autofocus
                autocomplete="off"
            >
            <p class="mt-2 text-xs text-gray-400">
                Your license key was included in the purchase confirmation email. Each key is unique to your account.
                Need help? Email <a href="mailto:sales@repropertycms.com" class="text-brand-600 hover:underline">sales@repropertycms.com</a>
            </p>
        </div>

        <button
            type="submit"
            class="w-full bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200"
        >
            Verify Key &amp; Begin Setup
        </button>
    </form>
@endsection

@section('illustration')
    <svg viewBox="0 0 200 200" class="w-48 h-48 mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="100" cy="100" r="80" fill="rgba(255,255,255,0.1)"/>
        <rect x="65" y="75" width="70" height="60" rx="8" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
        <circle cx="100" cy="97" r="10" fill="rgba(255,255,255,0.8)"/>
        <rect x="93" y="107" width="14" height="16" rx="3" fill="rgba(255,255,255,0.8)"/>
        <circle cx="100" cy="50" r="18" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
        <path d="M92 50 L96 54 L108 46" stroke="rgba(255,255,255,0.9)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        <line x1="100" y1="68" x2="100" y2="75" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
    </svg>
@endsection

@section('illustration_title', 'Secure Installation')
@section('illustration_text', 'Your setup key protects this installation process. Keep it confidential.')
