@php $currentStep = 1; @endphp
@extends('layouts.setup')

@section('title', 'System Requirements')

@section('content')
    @php
        $allPassed = collect($requirements)->every(function ($req) {
            return is_array($req) ? $req['status'] : $req;
        });
    @endphp

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">System Requirements</h1>
        <p class="text-gray-500 mt-2">Checking that your server meets all requirements before proceeding.</p>
    </div>

    <div class="space-y-3">

        {{-- PHP Version --}}
        <div class="flex items-center justify-between p-4 rounded-lg border {{ $requirements['php_version']['status'] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
            <div>
                <p class="text-sm font-medium text-gray-800">PHP Version</p>
                <p class="text-xs text-gray-500">Required: {{ $requirements['php_version']['required'] }} &nbsp;|&nbsp; Current: {{ $requirements['php_version']['current'] }}</p>
            </div>
            @if($requirements['php_version']['status'])
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-3 py-1 rounded-full">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    Pass
                </span>
                @php if(!$requirements['php_version']['status']) $allPassed = false; @endphp
            @else
                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-100 px-3 py-1 rounded-full">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Fail
                </span>
            @endif
        </div>

        {{-- PHP Extensions --}}
        @foreach(['openssl' => 'OpenSSL Extension', 'pdo' => 'PDO Extension', 'mbstring' => 'Mbstring Extension', 'curl' => 'cURL Extension', 'fileinfo' => 'Fileinfo Extension'] as $key => $label)
            <div class="flex items-center justify-between p-4 rounded-lg border {{ $requirements[$key] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $label }}</p>
                    <p class="text-xs text-gray-500">{{ $requirements[$key] ? 'Enabled' : 'Not found — install this PHP extension' }}</p>
                </div>
                @if($requirements[$key])
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-3 py-1 rounded-full">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Pass
                    </span>
                @else
                    @php $allPassed = false; @endphp
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-100 px-3 py-1 rounded-full">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Fail
                    </span>
                @endif
            </div>
        @endforeach

        {{-- Writability Checks --}}
        @foreach(['storage_writable' => 'Storage Folder Writable', 'cache_writable' => 'Bootstrap Cache Writable', 'env_writable' => '.env File Writable'] as $key => $label)
            <div class="flex items-center justify-between p-4 rounded-lg border {{ $requirements[$key] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                <div>
                    <p class="text-sm font-medium text-gray-800">{{ $label }}</p>
                    <p class="text-xs text-gray-500">{{ $requirements[$key] ? 'Writable' : 'Not writable — check file permissions' }}</p>
                </div>
                @if($requirements[$key])
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-3 py-1 rounded-full">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Pass
                    </span>
                @else
                    @php $allPassed = false; @endphp
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-100 px-3 py-1 rounded-full">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Fail
                    </span>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-8 flex items-center justify-between">
        <a href="{{ route('setup.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>

        @if($allPassed)
            <a href="{{ route('setup.database') }}"
               class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-3 px-8 rounded-lg transition-colors duration-200 flex items-center gap-2">
                Continue to Database
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        @else
            <button disabled class="bg-gray-300 text-gray-500 font-semibold py-3 px-8 rounded-lg cursor-not-allowed">
                Fix Issues First
            </button>
        @endif
    </div>
@endsection

@section('illustration')
    <svg viewBox="0 0 200 200" class="w-48 h-48 mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="100" cy="100" r="75" fill="rgba(255,255,255,0.08)"/>
        <rect x="50" y="55" width="100" height="12" rx="6" fill="rgba(255,255,255,0.3)"/>
        <rect x="50" y="75" width="100" height="12" rx="6" fill="rgba(255,255,255,0.3)"/>
        <rect x="50" y="95" width="100" height="12" rx="6" fill="rgba(255,255,255,0.3)"/>
        <rect x="50" y="115" width="100" height="12" rx="6" fill="rgba(255,255,255,0.3)"/>
        <rect x="50" y="135" width="100" height="12" rx="6" fill="rgba(255,255,255,0.3)"/>
        <circle cx="162" cy="61" r="8" fill="rgba(134,239,172,0.9)"/>
        <circle cx="162" cy="81" r="8" fill="rgba(134,239,172,0.9)"/>
        <circle cx="162" cy="101" r="8" fill="rgba(134,239,172,0.9)"/>
        <circle cx="162" cy="121" r="8" fill="rgba(253,224,71,0.9)"/>
        <circle cx="162" cy="141" r="8" fill="rgba(134,239,172,0.9)"/>
    </svg>
@endsection

@section('illustration_title', 'Server Requirements')
@section('illustration_text', 'Verifying your server has everything needed to run the application.')
