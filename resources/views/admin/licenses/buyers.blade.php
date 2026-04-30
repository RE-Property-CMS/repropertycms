@extends('admin.layouts.default')

@section('title', 'License Buyers')

@section('content')
<div class="w-full py-5">

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fa fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-1">Buyers</h5>
            <p class="text-sm text-gray-500 mb-0">Customers who have purchased RePropertyCMS.</p>
        </div>
        <a href="{{ route('admin.licenses.buyers.create') }}" class="btn-blue m-0">
            <i class="fa fa-user-plus mr-1"></i> Add Buyer
        </a>
    </div>

    <div class="table-responsive w-full">
        <table class="table w-full table-striped">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-sm">Name</th>
                    <th class="px-4 py-2 text-left text-sm">Email</th>
                    <th class="px-4 py-2 text-left text-sm">Keys</th>
                    <th class="px-4 py-2 text-left text-sm">Domains in Use</th>
                    <th class="px-4 py-2 text-left text-sm">Joined</th>
                    <th class="px-4 py-2 text-left text-sm">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($buyers as $buyer)
                @php
                    $domainsUsed = $buyer->licenseKeys->sum(fn($k) => $k->licenseDomains->count());
                @endphp
                <tr>
                    <td class="border px-4 py-3 font-medium text-sm">{{ $buyer->name }}</td>
                    <td class="border px-4 py-3 text-sm text-gray-600">{{ $buyer->email }}</td>
                    <td class="border px-4 py-3 text-sm">{{ $buyer->license_keys_count }}</td>
                    <td class="border px-4 py-3 text-sm">{{ $domainsUsed }}</td>
                    <td class="border px-4 py-3 text-xs text-gray-500" title="{{ $buyer->created_at->format('d M Y H:i') }}">
                        {{ $buyer->created_at->format('d M Y') }}
                    </td>
                    <td class="border px-4 py-3">
                        <div class="flex gap-2 flex-wrap">
                            <a href="{{ route('admin.licenses.keys', ['buyer_id' => $buyer->id]) }}"
                               class="btn-blue text-sm py-1 px-3 m-0">
                                <i class="fa fa-key mr-1"></i> Keys
                            </a>
                            <a href="{{ route('admin.licenses.keys.create', ['buyer_id' => $buyer->id]) }}"
                               class="btn-blue text-sm py-1 px-3 m-0">
                                <i class="fa fa-plus mr-1"></i> New Key
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="border px-4 py-8 text-center text-gray-400" colspan="6">
                        No buyers yet.
                        <a href="{{ route('admin.licenses.buyers.create') }}" class="text-blue-500 underline ml-1">Add your first buyer.</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $buyers->links() }}
    </div>

</div>
@endsection
