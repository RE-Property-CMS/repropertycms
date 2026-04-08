@extends('admin.layouts.default')

@section('title', 'Admin Dashboard')

@section('content')

<style>
/* ── Grid layouts ───────────────────────────────────── */
.db-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin: 1.5rem 0 1.25rem;
    align-items: stretch;
}
.db-grid-4 > a,
.db-grid-4 > div {
    display: flex;
    flex-direction: column;
}
.db-grid-4 > a > .db-card {
    flex: 1;
}
.db-grid-charts {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.25rem;
    margin-bottom: 1.25rem;
}
@media (max-width: 1199px) {
    .db-grid-4 { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 767px) {
    .db-grid-4 { grid-template-columns: 1fr; }
    .db-grid-charts { grid-template-columns: 1fr; }
}

/* ── KPI Card ───────────────────────────────────────── */
.db-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,.08);
    border: 1px solid rgba(0,0,0,.06);
    border-left: 4px solid #d1d5db;
    padding: 0;
    transition: box-shadow .22s ease, transform .22s ease;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
    position: relative;
}
.db-card:hover {
    box-shadow: 0 12px 36px rgba(0,0,0,.13);
    transform: translateY(-4px);
}
.db-card-body {
    padding: 1.3rem 1.4rem 1rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.db-card-top {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .75rem;
    margin-bottom: .6rem;
}
.db-card-info { flex: 1; min-width: 0; }
.db-card-label {
    font-size: .68rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #9ca3af;
    margin: 0 0 .45rem;
}
.db-card-val {
    font-size: 2.2rem;
    font-weight: 800;
    line-height: 1;
    color: #111827;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.db-card-icon {
    width: 50px;
    height: 50px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0,0,0,.18);
}
.db-spark-wrap { margin: .25rem 0 .5rem; line-height: 0; }
.db-card-footer {
    display: flex;
    align-items: center;
    gap: .45rem;
    padding-top: .65rem;
    border-top: 1px solid #f3f4f6;
    margin-top: auto;
}

/* Per-card left border + hover shadow */
.db-card-agents  { border-left-color: var(--primary-color, #4f46e5); }
.db-card-props   { border-left-color: #10b981; }
.db-card-subs    { border-left-color: #f59e0b; }
.db-card-revenue { border-left-color: #ef4444; }

.db-card-agents:hover  { box-shadow: 0 12px 36px rgba(79,70,229,.2); }
.db-card-props:hover   { box-shadow: 0 12px 36px rgba(16,185,129,.2); }
.db-card-subs:hover    { box-shadow: 0 12px 36px rgba(245,158,11,.2); }
.db-card-revenue:hover { box-shadow: 0 12px 36px rgba(239,68,68,.2); }
.db-badge {
    display: inline-flex;
    align-items: center;
    gap: .22rem;
    font-size: .7rem;
    font-weight: 700;
    padding: .18rem .5rem;
    border-radius: 20px;
    white-space: nowrap;
}
.db-badge-up      { background: #d1fae5; color: #065f46; }
.db-badge-neutral { background: #f3f4f6; color: #6b7280; }
.db-badge-blue    { background: #eff6ff; color: #1d4ed8; }
.db-footer-text { font-size: .7rem; color: #9ca3af; }

/* ── Chart Card ─────────────────────────────────────── */
.db-chart-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 14px rgba(0,0,0,.06);
    border: 1px solid rgba(0,0,0,.05);
    border-left: 4px solid #d1d5db;
    padding: 1.25rem 1.4rem .5rem;
    transition: box-shadow .22s ease, transform .22s ease;
}
.db-chart-card:hover { transform: translateY(-3px); }

/* Per-chart left border + hover shadow */
.db-chart-agents   { border-left-color: rgba(79,70,229,.2); }
.db-chart-props    { border-left-color: rgba(16,185,129,.2); }
.db-chart-newprops { border-left-color: rgba(16,185,129,.2); }
.db-chart-subs     { border-left-color: rgba(79,70,229,.2); }

.db-chart-agents:hover   { box-shadow: 0 10px 30px rgba(79,70,229,.4); }
.db-chart-props:hover    { box-shadow: 0 10px 30px rgba(16,185,129,.4); }
.db-chart-newprops:hover { box-shadow: 0 10px 30px rgba(16,185,129,.4); }
.db-chart-subs:hover     { box-shadow: 0 10px 30px rgba(79,70,229,.4); }
.db-chart-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: .5rem;
    gap: .5rem;
}
.db-chart-header h6 {
    font-size: .9rem;
    font-weight: 700;
    margin: 0 0 .2rem;
    color: #111827;
}
.db-chart-header p.sub { font-size: .73rem; color: #9ca3af; margin: 0; }
.db-period-badge {
    font-size: .68rem;
    font-weight: 600;
    background: #f0fdf4;
    color: #16a34a;
    border: 1px solid #bbf7d0;
    padding: .2rem .55rem;
    border-radius: 20px;
    white-space: nowrap;
    flex-shrink: 0;
    margin-top: .1rem;
}

/* ── Dashboard page heading ─────────────────────────── */
.db-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.db-page-header h4 {
    font-size: 1.35rem;
    font-weight: 800;
    color: #111827;
    margin: 0;
}
.db-page-header p { font-size: .8rem; color: #9ca3af; margin: .15rem 0 0; }


/* ── Table section heading ──────────────────────────── */
.db-table-heading {
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
    margin: 2rem 0 .75rem;
    padding-bottom: .6rem;
    border-bottom: 2px solid #f3f4f6;
}
</style>

    {{-- ============================================================
         PAGE HEADER
    ============================================================ --}}
    <div class="db-page-header">
        <div>
            <h4>Dashboard Overview</h4>
            <p>Welcome back — here's what's happening today.</p>
        </div>
    </div>

    {{-- ============================================================
         KPI STAT CARDS
    ============================================================ --}}
    <div class="db-grid-4">

        {{-- Total Agents --}}
        <a href="{{ route('admin.agentListing') }}" style="text-decoration:none;color:inherit;display:flex;flex-direction:column;">
        <div class="db-card db-card-agents" style="cursor:pointer;">
            <div class="db-card-body">
                <div class="db-card-top">
                    <div class="db-card-info">
                        <p class="db-card-label">Total Agents</p>
                        <p class="db-card-val">{{ $totalAgents }}</p>
                    </div>
                    <div class="db-card-icon" style="background:var(--primary-color,#4f46e5);">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
                <div class="db-spark-wrap"><div id="spark-agents"></div></div>
                <div class="db-card-footer">
                    @if($newAgentsThisMonth > 0)
                        <span class="db-badge db-badge-up">
                            <i class="fa fa-arrow-trend-up" style="font-size:.6rem;"></i>
                            +{{ $newAgentsThisMonth }}
                        </span>
                        <span class="db-footer-text">new this month</span>
                    @else
                        <span class="db-badge db-badge-neutral">No new agents this month</span>
                    @endif
                </div>
            </div>
        </div>
        </a>

        {{-- Total Properties --}}
        <a href="{{ route('admin.properties') }}" style="text-decoration:none;color:inherit;display:flex;flex-direction:column;">
        <div class="db-card db-card-props" style="cursor:pointer;">
            <div class="db-card-body">
                <div class="db-card-top">
                    <div class="db-card-info">
                        <p class="db-card-label">Total Properties</p>
                        <p class="db-card-val">{{ $totalProperties }}</p>
                    </div>
                    <div class="db-card-icon" style="background:#10b981;">
                        <i class="fa fa-building"></i>
                    </div>
                </div>
                <div class="db-spark-wrap"><div id="spark-properties"></div></div>
                <div class="db-card-footer">
                    <span class="db-badge {{ $publishedProperties > 0 ? 'db-badge-up' : 'db-badge-neutral' }}">
                        <i class="fa fa-circle-check" style="font-size:.6rem;"></i>
                        {{ $publishedProperties }}
                    </span>
                    <span class="db-footer-text">published</span>
                    @if($totalProperties > $publishedProperties)
                        <span class="db-badge db-badge-neutral" style="margin-left:auto;">
                            {{ $totalProperties - $publishedProperties }} unpublished
                        </span>
                    @endif
                </div>
            </div>
        </div>
        </a>

        {{-- Active Subscriptions --}}
        <a href="{{ route('admin.subscriptions') }}" style="text-decoration:none;color:inherit;display:flex;flex-direction:column;">
        <div class="db-card db-card-subs" style="cursor:pointer;">
            <div class="db-card-body">
                <div class="db-card-top">
                    <div class="db-card-info">
                        <p class="db-card-label">Active Subscriptions</p>
                        <p class="db-card-val">{{ $activeSubscriptions }}</p>
                    </div>
                    <div class="db-card-icon" style="background:#f59e0b;">
                        <i class="fa fa-credit-card"></i>
                    </div>
                </div>
                <div class="db-card-footer" style="margin-top:auto;">
                    <span class="db-badge db-badge-blue">
                        <i class="fa fa-circle" style="font-size:.45rem;"></i>
                        Stripe active
                    </span>
                </div>
            </div>
        </div>
        </a>

        {{-- Total Revenue --}}
        <a href="{{ route('admin.revenue') }}" style="text-decoration:none;color:inherit;display:flex;flex-direction:column;">
        <div class="db-card db-card-revenue" style="cursor:pointer;">
            <div class="db-card-body">
                <div class="db-card-top">
                    <div class="db-card-info">
                        <p class="db-card-label">Total Revenue</p>
                        <p class="db-card-val">${{ number_format($totalRevenue, 2) }}</p>
                    </div>
                    <div class="db-card-icon" style="background:#ef4444;">
                        <i class="fa fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="db-card-footer" style="margin-top:auto;">
                    <span class="db-badge db-badge-neutral">From paid payments</span>
                </div>
            </div>
        </div>
        </a>

    </div>

    {{-- ============================================================
         CHARTS ROW 1 — Agent Registrations + Properties Overview
    ============================================================ --}}
    <div class="db-grid-charts">

        <div class="db-chart-card db-chart-agents">
            <div class="db-chart-header">
                <div>
                    <h6>Agent Registrations</h6>
                    <p class="sub">New agents per month</p>
                </div>
                <span class="db-period-badge">Last 6 months</span>
            </div>
            <div id="agentChart"></div>
        </div>

        <a href="{{ route('admin.properties') }}" style="text-decoration:none;color:inherit;">
        <div class="db-chart-card db-chart-props" style="cursor:pointer;">
            <div class="db-chart-header">
                <div>
                    <h6>Properties Overview</h6>
                    <p class="sub">Published vs Unpublished</p>
                </div>
            </div>
            <div id="propertyDonut"></div>
        </div>
        </a>

    </div>

    {{-- ============================================================
         CHARTS ROW 2 — New Properties + Subscriptions by Plan
    ============================================================ --}}
    <div class="db-grid-charts">

        <div class="db-chart-card db-chart-newprops">
            <div class="db-chart-header">
                <div>
                    <h6>New Properties</h6>
                    <p class="sub">Properties added per month</p>
                </div>
                <span class="db-period-badge">Last 6 months</span>
            </div>
            <div id="propertyBarChart"></div>
        </div>

        <a href="{{ route('admin.subscriber.index') }}" style="text-decoration:none;color:inherit;">
        <div class="db-chart-card db-chart-subs" style="cursor:pointer;">
            <div class="db-chart-header">
                <div>
                    <h6>Subscriptions by Plan</h6>
                    <p class="sub">Active subscriptions breakdown</p>
                </div>
            </div>
            <div id="subscriptionDonut"></div>
        </div>
        </a>

    </div>

@stop

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.54.0/dist/apexcharts.min.js"></script>
<script>
(function () {
    var primaryColor = getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim() || '#4f46e5';
    if (!primaryColor) primaryColor = '#4f46e5';

    var agentCounts    = @json($agentMonthCounts);
    var agentLabels    = @json($agentMonthLabels);
    var propCounts     = @json($propertyMonthCounts);
    var propLabels     = @json($propertyMonthLabels);
    var planNames      = @json($planNames);
    var planCounts     = @json($planCounts);
    var propPublished  = {{ $publishedProperties }};
    var propTotal      = {{ $totalProperties }};

    // ── Shared sparkline defaults ─────────────────────────────────
    var sparkBase = {
        chart: { sparkline: { enabled: true }, animations: { enabled: false } },
        stroke: { curve: 'smooth', width: 2 },
        tooltip: {
            fixed: { enabled: false },
            x: { show: false },
            y: { title: { formatter: function() { return ''; } } },
            marker: { show: false }
        }
    };

    // ── Sparkline: Agents (line) ──────────────────────────────────
    new ApexCharts(document.querySelector('#spark-agents'), Object.assign({}, sparkBase, {
        chart: Object.assign({}, sparkBase.chart, { type: 'line', height: 48, width: '100%' }),
        series: [{ data: agentCounts }],
        colors: [primaryColor],
        fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0 } },
    })).render();

    // ── Sparkline: Properties (bar) ───────────────────────────────
    new ApexCharts(document.querySelector('#spark-properties'), Object.assign({}, sparkBase, {
        chart: Object.assign({}, sparkBase.chart, { type: 'bar', height: 48, width: '100%' }),
        series: [{ data: propCounts }],
        colors: ['#10b981'],
        plotOptions: { bar: { borderRadius: 2, columnWidth: '70%' } },
    })).render();

    // ── Agent Area Chart ──────────────────────────────────────────
    new ApexCharts(document.querySelector('#agentChart'), {
        chart: { type: 'area', height: 265, toolbar: { show: false } },
        series: [{ name: 'New Agents', data: agentCounts }],
        xaxis: {
            categories: agentLabels,
            labels: { style: { fontSize: '12px', colors: '#9ca3af' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            min: 0,
            labels: { formatter: function(v) { return Math.round(v); }, style: { colors: '#9ca3af' } }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2.5 },
        fill: {
            type: 'gradient',
            gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 100] }
        },
        colors: [primaryColor],
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
        markers: { size: 4, strokeWidth: 0, hover: { size: 6 } },
        tooltip: { y: { formatter: function(v) { return v + ' agents'; } } },
    }).render();

    // ── Property Donut ────────────────────────────────────────────
    var propUnpublished = propTotal - propPublished;
    new ApexCharts(document.querySelector('#propertyDonut'), {
        chart: { type: 'donut', height: 265 },
        series:  (propTotal === 0) ? [1]                              : [propPublished, propUnpublished],
        labels:  (propTotal === 0) ? ['No properties yet']            : ['Published', 'Unpublished'],
        colors:  (propTotal === 0) ? ['#e5e7eb']                      : ['#10b981', '#f3f4f6'],
        legend: { position: 'bottom', fontSize: '12px' },
        dataLabels: { enabled: propTotal > 0, style: { fontSize: '12px' } },
        plotOptions: { pie: { donut: { size: '68%', labels: {
            show: propTotal > 0,
            total: { show: true, label: 'Total', formatter: function() { return propTotal; } }
        } } } },
        stroke: { width: 0 },
        tooltip: { y: { formatter: function(v) { return v + ' properties'; } } },
    }).render();

    // ── Property Bar Chart ────────────────────────────────────────
    new ApexCharts(document.querySelector('#propertyBarChart'), {
        chart: { type: 'bar', height: 265, toolbar: { show: false } },
        series: [{ name: 'New Properties', data: propCounts }],
        xaxis: {
            categories: propLabels,
            labels: { style: { fontSize: '12px', colors: '#9ca3af' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            min: 0,
            labels: { formatter: function(v) { return Math.round(v); }, style: { colors: '#9ca3af' } }
        },
        dataLabels: { enabled: false },
        colors: ['#10b981'],
        plotOptions: { bar: { borderRadius: 5, columnWidth: '48%' } },
        grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
        tooltip: { y: { formatter: function(v) { return v + ' properties'; } } },
    }).render();

    // ── Subscription Donut ────────────────────────────────────────
    var donutColors = ['#4f46e5','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4'];
    // Clean up any null/empty labels that would break the donut
    planNames = planNames.map(function(n) { return n || 'Unknown Plan'; });
    if (planNames.length === 0 || planCounts.reduce(function(a,b){return a+b;},0) === 0) { planNames = ['No Data']; planCounts = [1]; donutColors = ['#e5e7eb']; }
    new ApexCharts(document.querySelector('#subscriptionDonut'), {
        chart: { type: 'donut', height: 265 },
        series: planCounts,
        labels: planNames,
        colors: donutColors,
        legend: { position: 'bottom', fontSize: '12px' },
        dataLabels: { enabled: planNames[0] !== 'No Data', style: { fontSize: '12px' } },
        plotOptions: { pie: { donut: { size: '68%', labels: {
            show: planNames[0] !== 'No Data',
            total: { show: true, label: 'Active', formatter: function(w) {
                return w.globals.seriesTotals.reduce(function(a,b) { return a+b; }, 0);
            }}
        } } } },
        stroke: { width: 0 },
        tooltip: { y: { formatter: function(v) { return v + ' active'; } } },
    }).render();
})();
</script>
@endpush
