@extends('backend.layouts.default')

@section('title', 'Storage Settings')

@section('content')
    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-0">Storage Settings</h5>
            <nav class="text-sm text-gray-500 mt-1">
                <a href="{{ route('backend.settings.index') }}" class="text-gray-400 hover:text-gray-600">Settings</a>
                <span class="mx-2">›</span>
                <span>Storage</span>
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
            driver: '{{ $current['driver'] }}',
            awsKey: '{{ $current['aws_key'] }}',
            awsSecret: '',
            awsRegion: '{{ $current['aws_region'] }}',
            awsBucket: '{{ $current['aws_bucket'] }}',
            testing: false, tested: false, testPassed: false, testMessage: '',
            async runTest() {
                this.testing = true; this.tested = false;
                try {
                    const r = await fetch('{{ route('backend.settings.storage.test') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            driver: this.driver, aws_key: this.awsKey,
                            aws_secret: this.awsSecret, aws_region: this.awsRegion,
                            aws_bucket: this.awsBucket
                        })
                    });
                    const d = await r.json();
                    this.testPassed = d.success; this.testMessage = d.message;
                } catch(e) { this.testPassed = false; this.testMessage = 'Request failed.'; }
                this.tested = true; this.testing = false;
            }
        }">
            <form method="POST" action="{{ route('backend.settings.storage.save') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Storage Driver</label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                        <label class="d-flex align-items-center p-3 border rounded-lg"
                            :style="driver==='local' ? 'border-color:#4f46e5;background:#eef2ff;' : 'border-color:#e5e7eb;'"
                            style="cursor:pointer;">
                            <input type="radio" name="driver" value="local" x-model="driver" class="mr-2">
                            <div>
                                <p class="font-medium text-sm mb-0" :style="driver==='local' ? 'color:#4338ca' : 'color:#374151'">Local Storage</p>
                                <p class="text-xs text-gray-400 mb-0">Files stored on your server</p>
                            </div>
                        </label>
                        <label class="d-flex align-items-center p-3 border rounded-lg"
                            :style="driver==='s3' ? 'border-color:#4f46e5;background:#eef2ff;' : 'border-color:#e5e7eb;'"
                            style="cursor:pointer;">
                            <input type="radio" name="driver" value="s3" x-model="driver" class="mr-2">
                            <div>
                                <p class="font-medium text-sm mb-0" :style="driver==='s3' ? 'color:#4338ca' : 'color:#374151'">AWS S3</p>
                                <p class="text-xs text-gray-400 mb-0">Cloud object storage</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- S3 Fields --}}
                <div x-show="driver === 's3'" x-cloak class="space-y-4">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">AWS Access Key</label>
                            <input type="text" name="aws_key" x-model="awsKey"
                                placeholder="AKIAIOSFODNN7EXAMPLE"
                                class="form-control" style="font-family:monospace;">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">AWS Secret</label>
                            <input type="password" name="aws_secret" x-model="awsSecret"
                                placeholder="{{ $current['aws_secret_set'] ? '••••••••  (leave blank to keep current)' : 'Enter secret' }}"
                                class="form-control" style="font-family:monospace;">
                            @if($current['aws_secret_set'])
                                <p class="text-xs text-gray-400 mt-1">Leave blank to keep current secret.</p>
                            @endif
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">AWS Region</label>
                            <input type="text" name="aws_region" x-model="awsRegion"
                                placeholder="us-east-1"
                                class="form-control" style="font-family:monospace;">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bucket Name</label>
                            <input type="text" name="aws_bucket" x-model="awsBucket"
                                placeholder="my-bucket"
                                class="form-control" style="font-family:monospace;">
                        </div>
                    </div>
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
                            <span x-show="!testing">Test Storage</span>
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
