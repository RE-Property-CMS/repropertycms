@php $currentStep = 5; @endphp
@extends('layouts.setup')

@section('title', 'Payments — Stripe')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Stripe Payments</h1>
        <p class="text-gray-500 mt-2">Connect Stripe to enable agent subscriptions and payments. <span class="text-amber-600 font-medium">Optional — you can skip this.</span></p>
    </div>

    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div x-data="{
        pubKey:'', secret:'', webhook:'',
        testing:false, tested:false, testPassed:false, testMessage:'',
        async runTest(){
            this.testing=true; this.tested=false;
            try{
                const r=await fetch('{{ route('setup.test-stripe') }}',{method:'POST',
                    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
                    body:JSON.stringify({stripe_secret:this.secret})});
                const d=await r.json(); this.testPassed=d.success; this.testMessage=d.message;
            }catch(e){this.testPassed=false;this.testMessage='Request failed.';}
            this.tested=true; this.testing=false;
        }
    }">
        <form method="POST" action="{{ route('setup.stripe.save') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Publishable Key</label>
                <input type="text" name="stripe_key" x-model="pubKey" placeholder="pk_live_..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                <input type="password" name="stripe_secret" x-model="secret" placeholder="sk_live_..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500">
                <p class="mt-1 text-xs text-gray-400">The secret key is used to validate the Stripe connection. It is stored in .env, never in the database.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Webhook Secret</label>
                <input type="password" name="stripe_webhook" placeholder="whsec_..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <div x-show="tested" x-cloak
                :class="testPassed?'bg-green-50 border-green-200 text-green-700':'bg-red-50 border-red-200 text-red-700'"
                class="p-3 rounded-lg border text-sm flex items-center gap-2">
                <svg x-show="testPassed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="!testPassed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span x-text="testMessage"></span>
            </div>

            <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('setup.mail') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Back
                </a>
                <div class="flex items-center gap-3">
                    <button type="button" @click="runTest()" :disabled="testing"
                        class="px-5 py-2.5 border border-brand-600 text-brand-600 hover:bg-brand-50 font-medium text-sm rounded-lg disabled:opacity-50 flex items-center gap-2">
                        <svg x-show="testing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-show="!testing">Test Stripe Keys</span><span x-show="testing">Testing...</span>
                    </button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 px-6 rounded-lg">
                        Save &amp; Continue
                    </button>
                </div>
            </div>
        </form>
        <form method="POST" action="{{ route('setup.stripe.skip') }}" class="mt-3">
            @csrf
            <button type="submit" class="w-full py-2.5 text-sm text-gray-400 hover:text-gray-600 border border-gray-200 hover:border-gray-300 rounded-lg">
                Skip for now — configure Stripe later in Admin Settings
            </button>
        </form>
    </div>
@endsection

@section('illustration')
    <svg viewBox="0 0 200 200" class="w-44 h-44 mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="45" y="65" width="110" height="75" rx="10" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
        <rect x="45" y="80" width="110" height="18" fill="rgba(255,255,255,0.25)"/>
        <rect x="55" y="105" width="35" height="8" rx="4" fill="rgba(255,255,255,0.4)"/>
        <rect x="55" y="120" width="25" height="8" rx="4" fill="rgba(255,255,255,0.3)"/>
        <circle cx="148" cy="58" r="18" fill="rgba(134,239,172,0.2)" stroke="rgba(134,239,172,0.7)" stroke-width="2"/>
        <path d="M141 58 L146 63 L156 51" stroke="rgba(255,255,255,0.9)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@endsection
@section('illustration_title', 'Stripe Payments')
@section('illustration_text', 'Required for agent subscriptions and payment processing. Keys are stored in .env only.')
