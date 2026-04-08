@extends('admin.layouts.default')

@section('title', 'Subscriptions')

@section('content')

<style>
.sub-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: .75rem;
}
.sub-page-header h4 {
    font-size: 1.25rem;
    font-weight: 800;
    color: #111827;
    margin: 0;
}
.sub-page-header p { font-size: .8rem; color: #9ca3af; margin: .1rem 0 0; }
.sub-kpi-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.sub-kpi {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #f3f4f6;
    box-shadow: 0 2px 10px rgba(0,0,0,.05);
    padding: .9rem 1.3rem;
    min-width: 160px;
}
.sub-kpi-label { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #9ca3af; margin: 0 0 .3rem; }
.sub-kpi-val   { font-size: 1.7rem; font-weight: 800; color: #111827; margin: 0; line-height: 1; }
.back-link {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-size: .8rem;
    font-weight: 600;
    color: #6b7280;
    text-decoration: none;
    padding: .35rem .75rem;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    transition: background .15s;
}
.back-link:hover { background: #f9fafb; color: #111827; }
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    font-size: .68rem;
    font-weight: 700;
    padding: .2rem .55rem;
    border-radius: 20px;
}
.status-active   { background: #d1fae5; color: #065f46; }
.status-inactive { background: #fee2e2; color: #991b1b; }
.status-other    { background: #f3f4f6; color: #6b7280; }
</style>

<div class="sub-page-header">
    <div>
        <h4>Subscriptions</h4>
        <p>All agent subscriptions</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="back-link">
        <i class="fa fa-arrow-left" style="font-size:.7rem;"></i> Back to Dashboard
    </a>
</div>

{{-- KPI summary --}}
<div class="sub-kpi-row">
    <div class="sub-kpi">
        <p class="sub-kpi-label">Total Subscriptions</p>
        <p class="sub-kpi-val">{{ $totalAll }}</p>
    </div>
    <div class="sub-kpi" style="border-left: 3px solid #f59e0b;">
        <p class="sub-kpi-label">Active</p>
        <p class="sub-kpi-val" style="color:#d97706;">{{ $totalActive }}</p>
    </div>
    <div class="sub-kpi" style="border-left: 3px solid #e5e7eb;">
        <p class="sub-kpi-label">Inactive / Expired</p>
        <p class="sub-kpi-val" style="color:#9ca3af;">{{ $totalAll - $totalActive }}</p>
    </div>
</div>

{{-- Table --}}
<div class="table-responsive">
    <table class="table w-full table-striped table-auto">
        <thead>
        <tr>
            <th>#</th>
            <th>Agent</th>
            <th>Plan</th>
            <th>Stripe ID</th>
            <th>Status</th>
            <th>Start Date</th>
            <th>Expire Date</th>
        </tr>
        </thead>
        <tbody>
        @forelse($subscriptions as $sub)
            <tr>
                <td>{{ $sub->id }}</td>
                <td>
                    @if($sub->agent)
                        {{ $sub->agent->first_name }} {{ $sub->agent->last_name }}<br>
                        <small class="text-muted">{{ $sub->agent->email }}</small>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>{{ $sub->name ?? '—' }}</td>
                <td style="font-size:.75rem;color:#6b7280;">{{ $sub->stripe_id }}</td>
                <td>
                    @php $s = $sub->stripe_status; @endphp
                    <span class="status-badge {{ $s === 'active' ? 'status-active' : ($s === 'canceled' ? 'status-inactive' : 'status-other') }}">
                        <i class="fa fa-circle" style="font-size:.4rem;"></i>
                        {{ ucfirst($s) }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($sub->start_date)->format('d M Y') }}</td>
                <td>{{ $sub->current_period_end ? \Carbon\Carbon::parse($sub->current_period_end)->format('d M Y') : '—' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">No subscriptions found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($subscriptions->hasPages())
    <div class="mt-4">
        {{ $subscriptions->links() }}
    </div>
@endif

@stop
