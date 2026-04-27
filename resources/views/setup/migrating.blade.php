@php $currentStep = 2; @endphp
@extends('layouts.setup')

@section('title', 'Running Migrations')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Setting Up Database</h1>
        <p class="text-gray-500 mt-2">Running migrations in the background. This usually takes 10–30 seconds.</p>
    </div>

    <div x-data="{
        status: 'running',
        error: '',
        dots: '.',
        init() {
            this.poll();
            setInterval(() => {
                this.dots = this.dots.length >= 3 ? '.' : this.dots + '.';
            }, 500);
        },
        async poll() {
            try {
                const res = await fetch('{{ route('setup.migration-status') }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.status = data.status || 'unknown';

                if (this.status === 'done') {
                    window.location.href = '{{ route('setup.admin') }}';
                    return;
                }
                if (this.status === 'failed') {
                    this.error = data.error || 'Migration failed. Check storage/setup-migrate.log for details.';
                    return;
                }
            } catch(e) {
                // Server temporarily unreachable — keep polling
            }
            setTimeout(() => this.poll(), 3000);
        }
    }">

        {{-- Running state --}}
        <div x-show="status === 'running' || status === 'unknown'" class="flex flex-col items-center py-12 gap-6">
            <div class="relative">
                <div class="w-20 h-20 rounded-full border-4 border-brand-100 border-t-brand-600 animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
            </div>
            <div class="text-center">
                <p class="text-lg font-semibold text-gray-800">Running migrations<span x-text="dots"></span></p>
                <p class="text-sm text-gray-500 mt-1">Please keep this page open.</p>
            </div>

            {{-- Progress steps --}}
            <div class="w-full max-w-sm space-y-2 mt-2">
                @foreach(['Creating tables', 'Setting up relationships', 'Applying indexes', 'Finalizing schema'] as $step)
                <div class="flex items-center gap-3 text-sm text-gray-500">
                    <svg class="w-4 h-4 text-brand-400 animate-pulse flex-shrink-0" fill="currentColor" viewBox="0 0 8 8">
                        <circle cx="4" cy="4" r="3"/>
                    </svg>
                    {{ $step }}
                </div>
                @endforeach
            </div>
        </div>

        {{-- Failed state --}}
        <div x-show="status === 'failed'" x-cloak class="py-6">
            <div class="flex items-start gap-3 p-5 bg-red-50 border border-red-200 rounded-xl mb-6">
                <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="font-semibold text-red-800 mb-1">Migration failed</p>
                    <p class="text-red-700 text-sm" x-text="error"></p>
                    <p class="text-red-600 text-xs mt-2">Check <code class="bg-red-100 px-1 rounded">storage/setup-migrate.log</code> for the full error output.</p>
                </div>
            </div>
            <a href="{{ route('setup.database') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium text-sm rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Database Setup
            </a>
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

@section('illustration_title', 'Building Your Database')
@section('illustration_text', 'Migrations are running in the background. You will be redirected automatically.')
