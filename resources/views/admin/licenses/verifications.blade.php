@extends('admin.layouts.default')

@section('title', 'Verification Log')

@section('content')
<div class="w-full py-5">

    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-1">Verification Log</h5>
            <p class="text-sm text-gray-500 mb-0">Every API call made by buyer installations during setup.</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.licenses.verifications') }}" class="mb-4 flex flex-wrap gap-3 items-end">

        <select name="result" onchange="this.form.submit()"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white text-gray-700 focus:outline-none">
            <option value="">All Results</option>
            @foreach([
                'success'              => 'Success',
                'invalid_key'          => 'Invalid Key',
                'revoked'              => 'Revoked',
                'expired'              => 'Expired',
                'domain_limit_reached' => 'Limit Reached',
            ] as $val => $label)
                <option value="{{ $val }}" {{ request('result') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-600">From</label>
            <input type="date" name="from" value="{{ request('from') }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
        </div>

        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-600">To</label>
            <input type="date" name="to" value="{{ request('to') }}"
                   class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
        </div>

        <button type="submit" class="btn-blue py-2 px-3 m-0 text-sm">
            <i class="fa fa-search mr-1"></i> Filter
        </button>

        @if(request('result') || request('from') || request('to'))
            <a href="{{ route('admin.licenses.verifications') }}" class="btn-grey py-2 px-3 m-0 text-sm">
                <i class="fa fa-xmark mr-1"></i> Clear
            </a>
        @endif
    </form>

    <div class="table-responsive w-full">
        <table class="table w-full table-striped">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-sm">Key</th>
                    <th class="px-4 py-2 text-left text-sm">Buyer</th>
                    <th class="px-4 py-2 text-left text-sm">Domain</th>
                    <th class="px-4 py-2 text-left text-sm">IP</th>
                    <th class="px-4 py-2 text-left text-sm">Result</th>
                    <th class="px-4 py-2 text-left text-sm">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($verifications as $v)
                <tr>
                    <td class="border px-4 py-2 font-mono text-xs text-gray-700">
                        {{ $v->licenseKey ? $v->licenseKey->key : '<span class="text-gray-400 font-sans">—</span>' }}
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
                        {{ $v->verified_at->format('d M Y H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="border px-4 py-8 text-center text-gray-400" colspan="6">No verifications found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $verifications->links() }}
    </div>

</div>
@endsection
