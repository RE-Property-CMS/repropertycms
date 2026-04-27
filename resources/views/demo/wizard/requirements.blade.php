@php $currentStep = 1; @endphp
@extends('layouts.setup')
@section('title', 'System Requirements')
@section('content')
    @include('demo.wizard._banner')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">System Requirements</h1>
        <p class="text-gray-500 mt-2">Before installation begins, the wizard checks that your server meets all requirements.</p>
    </div>

    <div class="space-y-2 mb-8">
        @php
            $labels = [
                'php_version'      => 'PHP Version (8.1+)',
                'openssl'          => 'OpenSSL Extension',
                'pdo'              => 'PDO Extension',
                'mbstring'         => 'Mbstring Extension',
                'curl'             => 'cURL Extension',
                'fileinfo'         => 'Fileinfo Extension',
                'storage_writable' => 'Storage Directory Writable',
                'cache_writable'   => 'Bootstrap/Cache Writable',
                'env_writable'     => '.env File Writable',
            ];
        @endphp
        @foreach($requirements as $key => $check)
            @php
                $pass   = is_array($check) ? $check['status'] : $check;
                $label  = $labels[$key] ?? $key;
                $detail = is_array($check) ? ('Current: ' . $check['current']) : null;
            @endphp
            <div class="flex items-center justify-between p-3 rounded-lg border {{ $pass ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                <div class="flex items-center gap-3">
                    @if($pass)
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    @else
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    @endif
                    <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                    @if($detail)
                        <span class="text-xs text-gray-400">({{ $detail }})</span>
                    @endif
                </div>
                <span class="text-xs font-semibold {{ $pass ? 'text-green-600' : 'text-red-600' }}">{{ $pass ? 'PASS' : 'FAIL' }}</span>
            </div>
        @endforeach
    </div>

    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
        <span class="text-sm text-gray-400 italic">Step 1 of 9</span>
        <a href="{{ route('demo.wizard.database') }}"
            class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg transition-colors flex items-center gap-2">
            Next
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
@endsection
@section('illustration')
    <svg viewBox="0 0 200 200" class="w-44 h-44 mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="100" cy="100" r="60" fill="rgba(255,255,255,0.12)" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
        <path d="M70 100 L88 118 L130 82" stroke="rgba(134,239,172,0.9)" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@endsection
@section('illustration_title', 'System Check')
@section('illustration_text', 'All server requirements are verified automatically before installation begins.')
