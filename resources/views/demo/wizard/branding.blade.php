@php $currentStep = 8; @endphp
@extends('layouts.setup')
@section('title', 'Brand Identity')
@section('content')
    @include('demo.wizard._banner')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Brand Identity</h1>
        <p class="text-gray-500 mt-2">Upload your logo and favicon to make the CMS your own.</p>
    </div>

    <div class="space-y-6">
        <div class="grid grid-cols-2 gap-6">
            {{-- Logo --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Logo</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 flex flex-col items-center justify-center bg-gray-50 cursor-not-allowed">
                    <img src="{{ asset('images/logo_small.png') }}" alt="Logo preview" class="h-12 object-contain mb-3 opacity-60">
                    <p class="text-xs text-gray-400">PNG, JPG, SVG or WebP up to 2MB</p>
                    <button disabled class="mt-3 opacity-50 cursor-not-allowed px-4 py-1.5 border border-gray-300 text-gray-400 text-xs rounded-lg">
                        Upload Logo
                    </button>
                </div>
            </div>
            {{-- Favicon --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Favicon</label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 flex flex-col items-center justify-center bg-gray-50 cursor-not-allowed">
                    <div class="w-10 h-10 rounded bg-brand-100 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <p class="text-xs text-gray-400">ICO, PNG or SVG up to 512KB</p>
                    <button disabled class="mt-3 opacity-50 cursor-not-allowed px-4 py-1.5 border border-gray-300 text-gray-400 text-xs rounded-lg">
                        Upload Favicon
                    </button>
                </div>
            </div>
        </div>
        <p class="text-xs text-gray-400">You can update your branding at any time from Admin → Settings → Brand.</p>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('demo.wizard.captcha') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <div class="flex items-center gap-3">
                <button disabled class="opacity-50 cursor-not-allowed px-5 py-2.5 bg-gray-200 text-gray-400 font-semibold text-sm rounded-lg">
                    Save &amp; Continue
                </button>
                <a href="{{ route('demo.wizard.complete') }}"
                    class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg transition-colors flex items-center gap-2">
                    Next
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>
@endsection
@section('illustration')
    <svg viewBox="0 0 200 200" class="w-44 h-44 mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="40" y="70" width="120" height="70" rx="8" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
        <circle cx="70" cy="105" r="15" fill="rgba(255,255,255,0.25)" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
        <rect x="95" y="95" width="50" height="8" rx="4" fill="rgba(255,255,255,0.3)"/>
        <rect x="95" y="110" width="35" height="6" rx="3" fill="rgba(255,255,255,0.2)"/>
    </svg>
@endsection
@section('illustration_title', 'Brand Identity')
@section('illustration_text', 'Make the CMS yours — upload your logo and it appears everywhere.')
