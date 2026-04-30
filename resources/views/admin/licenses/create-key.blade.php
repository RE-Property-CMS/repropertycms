@extends('admin.layouts.default')

@section('title', 'Generate License Key')

@section('content')
<div class="w-full py-5" style="max-width:600px;">

    <div class="my-4 page-heading">
        <h5 class="mb-1">Generate License Key</h5>
        <p class="text-sm text-gray-500 mb-0">Create a new license key and assign it to a buyer.</p>
    </div>

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            @foreach($errors->all() as $error)
                <div><i class="fa fa-circle-exclamation mr-1"></i> {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.licenses.keys.store') }}" class="bg-white border border-gray-200 rounded-xl p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Buyer <span class="text-red-500">*</span></label>
            @if($buyers->isEmpty())
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700">
                    No buyers yet.
                    <a href="{{ route('admin.licenses.buyers.create') }}" class="underline font-medium">Add a buyer first →</a>
                </div>
            @else
                <select name="license_buyer_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
                    <option value="">— Select a buyer —</option>
                    @foreach($buyers as $buyer)
                        <option value="{{ $buyer->id }}" {{ request('buyer_id') == $buyer->id || old('license_buyer_id') == $buyer->id ? 'selected' : '' }}>
                            {{ $buyer->name }} ({{ $buyer->email }})
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Max Domains</label>
            <input type="number" name="max_domains" value="{{ old('max_domains', 5) }}" min="1" max="100" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
            <p class="text-xs text-gray-400 mt-1">Number of unique domains this key can be used on. Default is 5.</p>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Expiry Date <span class="text-gray-400 font-normal">(optional — leave blank for lifetime)</span></label>
            <input type="date" name="expires_at" value="{{ old('expires_at') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Notes <span class="text-gray-400 font-normal">(optional)</span></label>
            <textarea name="notes" rows="2"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400 resize-vertical"
                      placeholder="Order ID, tier, or internal notes…">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            @if($buyers->isNotEmpty())
                <button type="submit" class="btn-blue m-0">
                    <i class="fa fa-key mr-1"></i> Generate Key
                </button>
            @endif
            <a href="{{ route('admin.licenses.keys') }}" class="btn-grey m-0">Cancel</a>
        </div>
    </form>

</div>
@endsection
