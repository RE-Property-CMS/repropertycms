@extends('admin.layouts.default')

@section('title', 'Revenue')

@section('content')

<style>
.rev-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: .75rem;
}
.rev-page-header h4 {
    font-size: 1.25rem;
    font-weight: 800;
    color: #111827;
    margin: 0;
}
.rev-page-header p { font-size: .8rem; color: #9ca3af; margin: .1rem 0 0; }
.rev-kpi-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.rev-kpi {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #f3f4f6;
    box-shadow: 0 2px 10px rgba(0,0,0,.05);
    padding: .9rem 1.3rem;
    min-width: 170px;
}
.rev-kpi-label { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #9ca3af; margin: 0 0 .3rem; }
.rev-kpi-val   { font-size: 1.7rem; font-weight: 800; color: #111827; margin: 0; line-height: 1; }
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
.pay-badge {
    display: inline-flex;
    align-items: center;
    gap: .25rem;
    font-size: .68rem;
    font-weight: 700;
    padding: .2rem .55rem;
    border-radius: 20px;
}
.pay-paid    { background: #d1fae5; color: #065f46; }
.pay-pending { background: #fef3c7; color: #92400e; }
.pay-unpaid  { background: #fee2e2; color: #991b1b; }
</style>

<div class="rev-page-header">
    <div>
        <h4>Revenue</h4>
        <p>All payment records</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="back-link">
        <i class="fa fa-arrow-left" style="font-size:.7rem;"></i> Back to Dashboard
    </a>
</div>

{{-- KPI summary --}}
<div class="rev-kpi-row">
    <div class="rev-kpi" style="border-left: 3px solid #ef4444;">
        <p class="rev-kpi-label">Total Revenue</p>
        <p class="rev-kpi-val" style="color:#ef4444;">${{ number_format($totalRevenue, 2) }}</p>
    </div>
    <div class="rev-kpi" style="border-left: 3px solid #10b981;">
        <p class="rev-kpi-label">Paid Payments</p>
        <p class="rev-kpi-val" style="color:#10b981;">{{ $totalPaid }}</p>
    </div>
    <div class="rev-kpi" style="border-left: 3px solid #f59e0b;">
        <p class="rev-kpi-label">Pending Payments</p>
        <p class="rev-kpi-val" style="color:#d97706;">{{ $totalPending }}</p>
    </div>
</div>

{{-- Table --}}
<div class="table-responsive">
    <table class="table w-full table-striped table-auto">
        <thead>
        <tr>
            <th>#</th>
            <th>Agent</th>
            <th>Payment Date</th>
            <th>Payment ID</th>
            <th>Amount</th>
            <th>Currency</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @forelse($payments as $payment)
            <tr>
                <td>{{ $payment->id }}</td>
                <td>
                    @if($payment->agent)
                        {{ $payment->agent->first_name }} {{ $payment->agent->last_name }}<br>
                        <small class="text-muted">{{ $payment->agent->email }}</small>
                    @else
                        <span class="text-muted">Agent #{{ $payment->agent_id }}</span>
                    @endif
                </td>
                <td>{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : '—' }}</td>
                <td style="font-size:.75rem;color:#6b7280;">{{ $payment->payment_id ?? '—' }}</td>
                <td style="font-weight:700;">${{ number_format($payment->amount ?? 0, 2) }}</td>
                <td>{{ strtoupper($payment->currency ?? '—') }}</td>
                <td>
                    @php $st = $payment->status; @endphp
                    <span class="pay-badge {{ $st === 'Paid' ? 'pay-paid' : ($st === 'Pending' ? 'pay-pending' : 'pay-unpaid') }}">
                        <i class="fa fa-circle" style="font-size:.4rem;"></i>
                        {{ $st }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted py-4">No payment records found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($payments->hasPages())
    <div class="mt-4">
        {{ $payments->links() }}
    </div>
@endif

@stop
