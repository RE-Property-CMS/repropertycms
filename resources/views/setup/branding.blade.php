@php $currentStep = 9; @endphp
@extends('layouts.setup')

@section('title', 'Brand Identity')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Brand Identity</h1>
        <p class="text-gray-500 mt-2">Upload your logo and favicon. <span class="text-amber-600 font-medium">Optional — you can skip this and set it later in Admin Settings.</span></p>
    </div>

    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('setup.branding.save') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Logo --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Site Logo</label>
            <p class="text-xs text-gray-500 mb-3">Shown in the sidebar, admin panel, and agent portal. PNG or SVG with a transparent background recommended.</p>
            @if($brand && $brand->logo_path)
                <div class="mb-3 inline-flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <img src="{{ asset($brand->logo_path) }}" alt="Current Logo" style="height:40px;max-width:160px;object-fit:contain;">
                    <span class="text-xs text-gray-400">Current logo</span>
                </div>
            @endif
            <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg,image/gif,image/svg+xml,image/webp"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('logo') border-red-400 @enderror">
            @error('logo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            <p class="mt-1 text-xs text-gray-400">PNG, JPG, SVG or WebP. Max 2MB.</p>
        </div>

        {{-- Favicon --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
            <p class="text-xs text-gray-500 mb-3">The small icon shown in browser tabs. Recommended: 32×32 or 64×64 pixels.</p>
            @if($brand && $brand->favicon_path)
                <div class="mb-3 inline-flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                    <img src="{{ asset($brand->favicon_path) }}" alt="Current Favicon" style="height:24px;width:24px;object-fit:contain;">
                    <span class="text-xs text-gray-400">Current favicon</span>
                </div>
            @endif
            <input type="file" name="favicon" accept=".ico,image/png,image/jpeg,image/svg+xml"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('favicon') border-red-400 @enderror">
            @error('favicon') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            <p class="mt-1 text-xs text-gray-400">ICO, PNG or SVG. Max 512KB.</p>
        </div>

        <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('setup.captcha') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Back
            </a>
            <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 px-8 rounded-lg">
                Save &amp; Continue
            </button>
        </div>
    </form>

    <form method="POST" action="{{ route('setup.branding.skip') }}" class="mt-3">
        @csrf
        <button type="submit" class="w-full py-2.5 text-sm text-gray-400 hover:text-gray-600 border border-gray-200 hover:border-gray-300 rounded-lg">
            Skip for now — configure branding later in Admin Settings
        </button>
    </form>
@endsection

@section('illustration')
    <svg viewBox="0 0 200 200" class="w-44 h-44 mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
        {{-- Stylised logo/image frame --}}
        <rect x="40" y="50" width="120" height="90" rx="10" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
        {{-- Mountain / landscape inside --}}
        <path d="M60 118 L85 80 L110 105 L130 88 L155 118 Z" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" stroke-linejoin="round"/>
        {{-- Sun --}}
        <circle cx="148" cy="70" r="10" fill="rgba(255,255,255,0.3)" stroke="rgba(255,255,255,0.6)" stroke-width="1.5"/>
        {{-- "Image" icon inside frame --}}
        <circle cx="65" cy="73" r="7" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.5)" stroke-width="1.5"/>
        {{-- Favicon small box --}}
        <rect x="150" y="140" width="24" height="24" rx="4" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.5)" stroke-width="1.5"/>
        <path d="M156 152 L159 149 L162 153 L165 147 L168 152" stroke="rgba(255,255,255,0.7)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@endsection
@section('illustration_title', 'Your Brand, Your Identity')
@section('illustration_text', 'A custom logo and favicon help users recognise your platform instantly. You can change these anytime.')
