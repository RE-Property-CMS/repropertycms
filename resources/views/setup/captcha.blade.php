@php $currentStep = 7; @endphp
@extends('layouts.setup')

@section('title', 'reCAPTCHA Security')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">reCAPTCHA Security</h1>
        <p class="text-gray-500 mt-2">Add Google reCAPTCHA to protect registration forms from bots. <span class="text-amber-600 font-medium">Optional — you can skip this.</span></p>
    </div>

    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm">
        <p class="font-medium mb-1">How to get reCAPTCHA keys:</p>
        <ol class="list-decimal list-inside space-y-1 text-xs">
            <li>Go to <span class="font-mono">https://www.google.com/recaptcha/admin</span></li>
            <li>Register a new site with reCAPTCHA v2 (checkbox)</li>
            <li>Copy the Site Key and Secret Key below</li>
        </ol>
    </div>

    <form method="POST" action="{{ route('setup.captcha.save') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Site Key</label>
            <input type="text" name="site_key" value="{{ old('site_key') }}"
                placeholder="6L..."
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500 @error('site_key') border-red-400 @enderror">
            @error('site_key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            <p class="mt-1 text-xs text-gray-400">Used in the frontend. Safe to make public.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
            <input type="password" name="secret_key"
                placeholder="6L..."
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500 @error('secret_key') border-red-400 @enderror">
            @error('secret_key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            <p class="mt-1 text-xs text-gray-400">Stored in .env only. Never exposed to the browser.</p>
        </div>

        <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('setup.storage') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Back
            </a>
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 px-8 rounded-lg">
                Save &amp; Continue
            </button>
        </div>
    </form>

    <form method="POST" action="{{ route('setup.captcha.skip') }}" class="mt-3">
        @csrf
        <button type="submit" class="w-full py-2.5 text-sm text-gray-400 hover:text-gray-600 border border-gray-200 hover:border-gray-300 rounded-lg">
            Skip for now — configure reCAPTCHA later in Admin Settings
        </button>
    </form>
@endsection

@section('illustration')
    <svg viewBox="0 0 200 200" class="w-44 h-44 mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M100 40 L140 58 L140 100 C140 128 120 148 100 158 C80 148 60 128 60 100 L60 58 Z"
            fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.5)" stroke-width="2" stroke-linejoin="round"/>
        <circle cx="100" cy="95" r="14" fill="rgba(255,255,255,0.3)" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
        <rect x="93" y="109" width="14" height="16" rx="3" fill="rgba(255,255,255,0.4)" stroke="rgba(255,255,255,0.6)" stroke-width="1.5"/>
        <circle cx="100" cy="95" r="4" fill="rgba(255,255,255,0.9)"/>
    </svg>
@endsection
@section('illustration_title', 'Bot Protection')
@section('illustration_text', 'reCAPTCHA prevents automated spam and abuse. Keys are stored securely in .env.')
