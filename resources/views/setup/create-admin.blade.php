@php $currentStep = 3; @endphp
@extends('layouts.setup')

@section('title', 'Create Admin Account')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Account</h1>
        <p class="text-gray-500 mt-2">Create the super admin account for your platform.</p>
    </div>

    @if($errors->any())
        <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('setup.admin.save') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input type="text" name="name" value="{{ old('name') }}" placeholder="John Smith"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('name') border-red-400 @enderror"
                required autofocus>
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('email') border-red-400 @enderror"
                required>
            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" placeholder="Min. 8 characters"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 @error('password') border-red-400 @enderror"
                required>
            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input type="password" name="password_confirmation" placeholder="Repeat your password"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                required>
        </div>

        <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-100">
            <a href="{{ route('setup.database') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
            <button type="submit"
                class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 px-8 rounded-lg transition-colors duration-200 flex items-center gap-2">
                Create Admin &amp; Continue
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
    </form>
@endsection

@section('illustration')
    <svg viewBox="0 0 200 200" class="w-48 h-48 mx-auto" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="100" cy="72" r="30" fill="rgba(255,255,255,0.25)" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
        <circle cx="100" cy="66" r="14" fill="rgba(255,255,255,0.7)"/>
        <path d="M62 148 C62 122 138 122 138 148" fill="rgba(255,255,255,0.2)" stroke="rgba(255,255,255,0.5)" stroke-width="2"/>
        <circle cx="148" cy="140" r="16" fill="rgba(134,239,172,0.3)" stroke="rgba(134,239,172,0.8)" stroke-width="2"/>
        <path d="M141 140 L146 145 L155 135" stroke="rgba(255,255,255,0.9)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
@endsection

@section('illustration_title', 'Admin Account')
@section('illustration_text', 'This account will have full access to manage your platform.')
