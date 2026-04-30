@extends('admin.layouts.default')

@section('title', 'Add Buyer')

@section('content')
<div class="w-full py-5" style="max-width:600px;">

    <div class="my-4 page-heading">
        <h5 class="mb-1">Add Buyer</h5>
        <p class="text-sm text-gray-500 mb-0">Register a new customer who has purchased RePropertyCMS.</p>
    </div>

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            @foreach($errors->all() as $error)
                <div><i class="fa fa-circle-exclamation mr-1"></i> {{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.licenses.buyers.store') }}" class="bg-white border border-gray-200 rounded-xl p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400"
                   placeholder="Jane Smith">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400"
                   placeholder="jane@company.com">
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Notes <span class="text-gray-400 font-normal">(optional)</span></label>
            <textarea name="notes" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-400 resize-vertical"
                      placeholder="Order reference, notes about this purchase…">{{ old('notes') }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="btn-blue m-0">
                <i class="fa fa-user-plus mr-1"></i> Add Buyer
            </button>
            <a href="{{ route('admin.licenses.buyers') }}" class="btn-grey m-0">Cancel</a>
        </div>
    </form>

</div>
@endsection
