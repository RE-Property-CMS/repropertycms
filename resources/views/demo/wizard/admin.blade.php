@php $currentStep = 3; @endphp
@extends('layouts.setup')
@section('title', 'Admin Account')
@section('content')
    @include('demo.wizard._banner')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Create Admin Account</h1>
        <p class="text-gray-500 mt-2">Set up the super admin account for your CMS.</p>
    </div>

    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" value="Alex Johnson" disabled
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" value="admin@yoursite.com" disabled
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" value="demopassword" disabled
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input type="password" value="demopassword" disabled
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('demo.wizard.database') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <div class="flex items-center gap-3">
                <button disabled class="opacity-50 cursor-not-allowed px-5 py-2.5 bg-gray-200 text-gray-400 font-semibold text-sm rounded-lg">
                    Save &amp; Continue
                </button>
                <a href="{{ route('demo.wizard.mail') }}"
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
        <circle cx="100" cy="80" r="30" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
        <path d="M50 160 Q50 130 100 130 Q150 130 150 160" stroke="rgba(255,255,255,0.5)" stroke-width="2" fill="rgba(255,255,255,0.1)"/>
    </svg>
@endsection
@section('illustration_title', 'Admin Account')
@section('illustration_text', 'This is the master account that controls the entire CMS.')
