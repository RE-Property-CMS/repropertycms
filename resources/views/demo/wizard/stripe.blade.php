@php $currentStep = 5; @endphp
@extends('layouts.setup')
@section('title', 'Stripe Payments')
@section('content')
    @include('demo.wizard._banner')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Stripe Payments</h1>
        <p class="text-gray-500 mt-2">Connect Stripe to accept subscription payments from your agents.</p>
    </div>

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Publishable Key</label>
            <input type="text" value="pk_live_••••••••••••••••••••••••" disabled
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed font-mono">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
            <input type="password" value="demopassword" disabled
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed font-mono">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Webhook Secret</label>
            <input type="password" value="demopassword" disabled
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed font-mono">
        </div>
        <p class="text-xs text-gray-400">Your Stripe keys are stored securely in .env and never exposed to the browser.</p>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('demo.wizard.mail') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <div class="flex items-center gap-3">
                <button disabled class="opacity-50 cursor-not-allowed px-5 py-2.5 border border-brand-600 text-brand-600 font-medium text-sm rounded-lg">
                    Test Connection
                </button>
                <button disabled class="opacity-50 cursor-not-allowed px-5 py-2.5 bg-gray-200 text-gray-400 font-semibold text-sm rounded-lg">
                    Save &amp; Continue
                </button>
                <a href="{{ route('demo.wizard.storage') }}"
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
        <rect x="35" y="65" width="130" height="80" rx="10" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
        <rect x="35" y="85" width="130" height="20" fill="rgba(255,255,255,0.15)"/>
        <rect x="50" y="115" width="30" height="12" rx="3" fill="rgba(255,255,255,0.3)"/>
        <rect x="90" y="115" width="30" height="12" rx="3" fill="rgba(255,255,255,0.3)"/>
    </svg>
@endsection
@section('illustration_title', 'Stripe Payments')
@section('illustration_text', 'Accept agent subscription payments securely through Stripe.')
