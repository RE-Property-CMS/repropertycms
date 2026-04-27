@php $currentStep = 6; @endphp
@extends('layouts.setup')
@section('title', 'File Storage')
@section('content')
    @include('demo.wizard._banner')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">File Storage</h1>
        <p class="text-gray-500 mt-2">Choose where property images and documents are stored.</p>
    </div>

    <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div class="p-4 rounded-xl border-2 border-brand-500 bg-brand-50 cursor-not-allowed">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-4 h-4 rounded-full border-2 border-brand-500 bg-brand-500 flex items-center justify-center">
                        <div class="w-2 h-2 rounded-full bg-white"></div>
                    </div>
                    <span class="font-semibold text-brand-700">Local Storage</span>
                </div>
                <p class="text-xs text-gray-500">Store files on your own server. Simple setup, no extra cost.</p>
            </div>
            <div class="p-4 rounded-xl border-2 border-gray-200 bg-gray-50 cursor-not-allowed opacity-60">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-4 h-4 rounded-full border-2 border-gray-300"></div>
                    <span class="font-semibold text-gray-500">AWS S3</span>
                </div>
                <p class="text-xs text-gray-400">Scalable cloud storage for high-volume deployments.</p>
            </div>
        </div>
        <p class="text-xs text-gray-400">You can switch storage drivers later from Admin → Settings.</p>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('demo.wizard.stripe') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <div class="flex items-center gap-3">
                <button disabled class="opacity-50 cursor-not-allowed px-5 py-2.5 bg-gray-200 text-gray-400 font-semibold text-sm rounded-lg">
                    Save &amp; Continue
                </button>
                <a href="{{ route('demo.wizard.captcha') }}"
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
        <rect x="60" y="50" width="80" height="100" rx="6" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
        <path d="M80 90 L100 70 L120 90" stroke="rgba(255,255,255,0.6)" stroke-width="2" stroke-linecap="round"/>
        <line x1="100" y1="70" x2="100" y2="110" stroke="rgba(255,255,255,0.6)" stroke-width="2" stroke-linecap="round"/>
        <line x1="75" y1="120" x2="125" y2="120" stroke="rgba(255,255,255,0.4)" stroke-width="2" stroke-linecap="round"/>
    </svg>
@endsection
@section('illustration_title', 'File Storage')
@section('illustration_text', 'All property media is stored securely and served efficiently to visitors.')
