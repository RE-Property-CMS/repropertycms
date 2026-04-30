@extends('admin.layouts.default')

@section('title', 'Dashboard')

@section('content')

<div class="my-4 d-flex align-items-center justify-content-between flex-wrap" style="gap:12px;">
    <div>
        <div style="display:flex;align-items:center;gap:10px;">
            <h5 class="mb-0">Dashboard</h5>
            <span style="background:#6366f1;color:white;font-size:11px;font-weight:600;padding:3px 10px;border-radius:9999px;letter-spacing:0.04em;">
                ★ Super Admin
            </span>
        </div>
        <p style="font-size:13px;color:#6b7280;margin-top:4px;">Demo session overview — {{ now()->format('F j, Y') }}</p>
    </div>
</div>

{{-- ── Top summary row ─────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">

    @php
    $cards = [
        [
            'label'  => 'Total Demos',
            'value'  => $demo['total'],
            'sub'    => 'all time',
            'color'  => '#6366f1',
            'bg'     => '#eef2ff',
            'icon'   => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        ],
        [
            'label'  => 'Active Now',
            'value'  => $demo['active'],
            'sub'    => 'not yet expired',
            'color'  => '#16a34a',
            'bg'     => '#f0fdf4',
            'icon'   => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        ],
        [
            'label'  => 'Self-Service',
            'value'  => $demo['self_service'],
            'sub'    => 'started by users',
            'color'  => '#2563eb',
            'bg'     => '#eff6ff',
            'icon'   => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        ],
        [
            'label'  => 'Invited',
            'value'  => $demo['invited'],
            'sub'    => 'sent by you',
            'color'  => '#d97706',
            'bg'     => '#fffbeb',
            'icon'   => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        ],
    ];
    @endphp

    @foreach($cards as $card)
    <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:20px 22px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
            <span style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.06em;">{{ $card['label'] }}</span>
            <div style="width:34px;height:34px;background:{{ $card['bg'] }};border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <svg style="width:17px;height:17px;color:{{ $card['color'] }};" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
        </div>
        <div style="font-size:2rem;font-weight:700;color:#111827;line-height:1;">{{ $card['value'] }}</div>
        <div style="font-size:12px;color:#9ca3af;margin-top:4px;">{{ $card['sub'] }}</div>
    </div>
    @endforeach

</div>

{{-- ── Second row: expired + period breakdown ──────────────────────── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">

    {{-- Expired --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:22px 24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:16px;">Session Status</div>
        <div style="display:flex;gap:24px;">
            <div style="flex:1;text-align:center;padding:16px;background:#f0fdf4;border-radius:10px;">
                <div style="font-size:1.8rem;font-weight:700;color:#16a34a;">{{ $demo['active'] }}</div>
                <div style="font-size:12px;color:#15803d;margin-top:4px;font-weight:500;">Active</div>
            </div>
            <div style="flex:1;text-align:center;padding:16px;background:#fef2f2;border-radius:10px;">
                <div style="font-size:1.8rem;font-weight:700;color:#dc2626;">{{ $demo['expired'] }}</div>
                <div style="font-size:12px;color:#991b1b;margin-top:4px;font-weight:500;">Expired</div>
            </div>
        </div>
        @if($demo['total'] > 0)
        <div style="margin-top:14px;background:#f9fafb;border-radius:8px;overflow:hidden;height:8px;">
            @php $pct = $demo['total'] > 0 ? round(($demo['active'] / $demo['total']) * 100) : 0; @endphp
            <div style="height:100%;width:{{ $pct }}%;background:#16a34a;transition:width 0.4s;"></div>
        </div>
        <div style="font-size:11px;color:#9ca3af;margin-top:5px;">{{ $pct }}% currently active</div>
        @endif
    </div>

    {{-- Period breakdown --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:22px 24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:16px;">New Demos Started</div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach([
                ['label' => 'Today',      'value' => $demo['today'],      'color' => '#6366f1'],
                ['label' => 'This Week',  'value' => $demo['this_week'],  'color' => '#2563eb'],
                ['label' => 'This Month', 'value' => $demo['this_month'], 'color' => '#0891b2'],
            ] as $row)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#f9fafb;border-radius:8px;">
                <span style="font-size:13px;color:#374151;font-weight:500;">{{ $row['label'] }}</span>
                <span style="font-size:1.1rem;font-weight:700;color:{{ $row['color'] }};">{{ $row['value'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ── Quick links ──────────────────────────────────────────────────── --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:20px 24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
    <div style="font-size:12px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:14px;">Quick Actions</div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('admin.demo.sessions') }}"
            style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#6366f1;color:white;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">
            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            View All Sessions
        </a>
        <a href="{{ route('admin.demo.invite') }}"
            style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:#d97706;color:white;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">
            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Send Invite
        </a>
        <a href="{{ route('admin.settings.index') }}"
            style="display:inline-flex;align-items:center;gap:7px;padding:9px 18px;background:white;color:#374151;border:1px solid #d1d5db;border-radius:8px;font-size:13px;font-weight:500;text-decoration:none;">
            <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Settings
        </a>
    </div>
</div>

@endsection
