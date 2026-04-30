@extends('admin.layouts.default')

@section('title', 'License Management')

@section('content')
<div class="w-full py-5">

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fa fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-1">License Management</h5>
            <p class="text-sm text-gray-500 mb-0">Manage buyers, license keys, and verification activity.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.licenses.buyers.create') }}" class="btn-blue m-0">
                <i class="fa fa-user-plus mr-1"></i> Add Buyer
            </a>
            <a href="{{ route('admin.licenses.keys.create') }}" class="btn-blue m-0">
                <i class="fa fa-key mr-1"></i> Generate Key
            </a>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-5">
        @php
        $cards = [
            ['label' => 'Total Buyers',       'value' => $stats['total_buyers'],       'icon' => 'fa-users',       'color' => 'blue'],
            ['label' => 'Active Keys',         'value' => $stats['active_keys'],        'icon' => 'fa-key',         'color' => 'green'],
            ['label' => 'Domains in Use',      'value' => $stats['domains_in_use'],     'icon' => 'fa-globe',       'color' => 'purple'],
            ['label' => 'Verifications Today', 'value' => $stats['verifications_today'],'icon' => 'fa-shield-halved','color' => 'amber'],
        ];
        $colorMap = [
            'blue'   => 'bg-blue-50 border-blue-200 text-blue-700',
            'green'  => 'bg-green-50 border-green-200 text-green-700',
            'purple' => 'bg-purple-50 border-purple-200 text-purple-700',
            'amber'  => 'bg-amber-50 border-amber-200 text-amber-700',
        ];
        @endphp
        @foreach($cards as $card)
        <div class="col-6 col-md-3">
            <div class="border rounded-xl p-4 {{ $colorMap[$card['color']] }}">
                <div class="flex items-center gap-3">
                    <i class="fa {{ $card['icon'] }} text-xl"></i>
                    <div>
                        <div class="text-2xl font-bold">{{ $card['value'] }}</div>
                        <div class="text-xs font-medium opacity-75">{{ $card['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Keys at domain limit --}}
    @if($atLimit->isNotEmpty())
    <div class="mb-5 border border-amber-300 bg-amber-50 rounded-xl overflow-hidden">
        <div class="px-4 py-3 bg-amber-100 border-b border-amber-200 flex items-center gap-2">
            <i class="fa fa-triangle-exclamation text-amber-600"></i>
            <span class="font-semibold text-amber-800 text-sm">{{ $atLimit->count() }} key(s) have reached their domain limit</span>
        </div>
        <div class="divide-y divide-amber-200">
            @foreach($atLimit as $key)
            <div class="px-4 py-3 flex items-center justify-between flex-wrap gap-2">
                <div>
                    <span class="font-mono text-sm text-gray-800">{{ $key->key }}</span>
                    <span class="text-xs text-gray-500 ml-2">— {{ $key->buyer->name }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-amber-700">{{ $key->domainsUsed() }}/{{ $key->max_domains }} domains</span>
                    <a href="{{ route('admin.licenses.keys') }}" class="text-xs text-blue-600 underline">View Key</a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Recent verifications --}}
    <h6 class="font-semibold text-gray-700 mb-3">Recent Verifications</h6>
    <div class="table-responsive w-full">
        <table class="table w-full table-striped">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-sm">Key</th>
                    <th class="px-4 py-2 text-left text-sm">Buyer</th>
                    <th class="px-4 py-2 text-left text-sm">Domain</th>
                    <th class="px-4 py-2 text-left text-sm">IP</th>
                    <th class="px-4 py-2 text-left text-sm">Result</th>
                    <th class="px-4 py-2 text-left text-sm">Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $v)
                <tr>
                    <td class="border px-4 py-2 font-mono text-xs text-gray-600">
                        {{ $v->licenseKey ? $v->licenseKey->key : '—' }}
                    </td>
                    <td class="border px-4 py-2 text-sm">
                        {{ optional(optional($v->licenseKey)->buyer)->name ?? '—' }}
                    </td>
                    <td class="border px-4 py-2 text-sm text-gray-600">{{ $v->domain }}</td>
                    <td class="border px-4 py-2 text-xs text-gray-500">{{ $v->ip }}</td>
                    <td class="border px-4 py-2">
                        @include('admin.licenses._result-badge', ['result' => $v->result])
                    </td>
                    <td class="border px-4 py-2 text-xs text-gray-500" title="{{ $v->verified_at->format('d M Y H:i:s') }}">
                        {{ $v->verified_at->diffForHumans() }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="border px-4 py-8 text-center text-gray-400" colspan="6">No verifications yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-2 text-right">
            <a href="{{ route('admin.licenses.verifications') }}" class="text-sm text-blue-600 underline">View all verifications →</a>
        </div>
    </div>

</div>
@endsection
