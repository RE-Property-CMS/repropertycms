@php $currentStep = 2; @endphp
@extends('layouts.setup')

@section('title', 'Running Migrations')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Setting Up Database</h1>
        <p class="text-gray-500 mt-2">Running migrations via background job. Usually completes in under 30 seconds.</p>
    </div>

    <div x-data="{
        status: 'queued',
        message: 'Waiting for worker...',
        error: '',
        countdown: 30,
        timer: null,
        pollInterval: null,

        init() {
            this.startCountdown();
            this.startPolling();
        },

        startCountdown() {
            this.timer = setInterval(() => {
                if (this.countdown > 0) this.countdown--;
            }, 1000);
        },

        startPolling() {
            this.poll();
            this.pollInterval = setInterval(() => this.poll(), 3000);
        },

        async poll() {
            try {
                const res  = await fetch('{{ route('setup.migration-status') }}', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                this.status  = data.status  || 'unknown';
                this.message = data.message || '';

                if (this.status === 'done') {
                    clearInterval(this.timer);
                    clearInterval(this.pollInterval);
                    window.location.href = '{{ route('setup.admin') }}';
                    return;
                }

                if (this.status === 'failed') {
                    clearInterval(this.timer);
                    clearInterval(this.pollInterval);
                    this.error = data.error || 'Migration failed.';
                    return;
                }

                if (this.countdown === 0) this.countdown = 15;

            } catch(e) {}
        },

        get statusLabel() {
            if (this.status === 'queued')  return 'Waiting for worker';
            if (this.status === 'running') return 'Running migrations';
            return 'Processing';
        },

        get barColor() {
            if (this.status === 'queued')  return 'bg-yellow-400';
            if (this.status === 'running') return 'bg-brand-600';
            return 'bg-gray-400';
        }
    }">

        {{-- Running / queued state --}}
        <div x-show="status !== 'failed'" class="flex flex-col items-center py-10 gap-6">

            <div class="relative">
                <div class="w-20 h-20 rounded-full border-4 border-brand-100 border-t-brand-600 animate-spin"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
            </div>

            <div class="text-center">
                <p class="text-lg font-semibold text-gray-800" x-text="statusLabel + '...'"></p>
                <p class="text-sm text-gray-500 mt-1" x-text="message || 'Please keep this page open.'"></p>
            </div>

            {{-- Countdown --}}
            <div class="flex flex-col items-center gap-1">
                <p class="text-4xl font-bold text-brand-600" x-text="countdown"></p>
                <p class="text-xs text-gray-400 uppercase tracking-widest">seconds estimated</p>
            </div>

            {{-- Progress bar --}}
            <div class="w-full max-w-sm">
                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                    <div class="h-2 rounded-full transition-all duration-1000"
                         :class="barColor"
                         :style="'width:' + Math.max(5, 100 - (countdown / 30 * 100)) + '%'">
                    </div>
                </div>
                <div class="flex justify-between text-xs text-gray-400 mt-1">
                    <span>Started</span>
                    <span>~30s</span>
                </div>
            </div>

            {{-- Steps --}}
            <div class="w-full max-w-sm space-y-2 mt-2">
                <div class="flex items-center gap-3 text-sm" :class="status === 'queued' ? 'text-yellow-500' : 'text-green-600'">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                    Job dispatched to queue
                </div>
                <div class="flex items-center gap-3 text-sm" :class="status === 'running' || status === 'done' ? 'text-green-600' : 'text-gray-400'">
                    <svg class="w-4 h-4 flex-shrink-0" :class="status === 'running' ? 'animate-pulse' : ''" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                    Running database migrations
                </div>
                <div class="flex items-center gap-3 text-sm" :class="status === 'done' ? 'text-green-600' : 'text-gray-400'">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3"/></svg>
                    Redirecting to next step
                </div>
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
@section('illustration_text', 'Your job is queued. The worker will complete it shortly and redirect you automatically.')
