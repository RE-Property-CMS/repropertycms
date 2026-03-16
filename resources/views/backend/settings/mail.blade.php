@extends('backend.layouts.default')

@section('title', 'Mail Settings')

@section('content')
    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-0">Mail Settings</h5>
            <nav class="text-sm text-gray-500 mt-1">
                <a href="{{ route('backend.settings.index') }}" class="text-gray-400 hover:text-gray-600">Settings</a>
                <span class="mx-2">›</span>
                <span>Mail</span>
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

        <div x-data="{
            host: '{{ $current['host'] }}',
            port: '{{ $current['port'] }}',
            username: '{{ $current['username'] }}',
            encryption: '{{ $current['encryption'] }}',
            fromAddress: '{{ $current['from_address'] }}',
            fromName: '{{ $current['from_name'] }}',
            password: '',
            testing: false, tested: false, testPassed: false, testMessage: '',
            async runTest() {
                this.testing = true; this.tested = false;
                try {
                    const r = await fetch('{{ route('backend.settings.mail.test') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            mail_host: this.host, mail_port: this.port,
                            mail_username: this.username, mail_password: this.password,
                            mail_encryption: this.encryption,
                            mail_from_address: this.fromAddress, mail_from_name: this.fromName
                        })
                    });
                    const d = await r.json();
                    this.testPassed = d.success; this.testMessage = d.message;
                } catch(e) { this.testPassed = false; this.testMessage = 'Request failed.'; }
                this.tested = true; this.testing = false;
            }
        }">
            <form method="POST" action="{{ route('backend.settings.mail.save') }}" class="space-y-4">
                @csrf

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                        <input type="text" name="mail_host" x-model="host" placeholder="smtp.mailtrap.io"
                            class="form-control" style="font-family:monospace;">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                        <input type="text" name="mail_port" x-model="port" placeholder="587"
                            class="form-control" style="font-family:monospace;">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Username</label>
                    <input type="text" name="mail_username" x-model="username" class="form-control">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Password</label>
                    <input type="password" name="mail_password" x-model="password"
                        placeholder="{{ $current['password_set'] ? '••••••••  (leave blank to keep current)' : 'Enter password' }}"
                        class="form-control">
                    @if($current['password_set'])
                        <p class="text-xs text-gray-400 mt-1">A password is currently set. Leave blank to keep it unchanged.</p>
                    @endif
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                        <select name="mail_encryption" x-model="encryption" class="form-control">
                            <option value="tls">TLS</option>
                            <option value="ssl">SSL</option>
                            <option value="">None</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Name</label>
                        <input type="text" name="mail_from_name" x-model="fromName"
                            placeholder="{{ config('app.name') }}" class="form-control">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Email Address</label>
                    <input type="email" name="mail_from_address" x-model="fromAddress"
                        placeholder="noreply@yourdomain.com" class="form-control">
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
                        <button type="button" @click="runTest()" :disabled="testing"
                            class="button button-sm" style="border:1px solid #4f46e5;color:#4f46e5;background:white;padding:6px 16px;border-radius:6px;font-size:13px;">
                            <i x-show="testing" class="fas fa-circle-notch fa-spin mr-1"></i>
                            <span x-show="!testing">Send Test Email</span>
                            <span x-show="testing">Sending...</span>
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
