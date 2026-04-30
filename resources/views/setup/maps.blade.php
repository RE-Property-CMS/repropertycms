@php $currentStep = 8; @endphp
@extends('layouts.setup')

@section('title', 'Google Maps')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Google Maps</h1>
        <p class="text-gray-500 mt-2">Enable interactive maps on property listings and address search. <span class="text-amber-600 font-medium">Optional — you can skip this.</span></p>
    </div>

    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-700 text-sm">
        <p class="font-medium mb-1">How to get a Google Maps API key:</p>
        <ol class="list-decimal list-inside space-y-1 text-xs">
            <li>Go to <span class="font-mono">https://console.cloud.google.com</span></li>
            <li>Create a project (or select an existing one)</li>
            <li>Go to <strong>APIs & Services → Library</strong> and enable <strong>Maps JavaScript API</strong></li>
            <li>Go to <strong>APIs & Services → Credentials</strong> and create an <strong>API Key</strong></li>
            <li>Paste the key below</li>
        </ol>
    </div>

    <form method="POST" action="{{ route('setup.maps.save') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
            <input type="text" name="maps_api_key" value="{{ old('maps_api_key', config('services.google_map.api_key')) }}"
                placeholder="AIza..."
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500 @error('maps_api_key') border-red-400 @enderror">
            @error('maps_api_key') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            <p class="mt-1 text-xs text-gray-400">Used for property maps, address search, and nearby places. Stored in .env as <span class="font-mono">GOOGLE_MAP_API_KEY</span>.</p>
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

    <form method="POST" action="{{ route('setup.maps.skip') }}" class="mt-3">
        @csrf
        <button type="submit" class="w-full py-2.5 text-sm text-gray-400 hover:text-gray-600 border border-gray-200 hover:border-gray-300 rounded-lg">
            Skip for now — configure Google Maps later in Admin Settings
        </button>
    </form>
@endsection

@section('illustration')
    <svg viewBox="0 0 200 200" class="w-44 h-44 mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="100" cy="90" r="40" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
        <path d="M100 60 C86 60 75 71 75 85 C75 105 100 130 100 130 C100 130 125 105 125 85 C125 71 114 60 100 60Z"
            fill="rgba(255,255,255,0.25)" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
        <circle cx="100" cy="85" r="8" fill="rgba(255,255,255,0.6)" stroke="rgba(255,255,255,0.9)" stroke-width="1.5"/>
        <line x1="60" y1="150" x2="140" y2="150" stroke="rgba(255,255,255,0.3)" stroke-width="2" stroke-linecap="round"/>
        <line x1="75" y1="158" x2="125" y2="158" stroke="rgba(255,255,255,0.2)" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
@endsection
@section('illustration_title', 'Google Maps')
@section('illustration_text', 'One API key powers interactive maps, address geocoding, and nearby places search across all property listings.')
