@extends('admin.layouts.default')

@section('title', 'Invite to Demo')

@section('content')
<div class="w-full py-5">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-1">Invite to Demo</h5>
            <p class="text-sm text-gray-500 mb-0">Send a private 10-day demo invitation directly to a prospect or organisation.</p>
        </div>
        <a href="{{ route('admin.demo.sessions') }}" class="btn-grey m-0">
            <i class="fa fa-arrow-left mr-1"></i> All Sessions
        </a>
    </div>

    {{-- Flash error --}}
    @if(session('error'))
        <div class="mb-5 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fa fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Active session already exists — offer to resend --}}
    @if(session('existing_session'))
        <div class="mb-5 border border-amber-300 bg-amber-50 rounded-xl overflow-hidden">
            <div class="px-4 py-3 bg-amber-100 border-b border-amber-200 flex items-center gap-2">
                <i class="fa fa-triangle-exclamation text-amber-600"></i>
                <span class="font-semibold text-amber-800 text-sm">Active demo already exists for {{ session('existing_email') }}</span>
            </div>
            <div class="px-4 py-4">
                <p class="text-sm text-amber-700 mb-4">
                    This person already has an active demo session — it expires <strong>{{ session('existing_expires') }}</strong>.
                    If they didn't receive the email, you can resend their credentials below.
                </p>
                <form method="POST" action="{{ route('admin.demo.sessions.resend', session('existing_session')) }}">
                    @csrf
                    <button type="submit" class="btn-blue m-0">
                        <i class="fa fa-paper-plane mr-1"></i> Resend Credentials to {{ session('existing_email') }}
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- Form card --}}
    <div class="max-w-xl">
        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm">

            <form method="POST" action="{{ route('admin.demo.invite.store') }}">
                @csrf

                {{-- Name --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Contact Name <span class="font-normal text-gray-400">(optional)</span>
                    </label>
                    <div class="input-group-outline input-group is-filled">
                        <input type="text" name="name" value="{{ old('name') }}"
                               placeholder="e.g. Jane Smith or Acme Corp"
                               class="form-control">
                    </div>
                </div>

                {{-- Email --}}
                <div class="mb-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="input-group-outline input-group {{ $errors->has('email') ? 'is-invalid' : 'is-filled' }}">
                        <input type="email" name="email" value="{{ old('email') }}"
                               placeholder="prospect@company.com" required
                               class="form-control">
                    </div>
                    @error('email')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Duration notice --}}
                <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 mb-6 mt-4">
                    <i class="fa fa-clock text-blue-500 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-blue-800">10-day access</p>
                        <p class="text-xs text-blue-600 mt-0.5">The invitee will receive full sandbox access valid for 10 days. You can revoke it early from the Sessions list if needed.</p>
                    </div>
                </div>

                <button type="submit" class="btn-blue w-full m-0 flex items-center justify-center gap-2" style="padding:12px;">
                    <i class="fa fa-paper-plane"></i> Send Demo Invitation
                </button>
            </form>

        </div>

        <p class="text-xs text-gray-400 mt-4 text-center">
            The invitee will receive an email with their admin and agent credentials plus direct login links.<br>
            All sandbox data is automatically deleted when the session expires or is revoked.
        </p>
    </div>

</div>
@endsection
