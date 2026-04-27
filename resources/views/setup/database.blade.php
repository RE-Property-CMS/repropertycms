@php $currentStep = 2; @endphp
@extends('layouts.setup')

@section('title', 'Database Configuration')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Database Setup</h1>
        <p class="text-gray-500 mt-2">Verify your database connection before running migrations.</p>
    </div>

    @if(!$configured)
        {{-- ── NOT CONFIGURED STATE ─────────────────────────────────────────── --}}
        <div class="p-5 bg-amber-50 border border-amber-300 rounded-xl mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                <div>
                    <p class="font-semibold text-amber-800 mb-2">Database credentials not found in <code class="bg-amber-100 px-1 rounded">.env</code></p>
                    <p class="text-amber-700 text-sm mb-4">Open your <code class="bg-amber-100 px-1 rounded">.env</code> file and set the following values before continuing:</p>
                    <div class="bg-white border border-amber-200 rounded-lg p-4 font-mono text-sm text-gray-700 space-y-1">
                        <div><span class="text-blue-600">DB_CONNECTION</span>=mysql</div>
                        <div><span class="text-blue-600">DB_HOST</span>=127.0.0.1</div>
                        <div><span class="text-blue-600">DB_PORT</span>=3306</div>
                        <div><span class="text-blue-600">DB_DATABASE</span>=<span class="text-gray-400">your_database_name</span></div>
                        <div><span class="text-blue-600">DB_USERNAME</span>=<span class="text-gray-400">your_db_user</span></div>
                        <div><span class="text-blue-600">DB_PASSWORD</span>=<span class="text-gray-400">your_db_password</span></div>
                    </div>
                    <p class="text-amber-700 text-sm mt-3">Once saved, click <strong>Re-check</strong> below.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('setup.requirements') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <a href="{{ route('setup.database') }}"
                class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold text-sm rounded-lg transition-colors duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Re-check
            </a>
        </div>

    @else
        {{-- ── CONFIGURED STATE ─────────────────────────────────────────────── --}}
        <div x-data="{
            testing: false,
            tested: false,
            testPassed: false,
            testMessage: '',
            saving: false,
            saveError: '',
            async runTest() {
                this.testing = true;
                this.tested = false;
                try {
                    const res = await fetch('{{ route('setup.test-database') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            db_host: '{{ addslashes($host) }}',
                            db_port: '{{ $port }}',
                            db_name: '{{ addslashes($name) }}',
                            db_user: '{{ addslashes($username) }}',
                            db_pass: '',
                        })
                    });
                    const data = await res.json();
                    this.testPassed = data.success;
                    this.testMessage = data.message;
                } catch(e) {
                    this.testPassed = false;
                    this.testMessage = 'Request failed: ' + e.message;
                }
                this.tested = true;
                this.testing = false;
            },
            async proceed() {
                this.saving = true;
                this.saveError = '';
                try {
                    const res = await fetch('{{ route('setup.database.save') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: '{}'
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        this.saveError = data.message || 'An unexpected error occurred.';
                        this.saving = false;
                    }
                } catch(e) {
                    this.saveError = 'Request failed: ' + e.message;
                    this.saving = false;
                }
            }
        }">
            {{-- Saving overlay --}}
            <div x-show="saving" x-cloak class="fixed inset-0 bg-white/80 backdrop-blur-sm z-50 flex flex-col items-center justify-center gap-4">
                <svg class="w-10 h-10 animate-spin text-brand-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <p class="text-gray-700 font-medium">Starting migration process…</p>
            </div>

            {{-- Credentials display --}}
            <div class="bg-gray-50 border border-gray-200 rounded-xl overflow-hidden mb-5">
                <div class="px-5 py-3 bg-gray-100 border-b border-gray-200 flex items-center justify-between">
                    <span class="text-sm font-semibold text-gray-600">Credentials from <code>.env</code></span>
                    <span class="text-xs text-gray-400">Read-only — edit <code>.env</code> to change</span>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach([
                        ['Host',     $host],
                        ['Port',     $port],
                        ['Database', $name],
                        ['Username', $username],
                        ['Password', $hasPass ? '••••••••' : '(none set)'],
                    ] as [$label, $value])
                    <div class="flex items-center px-5 py-3">
                        <span class="w-28 text-sm font-medium text-gray-500">{{ $label }}</span>
                        <span class="text-sm text-gray-800 font-mono">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Test result banner --}}
            <div x-show="tested" x-cloak
                :class="testPassed ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
                class="mb-4 p-3 rounded-lg border text-sm flex items-center gap-2">
                <svg x-show="testPassed" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="!testPassed" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span x-text="testMessage"></span>
            </div>

            {{-- Save error --}}
            <div x-show="saveError" x-cloak class="mb-4 p-3 rounded-lg border border-red-200 bg-red-50 text-red-700 text-sm" x-text="saveError"></div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('setup.requirements') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back
                </a>

                <div class="flex items-center gap-3">
                    <button type="button" @click="runTest()" :disabled="testing || saving"
                        class="px-5 py-2.5 border border-brand-600 text-brand-600 hover:bg-brand-50 font-medium text-sm rounded-lg transition-colors duration-200 disabled:opacity-50 flex items-center gap-2">
                        <svg x-show="testing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-show="!testing">Test Connection</span>
                        <span x-show="testing">Testing…</span>
                    </button>

                    <button type="button" @click="proceed()" :disabled="!tested || !testPassed || saving"
                        :class="(tested && testPassed && !saving) ? 'bg-brand-600 hover:bg-brand-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                        class="px-6 py-2.5 font-semibold text-sm rounded-lg transition-colors duration-200 flex items-center gap-2">
                        Proceed
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>
    @endif
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
@section('illustration_text', 'Your credentials are read directly from .env — never entered through the browser.')
