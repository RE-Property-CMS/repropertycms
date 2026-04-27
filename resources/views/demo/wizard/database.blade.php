@php $currentStep = 2; @endphp
@extends('layouts.setup')
@section('title', 'Database Configuration')
@section('content')
    @include('demo.wizard._banner')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Database Setup</h1>
        <p class="text-gray-500 mt-2">Enter your MySQL database connection details.</p>
    </div>

    <div class="space-y-4">
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
                <input type="text" value="127.0.0.1" disabled
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                <input type="text" value="3306" disabled
                    class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Database Name</label>
            <input type="text" value="my_realestate_cms" disabled
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Database Username</label>
            <input type="text" value="root" disabled
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Database Password</label>
            <input type="password" value="demopassword" disabled
                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm bg-gray-50 text-gray-500 cursor-not-allowed">
        </div>

        <div class="p-3 rounded-lg border border-green-200 bg-green-50 text-green-700 text-sm flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            Database connection successful.
        </div>

        <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('demo.wizard.requirements') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <div class="flex items-center gap-3">
                <button disabled class="opacity-50 cursor-not-allowed px-5 py-2.5 border border-brand-600 text-brand-600 font-medium text-sm rounded-lg flex items-center gap-2">
                    Test Connection
                </button>
                <button disabled class="opacity-50 cursor-not-allowed px-5 py-2.5 bg-gray-200 text-gray-400 font-semibold text-sm rounded-lg flex items-center gap-2">
                    Save &amp; Continue
                </button>
                <a href="{{ route('demo.wizard.admin') }}"
                    class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg transition-colors flex items-center gap-2">
                    Next
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    </div>
@endsection
@section('illustration')
    <svg viewBox="0 0 200 200" class="w-48 h-48 mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="100" cy="70" rx="50" ry="18" fill="rgba(255,255,255,0.25)" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
        <rect x="50" y="70" width="100" height="30" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
        <ellipse cx="100" cy="100" rx="50" ry="18" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
        <rect x="50" y="100" width="100" height="30" fill="rgba(255,255,255,0.08)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
        <ellipse cx="100" cy="130" rx="50" ry="18" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
    </svg>
@endsection
@section('illustration_title', 'Database Configuration')
@section('illustration_text', 'Your credentials are written directly to .env and never stored in the database.')
