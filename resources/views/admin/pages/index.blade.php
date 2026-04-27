@extends('admin.layouts.default')

@section('title', 'Page Builder')

@section('content')
<div class="w-full py-5">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
            <i class="fa fa-circle-check mr-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
            <i class="fa fa-circle-exclamation mr-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-1">Page Builder</h5>
            <p class="text-sm text-gray-500 mb-0">Build and edit pages with the visual editor. System pages control the live site.</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="btn-blue m-0">
            <i class="fa fa-plus mr-1"></i> New Page
        </a>
    </div>

    {{-- ── System Pages ─────────────────────────────────────────────────── --}}
    <div class="mb-8">
        <p class="text-xs font-bold text-gray-500 mb-3" style="letter-spacing:.08em;text-transform:uppercase;">System Pages</p>
        <div class="table-responsive w-full">
            <table class="table w-full table-striped table-auto">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2">Page</th>
                        <th class="px-4 py-2">URL</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Last Saved</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($systemPages as $key => $entry)
                        @php $sp = $entry['page']; @endphp
                        <tr>
                            <td class="border px-4 py-3">
                                <span class="inline-flex items-center gap-1 text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full mr-2">
                                    <i class="fa fa-lock fa-xs"></i> system
                                </span>
                                <span class="font-medium">{{ $entry['label'] }}</span>
                            </td>
                            <td class="border px-4 py-3 text-sm text-gray-500">
                                {{ $key === 'home' ? '/' : '/demo' }}
                            </td>
                            <td class="border px-4 py-3">
                                @if($sp)
                                    @if($sp->action)
                                        <span class="text-xs font-semibold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                            ● Published
                                        </span>
                                    @else
                                        <span class="text-xs font-semibold bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">
                                            ● Draft
                                        </span>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400 italic">Using default Blade view</span>
                                @endif
                            </td>
                            <td class="border px-4 py-3 text-sm text-gray-500">
                                {{ $sp ? $sp->updated_at->diffForHumans() : '—' }}
                            </td>
                            <td class="border px-4 py-3">
                                @if($sp)
                                    <a href="{{ route('admin.pages.edit', $sp->id) }}" class="btn-blue m-0">
                                        <i class="fa fa-pen-to-square mr-1"></i> Edit
                                    </a>
                                @else
                                    <a href="{{ route('admin.pages.create') }}?preset={{ $key }}" class="btn-blue m-0">
                                        <i class="fa fa-pen-to-square mr-1"></i> Edit
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Custom Pages ─────────────────────────────────────────────────── --}}
    <div>
        <p class="text-xs font-bold text-gray-500 mb-3" style="letter-spacing:.08em;text-transform:uppercase;">Custom Pages</p>
        <div class="table-responsive w-full">
            <table class="table w-full table-striped table-auto">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2">Title</th>
                        <th class="px-4 py-2">URL Slug</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Last Saved</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customPages as $page)
                        <tr>
                            <td class="border px-4 py-3">{{ $page->title }}</td>
                            <td class="border px-4 py-3 text-sm text-gray-500">
                                <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">/{{ $page->slug }}</code>
                            </td>
                            <td class="border px-4 py-3">
                                @if($page->action)
                                    <span class="text-xs font-semibold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                        ● Published
                                    </span>
                                @else
                                    <span class="text-xs font-semibold bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">
                                        ● Draft
                                    </span>
                                @endif
                            </td>
                            <td class="border px-4 py-3 text-sm text-gray-500">
                                {{ $page->updated_at->diffForHumans() }}
                            </td>
                            <td class="border px-4 py-3">
                                <div style="display:flex;gap:6px;flex-wrap:wrap;align-items:center;">
                                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="btn-blue m-0">
                                        <i class="fa fa-pen-to-square mr-1"></i> Edit
                                    </a>
                                    <a href="/{{ $page->slug }}" target="_blank" class="btn-grey m-0">
                                        <i class="fa fa-eye mr-1"></i> View
                                    </a>
                                    <a href="{{ route('admin.pages.destroy', $page->id) }}"
                                       class="btn-red m-0"
                                       onclick="return confirm('Delete \'{{ addslashes($page->title) }}\'? This cannot be undone.')">
                                        <i class="fa fa-trash mr-1"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="border px-4 py-6 text-center text-gray-400" colspan="5">
                                No custom pages yet.
                                <a href="{{ route('admin.pages.create') }}" class="text-blue-600 underline ml-1">Create your first page</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $customPages->links() }}
        </div>
    </div>

</div>
@endsection
