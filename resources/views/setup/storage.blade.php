@php $currentStep = 6; @endphp
@extends('layouts.setup')

@section('title', 'Storage Configuration')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">File Storage</h1>
        <p class="text-gray-500 mt-2">Choose where uploaded files and media will be stored. <span class="text-amber-600 font-medium">Optional — defaults to local storage.</span></p>
    </div>

    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div x-data="{
        driver:'local', awsKey:'', awsSecret:'', awsRegion:'us-east-1', awsBucket:'',
        testing:false, tested:false, testPassed:false, testMessage:'',
        async runTest(){
            this.testing=true; this.tested=false;
            try{
                const r=await fetch('{{ route('setup.test-storage') }}',{method:'POST',
                    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},
                    body:JSON.stringify({driver:this.driver,aws_key:this.awsKey,aws_secret:this.awsSecret,
                        aws_region:this.awsRegion,aws_bucket:this.awsBucket})});
                const d=await r.json(); this.testPassed=d.success; this.testMessage=d.message;
            }catch(e){this.testPassed=false;this.testMessage='Request failed.';}
            this.tested=true; this.testing=false;
        }
    }">
        <form method="POST" action="{{ route('setup.storage.save') }}" class="space-y-4">
            @csrf

            {{-- Driver selection --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Storage Driver</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative flex items-center p-4 border rounded-lg cursor-pointer"
                        :class="driver==='local'?'border-brand-500 bg-brand-50':'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="driver" value="local" x-model="driver" class="sr-only">
                        <div>
                            <p class="font-medium text-sm" :class="driver==='local'?'text-brand-700':'text-gray-700'">Local Storage</p>
                            <p class="text-xs text-gray-400 mt-0.5">Files stored on your server</p>
                        </div>
                    </label>
                    <label class="relative flex items-center p-4 border rounded-lg cursor-pointer"
                        :class="driver==='s3'?'border-brand-500 bg-brand-50':'border-gray-200 hover:border-gray-300'">
                        <input type="radio" name="driver" value="s3" x-model="driver" class="sr-only">
                        <div>
                            <p class="font-medium text-sm" :class="driver==='s3'?'text-brand-700':'text-gray-700'">AWS S3</p>
                            <p class="text-xs text-gray-400 mt-0.5">Cloud object storage</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- S3 fields (shown only when S3 selected) --}}
            <div x-show="driver==='s3'" x-cloak class="space-y-4 pt-2">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">AWS Access Key</label>
                        <input type="text" name="aws_key" x-model="awsKey" placeholder="AKIAIOSFODNN7EXAMPLE"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">AWS Secret</label>
                        <input type="password" name="aws_secret" x-model="awsSecret"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">AWS Region</label>
                        <input type="text" name="aws_region" x-model="awsRegion" placeholder="us-east-1"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bucket Name</label>
                        <input type="text" name="aws_bucket" x-model="awsBucket" placeholder="my-bucket"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>
            </div>

            <div x-show="tested" x-cloak
                :class="testPassed?'bg-green-50 border-green-200 text-green-700':'bg-red-50 border-red-200 text-red-700'"
                class="p-3 rounded-lg border text-sm flex items-center gap-2">
                <svg x-show="testPassed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                <svg x-show="!testPassed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                <span x-text="testMessage"></span>
            </div>

            <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('setup.stripe') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Back
                </a>
                <div class="flex items-center gap-3">
                    <button type="button" @click="runTest()" :disabled="testing"
                        class="px-5 py-2.5 border border-brand-600 text-brand-600 hover:bg-brand-50 font-medium text-sm rounded-lg disabled:opacity-50 flex items-center gap-2">
                        <svg x-show="testing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        <span x-show="!testing">Test Storage</span><span x-show="testing">Testing...</span>
                    </button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 px-6 rounded-lg">
                        Save &amp; Continue
                    </button>
                </div>
            </div>
        </form>
        <form method="POST" action="{{ route('setup.storage.skip') }}" class="mt-3">
            @csrf
            <button type="submit" class="w-full py-2.5 text-sm text-gray-400 hover:text-gray-600 border border-gray-200 hover:border-gray-300 rounded-lg">
                Skip — use local storage (can change later in Admin Settings)
            </button>
        </form>
    </div>
@endsection

@section('illustration')
    <svg viewBox="0 0 200 200" class="w-44 h-44 mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="55" y="55" width="90" height="90" rx="12" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.4)" stroke-width="2"/>
        <path d="M75 115 L75 90 L100 75 L125 90 L125 115" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.6)" stroke-width="2"/>
        <path d="M90 115 L90 100 L110 100 L110 115" fill="rgba(255,255,255,0.3)" stroke="rgba(255,255,255,0.5)" stroke-width="1.5"/>
        <path d="M100 75 L100 60" stroke="rgba(255,255,255,0.6)" stroke-width="2" stroke-linecap="round"/>
        <path d="M95 64 L100 58 L105 64" stroke="rgba(255,255,255,0.6)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@endsection
@section('illustration_title', 'File Storage')
@section('illustration_text', 'AWS S3 is recommended for production. Local storage works fine for small deployments.')
