@php
    $demoToken    = session('demo_session_id');
    $demoSession  = $demoToken
        ? \App\Models\DemoSession::where('token', $demoToken)->first()
        : null;
@endphp

@if($demoToken && $demoSession && !$demoSession->isExpired())
@php
    $expiresAt    = $demoSession->expires_at->timestamp;
    $currentRole  = request()->is('admin*') ? 'admin' : (request()->is('agent*') ? 'agent' : 'buyer');
@endphp

<div id="demo-banner"
     style="position:fixed;bottom:0;left:0;right:0;z-index:9999;background:linear-gradient(90deg,#1e3a5f,#0f2340);border-top:1px solid rgba(59,130,246,.4);padding:10px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;font-family:system-ui,sans-serif;box-shadow:0 -4px 20px rgba(0,0,0,.4);">

    {{-- Left: Demo label + timer --}}
    <div style="display:flex;align-items:center;gap:14px;">
        <div style="display:flex;align-items:center;gap:6px;">
            <span style="width:8px;height:8px;background:#f59e0b;border-radius:50%;display:inline-block;animation:demopulse 1.5s infinite;"></span>
            <span style="color:#f59e0b;font-size:11px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;">DEMO MODE</span>
        </div>
        <div style="color:#93c5fd;font-size:13px;">
            Session ends in&nbsp;
            <span id="demo-timer" style="font-weight:700;color:#fff;font-variant-numeric:tabular-nums;">--:--</span>
        </div>
    </div>

    {{-- Centre: Role switcher --}}
    <div style="display:flex;align-items:center;gap:6px;">
        <span style="color:#6b7280;font-size:11px;text-transform:uppercase;letter-spacing:.05em;margin-right:4px;">Switch role:</span>

        <a href="/demo/{{ $demoToken }}/admin"
           style="text-decoration:none;padding:5px 14px;border-radius:8px;font-size:12px;font-weight:600;transition:all .15s;
                  {{ $currentRole === 'admin' ? 'background:#3b82f6;color:#fff;' : 'background:rgba(255,255,255,.1);color:#93c5fd;border:1px solid rgba(255,255,255,.15);' }}">
            <i class="fa fa-shield-halved" style="margin-right:4px;font-size:11px;"></i>Admin
        </a>

        <a href="/demo/{{ $demoToken }}/agent"
           style="text-decoration:none;padding:5px 14px;border-radius:8px;font-size:12px;font-weight:600;transition:all .15s;
                  {{ $currentRole === 'agent' ? 'background:#3b82f6;color:#fff;' : 'background:rgba(255,255,255,.1);color:#93c5fd;border:1px solid rgba(255,255,255,.15);' }}">
            <i class="fa fa-user-tie" style="margin-right:4px;font-size:11px;"></i>Agent
        </a>

        <a href="/demo/{{ $demoToken }}/buyer"
           target="_blank"
           style="text-decoration:none;padding:5px 14px;border-radius:8px;font-size:12px;font-weight:600;transition:all .15s;
                  background:rgba(255,255,255,.1);color:#93c5fd;border:1px solid rgba(255,255,255,.15);">
            <i class="fa fa-magnifying-glass" style="margin-right:4px;font-size:11px;"></i>Buyer <i class="fa fa-arrow-up-right-from-square" style="font-size:9px;margin-left:2px;"></i>
        </a>
    </div>

    {{-- Right: End button --}}
    <a href="/demo/{{ $demoToken }}/end"
       onclick="return confirm('End demo session and clear all data?')"
       style="text-decoration:none;padding:5px 14px;border-radius:8px;font-size:12px;font-weight:600;background:rgba(239,68,68,.15);color:#fca5a5;border:1px solid rgba(239,68,68,.3);">
        <i class="fa fa-xmark" style="margin-right:4px;"></i>End Demo
    </a>
</div>

<style>
    @keyframes demopulse { 0%,100%{opacity:1} 50%{opacity:.3} }
</style>

<script>
(function () {
    // Add bottom padding to body so content is not hidden behind the fixed banner
    document.documentElement.style.setProperty('padding-bottom', '52px');
    document.body.style.paddingBottom = '52px';

    var expiresAt = {{ $expiresAt }};
    var timerEl   = document.getElementById('demo-timer');

    function tick() {
        var remaining = expiresAt - Math.floor(Date.now() / 1000);
        if (remaining <= 0) {
            timerEl.textContent = '00:00';
            timerEl.style.color = '#f87171';
            window.location.href = '/demo?expired=1';
            return;
        }
        var m = Math.floor(remaining / 60);
        var s = remaining % 60;
        timerEl.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
        if (remaining <= 300) timerEl.style.color = '#f59e0b'; // amber at 5 min
        if (remaining <= 60)  timerEl.style.color = '#f87171'; // red at 1 min
    }

    tick();
    setInterval(tick, 1000);
})();
</script>
@endif
