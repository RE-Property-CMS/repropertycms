<div class="w-full py-5"
     x-data="{ confirmDeleteId: null, confirmPublishId: null }"
     x-on:show-publish-confirm.window="confirmPublishId = $event.detail.id">

@verbatim
<style>
/* ── Grid layouts ───────────────────────────────────── */
.ag-grid-4 {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
.ag-prop-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 1199px) {
    .ag-grid-4   { grid-template-columns: repeat(2, 1fr); }
    .ag-prop-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 767px) {
    .ag-grid-4   { grid-template-columns: repeat(2, 1fr); }
    .ag-prop-grid { grid-template-columns: 1fr; }
}
@media (max-width: 480px) {
    .ag-grid-4   { grid-template-columns: 1fr; }
}

/* ── Stat card ──────────────────────────────────────── */
.ag-stat-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 2px 10px rgba(0,0,0,.07);
    border: 1px solid rgba(0,0,0,.06);
    padding: 1.2rem 1.3rem;
    display: flex;
    align-items: center;
    gap: 14px;
    border-left: 4px solid #d1d5db;
}
.ag-stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.1rem;
}
.ag-stat-val {
    font-size: 1.8rem;
    font-weight: 700;
    line-height: 1;
    color: #111827;
}
.ag-stat-label {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: #9ca3af;
    margin-top: 4px;
}

/* ── Section header ─────────────────────────────────── */
.ag-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 8px;
}
.ag-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
}
.ag-section-bar {
    width: 4px;
    height: 22px;
    border-radius: 2px;
    display: inline-block;
    flex-shrink: 0;
}
.ag-section-pill {
    border-radius: 20px;
    padding: 2px 10px;
    font-size: 0.75rem;
    font-weight: 600;
}

/* ── Property card ──────────────────────────────────── */
.ag-prop-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e8e8e8;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
    transition: box-shadow .2s, transform .2s;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.ag-prop-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,.12);
    transform: translateY(-2px);
}
.ag-prop-img-wrap {
    position: relative;
    aspect-ratio: 16 / 9;
    overflow: hidden;
    flex-shrink: 0;
}
.ag-prop-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.ag-prop-img-placeholder {
    width: 100%;
    height: 100%;
    background: #f3f4f6;
    display: flex;
    align-items: center;
    justify-content: center;
}
.ag-prop-status {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: .04em;
}
.ag-prop-price {
    position: absolute;
    bottom: 10px;
    right: 10px;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.76rem;
    font-weight: 700;
    background: rgba(0,0,0,.55);
    color: #fff;
}
.ag-prop-body {
    padding: 12px 14px 8px;
    flex: 1;
}
.ag-prop-name {
    font-weight: 700;
    font-size: 0.88rem;
    color: #111827;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    margin-bottom: 3px;
}
.ag-prop-addr {
    font-size: 0.75rem;
    color: #9ca3af;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ag-prop-footer {
    padding: 9px 12px 11px;
    border-top: 1px solid #f3f4f6;
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.ag-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.73rem;
    font-weight: 600;
    padding: 4px 9px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: background .15s;
    white-space: nowrap;
    line-height: 1.4;
}
.ag-action-btn:hover { text-decoration: none; }
.ag-btn-edit    { background: #eff6ff; color: #2563eb; }
.ag-btn-edit:hover { background: #dbeafe; color: #2563eb; }
.ag-btn-preview { background: #f0fdf4; color: #16a34a; }
.ag-btn-preview:hover { background: #dcfce7; color: #16a34a; }
.ag-btn-share   { background: #f5f3ff; color: #7c3aed; }
.ag-btn-share:hover { background: #ede9fe; color: #7c3aed; }
.ag-btn-publish { background: #eff6ff; color: #0284c7; }
.ag-btn-publish:hover { background: #bae6fd; color: #0284c7; }
.ag-btn-delete  { background: #fef2f2; color: #dc2626; }
.ag-btn-delete:hover { background: #fee2e2; color: #dc2626; }

/* ── Empty state ────────────────────────────────────── */

.ag-empty {
    text-align: center;
    padding: 40px 24px;
    background: #fff;
    border-radius: 12px;
    border: 1px dashed #e5e7eb;
    color: #9ca3af;
}
</style>
@endverbatim

    {{-- Page heading --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:1.5rem;">
        <h3 style="margin:0;" class="page-heading">Dashboard</h3>
        @if($agent->hasActiveSubscription())
            <button wire:click="stripePortal" class="button font-bold text-base mb-0" style="background-color:rgb(0,100,131);">
                Subscription Portal
            </button>
        @endif
    </div>

    {{-- Subscription alert --}}
    @if($subscriptionAlert && isset($subscriptionAlert['alert_message']))
        <div style="display:flex;align-items:center;gap:14px;padding:16px 20px;border-radius:10px;border-left:5px solid {{ $subscriptionAlert['alert_color'] }};background:{{ $subscriptionAlert['alert_color'] }};margin-bottom:1.5rem;">
            <div style="font-size:1.8rem;color:{{ $subscriptionAlert['alert_foreground_color'] }}">{!! $subscriptionAlert['alert_icon'] !!}</div>
            <div>
                <div style="font-weight:700;font-size:1rem;color:{{ $subscriptionAlert['alert_foreground_color'] }}">{{ $subscriptionAlert['alert_section'] }}</div>
                <div style="font-size:0.9rem;color:#333;">{{ $subscriptionAlert['alert_message'] }}</div>
            </div>
        </div>
    @endif

    {{-- Stats bar --}}
    @php
        $totalCount       = $agent->properties()->count();
        $unpublishedCount = $totalCount - $publishedPropertiesCount;
    @endphp
    <div class="ag-grid-4">
        <div class="ag-stat-card" style="border-left-color:#2563eb;">
            <div class="ag-stat-icon" style="background:#eff6ff;">
                <i class="fa fa-building" style="color:#2563eb;"></i>
            </div>
            <div>
                <div class="ag-stat-val">{{ $totalCount }}</div>
                <div class="ag-stat-label">Total Properties</div>
            </div>
        </div>
        <div class="ag-stat-card" style="border-left-color:#16a34a;">
            <div class="ag-stat-icon" style="background:#f0fdf4;">
                <i class="fa fa-globe" style="color:#16a34a;"></i>
            </div>
            <div>
                <div class="ag-stat-val">{{ $publishedPropertiesCount }}</div>
                <div class="ag-stat-label">Published</div>
            </div>
        </div>
        <div class="ag-stat-card" style="border-left-color:#d97706;">
            <div class="ag-stat-icon" style="background:#fffbeb;">
                <i class="fa fa-pencil-alt" style="color:#d97706;"></i>
            </div>
            <div>
                <div class="ag-stat-val">{{ $unpublishedCount }}</div>
                <div class="ag-stat-label">Unpublished</div>
            </div>
        </div>
        <div class="ag-stat-card" style="border-left-color:#7c3aed;">
            <div class="ag-stat-icon" style="background:#f5f3ff;">
                <i class="fa fa-coins" style="color:#7c3aed;"></i>
            </div>
            <div>
                <div class="ag-stat-val">{{ $agent->credit_balance ?? 0 }}</div>
                <div class="ag-stat-label">Credits</div>
            </div>
        </div>
    </div>

    {{-- Published Properties --}}
    <div class="ag-section-header">
        <div class="ag-section-title">
            <span class="ag-section-bar" style="background:#16a34a;"></span>
            <h5 style="margin:0;font-weight:700;">Published Properties</h5>
            <span class="ag-section-pill" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">{{ $publishedPropertiesCount }}</span>
        </div>
        @if($published_properties->count() == 0 || $agent->hasActiveSubscription())
            <a href="{{ url('agent/property/address') }}" class="button btn-blue m-0">
                <i class="fa fa-plus mr-1"></i> New Property
            </a>
        @else
            <button wire:click="subscriptionPlan" class="button btn-blue m-0">
                <i class="fa fa-plus mr-1"></i> New Property
            </button>
        @endif
    </div>

    {{-- Published grid --}}
    @if($published_properties->count() > 0)
        <div class="ag-prop-grid">
            @foreach($published_properties as $row)
                @include('livewire.agent.partials.property-card', ['row' => $row])
            @endforeach
        </div>
    @else
        <div class="ag-empty" style="margin-bottom:1.5rem;">
            <i class="fa fa-home" style="font-size:2.2rem;margin-bottom:10px;display:block;"></i>
            <p style="font-weight:600;color:#6b7280;margin-bottom:3px;">No published properties</p>
            <p style="font-size:0.82rem;margin:0;">Publish a property to make it visible to the public.</p>
        </div>
    @endif

    {{-- Demo: live example banner --}}
    @if(session('demo_session_id'))
    <div style="display:flex;align-items:stretch;gap:0;border-radius:12px;overflow:hidden;border:1px solid #bfdbfe;margin-bottom:1rem;box-shadow:0 2px 8px rgba(37,99,235,.08);">
        {{-- Property thumbnail --}}
        <div style="flex-shrink:0;width:140px;min-height:90px;position:relative;overflow:hidden;">
            <img src="{{ asset('images/demo/publish-property-banner.jpg') }}" alt="101505 Valhalla Drive"
                 style="width:100%;height:100%;object-fit:cover;display:block;">
            <div style="position:absolute;inset:0;background:linear-gradient(to right,transparent 60%,rgba(239,246,255,0.6));"></div>
        </div>
        {{-- Content --}}
        <div style="flex:1;display:flex;align-items:center;gap:14px;padding:14px 18px;background:#eff6ff;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:0.7rem;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#3b82f6;margin-bottom:3px;">Live Example</div>
                <div style="font-weight:700;font-size:0.95rem;color:#1e3a8a;margin-bottom:4px;">101505 Valhalla Drive</div>
                <div style="font-size:0.78rem;color:#3b82f6;">See what your listings look like when published &amp; live.</div>
            </div>
            <a href="https://app.realtyinterface.com/101505-valhalla-drive" target="_blank" rel="noopener"
               style="flex-shrink:0;display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border-radius:8px;background:#2563eb;color:#fff;font-size:0.8rem;font-weight:600;text-decoration:none;white-space:nowrap;box-shadow:0 1px 4px rgba(37,99,235,.3);">
                View Live <i class="fa fa-arrow-up-right-from-square" style="font-size:0.7rem;"></i>
            </a>
        </div>
    </div>
    @endif

    {{-- Unpublished Properties --}}
    <div class="ag-section-header" style="margin-top:0.5rem;">
        <div class="ag-section-title">
            <span class="ag-section-bar" style="background:#d97706;"></span>
            <h5 style="margin:0;font-weight:700;">Unpublished Properties</h5>
            <span class="ag-section-pill" style="background:#fffbeb;color:#d97706;border:1px solid #fde68a;">{{ $unpublishedCount }}</span>
        </div>
    </div>

    {{-- Unpublished grid --}}
    @if($property_update->count() > 0)
        <div class="ag-prop-grid">
            @foreach($property_update as $row)
                @include('livewire.agent.partials.property-card', ['row' => $row])
            @endforeach
        </div>
    @else
        <div class="ag-empty">
            <i class="fa fa-home" style="font-size:2.2rem;margin-bottom:10px;display:block;"></i>
            <p style="font-weight:600;color:#6b7280;margin-bottom:3px;">No unpublished properties</p>
            <p style="font-size:0.82rem;margin:0;">All your properties are live.</p>
        </div>
    @endif

    {{-- Delete Confirmation Overlay --}}
    <div x-show="confirmDeleteId !== null" x-cloak style="position:fixed;inset:0;z-index:9999;">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.5);" @click="confirmDeleteId = null"></div>
        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
            <div style="position:relative;background:white;border-radius:12px;max-width:400px;width:90%;padding:24px;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
                <h3 style="font-size:1.1rem;font-weight:600;margin-bottom:12px;">Delete Property</h3>
                <p style="color:#6b7280;margin-bottom:20px;">Are you sure you want to delete this property? This action cannot be undone.</p>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" @click="confirmDeleteId = null" class="button button-grey">Cancel</button>
                    <button type="button" @click="$wire.doDeleteProperty(confirmDeleteId); confirmDeleteId = null" class="button button-red">Delete</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Publish Confirmation Overlay --}}
    <div x-show="confirmPublishId !== null" x-cloak style="position:fixed;inset:0;z-index:9999;">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.5);" @click="confirmPublishId = null"></div>
        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
            <div style="position:relative;background:white;border-radius:12px;max-width:400px;width:90%;padding:24px;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
                <h3 style="font-size:1.1rem;font-weight:600;margin-bottom:12px;">Publish Property</h3>
                <p style="color:#6b7280;margin-bottom:20px;">Are you sure you want to publish this property? It will be visible to the public.</p>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" @click="confirmPublishId = null" class="button button-grey">Cancel</button>
                    <button type="button" @click="$wire.doPublishProperty(confirmPublishId); confirmPublishId = null" class="button button-green">Publish</button>
                </div>
            </div>
        </div>
    </div>

</div>
