@php $currentStep = 2; @endphp
@extends('layouts.setup')

@section('title', 'Database Configuration')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Database Setup</h1>
        <p class="text-gray-500 mt-2">Enter your MySQL database connection details.</p>
    </div>

    {{-- Errors --}}
    @if($errors->has('db_error'))
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            {{ $errors->first('db_error') }}
        </div>
    @endif

    @if($errors->any() && !$errors->has('db_error'))
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="{
        host: '{{ old('db_host', '127.0.0.1') }}',
        port: '{{ old('db_port', '3306') }}',
        name: '{{ old('db_name') }}',
        user: '{{ old('db_user') }}',
        pass: '{{ old('db_pass') }}',
        testing: false,
        tested: false,
        testPassed: false,
        testMessage: '',
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
                        db_host: this.host,
                        db_port: this.port,
                        db_name: this.name,
                        db_user: this.user,
                        db_pass: this.pass,
                    })
                });
                const data = await res.json();
                this.testPassed = data.success;
                this.testMessage = data.message;
            } catch(e) {
                this.testPassed = false;
                this.testMessage = 'Request failed. Check server connectivity.';
            }
            this.tested = true;
            this.testing = false;
        }
    }">
        <form method="POST" action="{{ route('setup.database.save') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
                    <input type="text" name="db_host" x-model="host"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                    <input type="text" name="db_port" x-model="port"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Database Name</label>
                <input type="text" name="db_name" x-model="name" placeholder="your_database_name"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Database Username</label>
                <input type="text" name="db_user" x-model="user" placeholder="root"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Database Password</label>
                <input type="password" name="db_pass" x-model="pass" placeholder="Leave blank if no password"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            {{-- Test result banner --}}
            <div x-show="tested" x-cloak
                :class="testPassed ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700'"
                class="p-3 rounded-lg border text-sm flex items-center gap-2">
                <svg x-show="testPassed" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="!testPassed" class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span x-text="testMessage"></span>
            </div>

            <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('setup.requirements') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back
                </a>

                <div class="flex items-center gap-3">
                    <button type="button" @click="runTest()" :disabled="testing"
                        class="px-5 py-2.5 border border-brand-600 text-brand-600 hover:bg-brand-50 font-medium text-sm rounded-lg transition-colors duration-200 disabled:opacity-50 flex items-center gap-2">
                        <svg x-show="testing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-show="!testing">Test Connection</span>
                        <span x-show="testing">Testing...</span>
                    </button>

                    <button type="submit" :disabled="!tested || !testPassed"
                        :class="(tested && testPassed) ? 'bg-brand-600 hover:bg-brand-700 text-white' : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                        class="px-6 py-2.5 font-semibold text-sm rounded-lg transition-colors duration-200 flex items-center gap-2">
                        Save &amp; Continue
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

        </form>
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
