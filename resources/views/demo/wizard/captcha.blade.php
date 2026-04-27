@php $currentStep = 7; @endphp
@extends('layouts.setup')
@section('title', 'reCAPTCHA')
@section('content')
    @include('demo.wizard._banner')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">reCAPTCHA Protection</h1>
        <p class="text-gray-500 mt-2">Protect agent registration from spam and automated sign-ups.</p>
    </div>

    <div class="space-y-4">
        <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
            Get your free keys at <span class="font-semibold">google.com/recaptcha</span> — takes about 2 minutes to set up.
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Site Key</label>
            <input type="text" value="6LeXXXXXXXXXXXXXXXXXXXXX" disabled
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed font-mono">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
            <input type="password" value="demopassword" disabled
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed font-mono">
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('demo.wizard.storage') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <div class="flex items-center gap-3">
                <button disabled class="opacity-50 cursor-not-allowed px-5 py-2.5 bg-gray-200 text-gray-400 font-semibold text-sm rounded-lg">
                    Save &amp; Continue
                </button>
                <a href="{{ route('demo.wizard.branding') }}"
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
        <path d="M100 40 L130 55 L130 95 Q130 130 100 150 Q70 130 70 95 L70 55 Z" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
        <path d="M85 100 L95 110 L118 85" stroke="rgba(134,239,172,0.9)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@endsection
@section('illustration_title', 'Spam Protection')
@section('illustration_text', 'reCAPTCHA keeps your agent registration process clean and bot-free.')
