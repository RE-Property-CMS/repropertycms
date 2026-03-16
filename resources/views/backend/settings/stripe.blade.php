@extends('backend.layouts.default')

@section('title', 'Stripe Settings')

@section('content')
    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-0">Stripe Settings</h5>
            <nav class="text-sm text-gray-500 mt-1">
                <a href="{{ route('backend.settings.index') }}" class="text-gray-400 hover:text-gray-600">Settings</a>
                <span class="mx-2">›</span>
                <span>Stripe</span>
            </nav>
        </div>
        @if($isConfigured)
            <span style="background:#d1fae5;color:#065f46;font-size:12px;padding:5px 12px;border-radius:9999px;font-weight:500;">
                <i class="fas fa-check-circle mr-1"></i> Configured
            </span>
        @else
            <span style="background:#fef3c7;color:#92400e;font-size:12px;padding:5px 12px;border-radius:9999px;font-weight:500;">
                <i class="fas fa-exclamation-circle mr-1"></i> Not Configured
            </span>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0 list-disc list-inside">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="card p-6 bg-white border rounded-xl shadow-sm" style="max-width:680px;">

        <div class="alert mb-4" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;font-size:13px;padding:12px 16px;border-radius:8px;">
            <i class="fas fa-info-circle mr-2"></i>
            Stripe keys are stored in <code>.env</code> only. They are never written to the database. The secret key is masked below.
        </div>

        <div x-data="{
            secret: '',
            testing: false, tested: false, testPassed: false, testMessage: '',
            async runTest() {
                this.testing = true; this.tested = false;
                try {
                    const r = await fetch('{{ route('backend.settings.stripe.test') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ stripe_secret: this.secret })
                    });
                    const d = await r.json();
                    this.testPassed = d.success; this.testMessage = d.message;
                } catch(e) { this.testPassed = false; this.testMessage = 'Request failed.'; }
                this.tested = true; this.testing = false;
            }
        }">
            <form method="POST" action="{{ route('backend.settings.stripe.save') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Publishable Key</label>
                    <input type="text" name="stripe_key" value="{{ $current['key'] }}"
                        placeholder="pk_live_..."
                        class="form-control" style="font-family:monospace;">
                    <p class="text-xs text-gray-400 mt-1">Safe to expose in the browser.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Secret Key</label>
                    <input type="password" name="stripe_secret" x-model="secret"
                        placeholder="{{ $current['secret_set'] ? '••••••••  (leave blank to keep current)' : 'sk_live_...' }}"
                        class="form-control" style="font-family:monospace;">
                    @if($current['secret_set'])
                        <p class="text-xs text-gray-400 mt-1">A secret key is currently set. Leave blank to keep it unchanged.</p>
                    @else
                        <p class="text-xs text-gray-400 mt-1">Stored in .env only. Never exposed to the browser.</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Webhook Secret</label>
                    <input type="password" name="stripe_webhook"
                        placeholder="{{ $current['webhook_set'] ? '••••••••  (leave blank to keep current)' : 'whsec_...' }}"
                        class="form-control" style="font-family:monospace;">
                    @if($current['webhook_set'])
                        <p class="text-xs text-gray-400 mt-1">A webhook secret is currently set. Leave blank to keep it unchanged.</p>
                    @endif
                </div>

                {{-- Test result --}}
                <div x-show="tested" x-cloak
                    :class="testPassed ? 'alert-success' : 'alert-danger'"
                    class="alert d-flex align-items-center gap-2 mb-0">
                    <i :class="testPassed ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                    <span x-text="testMessage"></span>
                </div>

                <div class="d-flex align-items-center justify-content-between pt-3" style="margin-top:1rem;padding-top:1rem;border-top:1px solid #e5e7eb;">
                    <a href="{{ route('backend.settings.index') }}" class="text-sm text-gray-500">← Back to Settings</a>
                    <div class="d-flex align-items-center gap-3">
                        <button type="button" @click="runTest()" :disabled="testing || !secret"
                            class="button button-sm" style="border:1px solid #4f46e5;color:#4f46e5;background:white;padding:6px 16px;border-radius:6px;font-size:13px;">
                            <i x-show="testing" class="fas fa-circle-notch fa-spin mr-1"></i>
                            <span x-show="!testing">Test Keys</span>
                            <span x-show="testing">Testing...</span>
                        </button>
                        <button type="submit" class="button button-green button-sm">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@push('scripts')
<script src="//unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endpush
