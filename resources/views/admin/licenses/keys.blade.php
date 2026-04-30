@extends('admin.layouts.default')

@section('title', 'License Keys')

@section('content')
<div class="w-full py-5">

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fa fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-1">License Keys</h5>
            <p class="text-sm text-gray-500 mb-0">All generated license keys and their domain usage.</p>
        </div>
        <a href="{{ route('admin.licenses.keys.create') }}" class="btn-blue m-0">
            <i class="fa fa-key mr-1"></i> Generate Key
        </a>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.licenses.keys') }}" class="mb-4 flex flex-wrap gap-3 items-end">
        <select name="status" onchange="this.form.submit()"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white text-gray-700 focus:outline-none">
            <option value="">All Statuses</option>
            @foreach(['active' => 'Active', 'revoked' => 'Revoked', 'expired' => 'Expired'] as $val => $label)
                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="buyer_id" onchange="this.form.submit()"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white text-gray-700 focus:outline-none">
            <option value="">All Buyers</option>
            @foreach($buyers as $buyer)
                <option value="{{ $buyer->id }}" {{ request('buyer_id') == $buyer->id ? 'selected' : '' }}>
                    {{ $buyer->name }}
                </option>
            @endforeach
        </select>

        @if(request('status') || request('buyer_id'))
            <a href="{{ route('admin.licenses.keys') }}" class="btn-grey py-2 px-3 m-0 text-sm">
                <i class="fa fa-xmark"></i> Clear
            </a>
        @endif
    </form>

    <div class="table-responsive w-full">
        <table class="table w-full table-striped">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-sm">License Key</th>
                    <th class="px-4 py-2 text-left text-sm">Buyer</th>
                    <th class="px-4 py-2 text-left text-sm">Status</th>
                    <th class="px-4 py-2 text-left text-sm">Domains</th>
                    <th class="px-4 py-2 text-left text-sm">Expires</th>
                    <th class="px-4 py-2 text-left text-sm">Last Verified</th>
                    <th class="px-4 py-2 text-left text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($keys as $key)
                @php
                    $used       = $key->licenseDomains->count();
                    $atLimit    = $used >= $key->max_domains;
                    $lastVerify = $key->verifications()->orderByDesc('verified_at')->first();
                @endphp
                <tr>
                    <td class="border px-4 py-3 font-mono text-xs text-gray-800 tracking-wide">{{ $key->key }}</td>
                    <td class="border px-4 py-3 text-sm">{{ $key->buyer->name }}</td>
                    <td class="border px-4 py-3">
                        @if($key->status === 'active')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Active
                            </span>
                        @elseif($key->status === 'revoked')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold bg-red-100 text-red-700 px-2 py-0.5 rounded-full">
                                <i class="fa fa-ban fa-xs"></i> Revoked
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                <i class="fa fa-clock fa-xs"></i> Expired
                            </span>
                        @endif
                    </td>
                    <td class="border px-4 py-3 text-sm">
                        <span class="{{ $atLimit ? 'text-amber-600 font-semibold' : 'text-gray-700' }}">
                            {{ $used }}/{{ $key->max_domains }}
                        </span>
                        @if($atLimit)
                            <i class="fa fa-triangle-exclamation text-amber-500 ml-1" title="At domain limit"></i>
                        @endif
                        @if($key->licenseDomains->isNotEmpty())
                            <div class="mt-1">
                                @foreach($key->licenseDomains as $d)
                                    <div class="text-xs text-gray-400 leading-tight">{{ $d->domain }}</div>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td class="border px-4 py-3 text-xs text-gray-500">
                        {{ $key->expires_at ? $key->expires_at->format('d M Y') : 'Lifetime' }}
                    </td>
                    <td class="border px-4 py-3 text-xs text-gray-500">
                        {{ $lastVerify ? $lastVerify->verified_at->diffForHumans() : '—' }}
                    </td>
                    <td class="border px-4 py-3">
                        @if($key->status === 'active')
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.licenses.keys.revoke', $key->id) }}"
                                  id="revoke-key-{{ $key->id }}">
                                @csrf
                                <button type="button"
                                        onclick="confirmRevokeKey({{ $key->id }}, '{{ addslashes($key->key) }}')"
                                        class="btn-red text-sm py-1 px-3 m-0">
                                    <i class="fa fa-ban mr-1"></i> Revoke
                                </button>
                            </form>
                        </div>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="border px-4 py-8 text-center text-gray-400" colspan="7">
                        No license keys found.
                        <a href="{{ route('admin.licenses.keys.create') }}" class="text-blue-500 underline ml-1">Generate your first key.</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $keys->links() }}
    </div>

</div>

@push('scripts')
<script>
function confirmRevokeKey(id, key) {
    Swal.fire({
        title: 'Revoke License Key?',
        html:  `The key <strong>${key}</strong> will be permanently revoked. The buyer's existing installations will still work, but they cannot use this key on any new domain.`,
        icon:  'warning',
        showCancelButton:   true,
        confirmButtonText:  'Yes, Revoke Key',
        cancelButtonText:   'Cancel',
        confirmButtonColor: '#dc2626',
        cancelButtonColor:  '#6b7280',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('revoke-key-' + id).submit();
        }
    });
}
</script>
@endpush

@endsection
