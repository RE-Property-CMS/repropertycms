@extends('admin.layouts.default')

@section('title', 'Demo Sessions')

@section('content')
<div class="w-full py-5">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fa fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fa fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <h5 class="mb-1">Demo Sessions</h5>
            <p class="text-sm text-gray-500 mb-0">All demo activity — self-service sign-ups and owner-sent invitations.</p>
        </div>
        <a href="{{ route('admin.demo.invite') }}" class="btn-blue m-0">
            <i class="fa fa-paper-plane mr-1"></i> Invite Someone
        </a>
    </div>

    {{-- ── Filters ───────────────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('admin.demo.sessions') }}" class="mb-5">
        <div class="flex flex-wrap gap-3 items-end">

            {{-- Status tabs --}}
            <div class="flex rounded-lg border border-gray-200 overflow-hidden text-sm font-medium">
                @foreach(['all' => 'All', 'active' => 'Active', 'expired' => 'Expired'] as $val => $label)
                    <a href="{{ route('admin.demo.sessions', array_merge(request()->query(), ['status' => $val, 'page' => 1])) }}"
                       class="px-4 py-2 {{ $status === $val ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            {{-- Type filter --}}
            <select name="type" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white text-gray-700 focus:outline-none focus:border-blue-400">
                <option value="all"         {{ $type === 'all'          ? 'selected' : '' }}>All Types</option>
                <option value="self_service" {{ $type === 'self_service' ? 'selected' : '' }}>Self-Service</option>
                <option value="invited"      {{ $type === 'invited'      ? 'selected' : '' }}>Invited</option>
            </select>

            {{-- Email search --}}
            <div class="flex gap-2 flex-1" style="min-width:220px;max-width:360px;">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by email…"
                       class="border border-gray-200 rounded-lg px-3 py-2 text-sm flex-1 focus:outline-none focus:border-blue-400">
                <button type="submit" class="btn-blue py-2 px-3 m-0 text-sm">
                    <i class="fa fa-search"></i>
                </button>
                @if($search)
                    <a href="{{ route('admin.demo.sessions', array_diff_key(request()->query(), ['search' => '', 'page' => ''])) }}"
                       class="btn-grey py-2 px-3 m-0 text-sm">
                        <i class="fa fa-xmark"></i>
                    </a>
                @endif
            </div>

        </div>
    </form>

    {{-- ── Table ────────────────────────────────────────────────────────── --}}
    <div class="table-responsive w-full">
        <table class="table w-full table-striped">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Type</th>
                    <th class="px-4 py-2 text-left">Created</th>
                    <th class="px-4 py-2 text-left">Expires</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                    @php $isActive = ! $session->isExpired(); @endphp
                    <tr>
                        <td class="border px-4 py-3">{{ $session->lead_name ?: '—' }}</td>
                        <td class="border px-4 py-3 text-sm">{{ $session->lead_email ?: '—' }}</td>
                        <td class="border px-4 py-3">
                            @if($session->type === 'invited')
                                <span class="inline-flex items-center gap-1 text-xs font-semibold bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full">
                                    <i class="fa fa-envelope fa-xs"></i> Invited
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">
                                    <i class="fa fa-globe fa-xs"></i> Self-Service
                                </span>
                            @endif
                        </td>
                        <td class="border px-4 py-3 text-sm text-gray-500" title="{{ $session->created_at->format('d M Y H:i') }}">
                            {{ $session->created_at->diffForHumans() }}
                        </td>
                        <td class="border px-4 py-3 text-sm text-gray-500" title="{{ $session->expires_at->format('d M Y H:i') }}">
                            {{ $session->expires_at->format('d M Y') }}
                            <span class="text-xs text-gray-400">({{ $session->expires_at->diffForHumans() }})</span>
                        </td>
                        <td class="border px-4 py-3">
                            @if($isActive)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-semibold bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span> Expired
                                </span>
                            @endif
                        </td>
                        <td class="border px-4 py-3">
                            @if($isActive)
                                <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-start;">
                                    {{-- Resend credentials --}}
                                    <form method="POST"
                                          action="{{ route('admin.demo.sessions.resend', $session->id) }}">
                                        @csrf
                                        <button type="submit" class="btn-blue text-sm py-1 px-3">
                                            <i class="fa fa-paper-plane mr-1"></i> Resend
                                        </button>
                                    </form>

                                    {{-- Revoke — requires SweetAlert2 confirmation --}}
                                    <form method="POST"
                                          action="{{ route('admin.demo.sessions.revoke', $session->id) }}"
                                          id="revoke-form-{{ $session->id }}">
                                        @csrf
                                        <button type="button"
                                                onclick="confirmRevoke({{ $session->id }}, '{{ addslashes($session->lead_email ?? 'this session') }}')"
                                                class="btn-red text-sm py-1 px-3">
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
                            No demo sessions found.
                            @if($search || $status !== 'all' || $type !== 'all')
                                <a href="{{ route('admin.demo.sessions') }}" class="text-blue-500 underline ml-1">Clear filters</a>
                            @else
                                <a href="{{ route('admin.demo.invite') }}" class="text-blue-500 underline ml-1">Send your first invitation</a>.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $sessions->links() }}
    </div>

</div>

@push('scripts')
<script>
function confirmRevoke(id, email) {
    Swal.fire({
        title: 'Revoke Demo Access?',
        html:  `This will <strong>immediately end</strong> the demo session for <strong>${email}</strong> and permanently delete all their sandbox data (admin account, agent account, properties). This cannot be undone.`,
        icon:  'warning',
        showCancelButton:  true,
        confirmButtonText: 'Yes, Revoke Access',
        cancelButtonText:  'Cancel',
        confirmButtonColor: '#dc2626',
        cancelButtonColor:  '#6b7280',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('revoke-form-' + id).submit();
        }
    });
}
</script>
@endpush

@endsection
