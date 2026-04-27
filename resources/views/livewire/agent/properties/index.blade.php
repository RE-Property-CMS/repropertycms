<div class="w-full py-5"
     x-data="{ confirmDeleteId: null, confirmPublishId: null }"
     x-on:show-publish-confirm.window="confirmPublishId = $event.detail.id">

<style>
.pl-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 1199px) { .pl-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 767px)  { .pl-grid { grid-template-columns: 1fr; } }

.pl-card {
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
.pl-card:hover {
    box-shadow: 0 6px 24px rgba(0,0,0,.12);
    transform: translateY(-2px);
}
.pl-img-wrap {
    position: relative;
    aspect-ratio: 16 / 9;
    overflow: hidden;
    flex-shrink: 0;
}
.pl-img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover; display: block;
}
.pl-img-placeholder {
    width: 100%; height: 100%;
    background: #f3f4f6;
    display: flex; align-items: center; justify-content: center;
}
.pl-status {
    position: absolute; top: 10px; left: 10px;
    padding: 3px 10px; border-radius: 20px;
    font-size: 0.7rem; font-weight: 700; letter-spacing: .04em;
}
.pl-price {
    position: absolute; bottom: 10px; right: 10px;
    padding: 4px 10px; border-radius: 20px;
    font-size: 0.76rem; font-weight: 700;
    background: rgba(0,0,0,.55); color: #fff;
}
.pl-body { padding: 12px 14px 8px; flex: 1; }
.pl-name {
    font-weight: 700; font-size: 0.88rem; color: #111827;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-bottom: 3px;
}
.pl-addr {
    font-size: 0.75rem; color: #9ca3af;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.pl-footer {
    padding: 9px 12px 11px;
    border-top: 1px solid #f3f4f6;
    display: flex; flex-wrap: wrap; gap: 5px;
}
.pl-btn {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 0.73rem; font-weight: 600;
    padding: 4px 9px; border-radius: 6px;
    border: none; cursor: pointer;
    text-decoration: none; transition: background .15s;
    white-space: nowrap; line-height: 1.4;
}
.pl-btn:hover { text-decoration: none; }
.pl-btn-edit    { background: #eff6ff; color: #2563eb; }
.pl-btn-edit:hover { background: #dbeafe; color: #2563eb; }
.pl-btn-preview { background: #f0fdf4; color: #16a34a; }
.pl-btn-preview:hover { background: #dcfce7; color: #16a34a; }
.pl-btn-share   { background: #f5f3ff; color: #7c3aed; }
.pl-btn-share:hover { background: #ede9fe; color: #7c3aed; }
.pl-btn-publish { background: #eff6ff; color: #0284c7; }
.pl-btn-publish:hover { background: #bae6fd; color: #0284c7; }
.pl-btn-delete  { background: #fef2f2; color: #dc2626; }
.pl-btn-delete:hover { background: #fee2e2; color: #dc2626; }
.pl-empty {
    text-align: center; padding: 40px 24px;
    background: #fff; border-radius: 12px;
    border: 1px dashed #e5e7eb; color: #9ca3af;
}
</style>

    {{-- Page heading --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:1.5rem;">
        <h3 style="margin:0;" class="page-heading">Your Properties</h3>
        <a href="{{ url('agent/property/address') }}" class="button font-bold text-base mb-0" style="background-color:rgb(0,100,131);">
            <i class="fa fa-plus mr-1"></i> New Property
        </a>
    </div>

    {{-- Demo: live example banner --}}
    @if(session('demo_session_id'))
    <div style="display:flex;align-items:stretch;gap:0;border-radius:12px;overflow:hidden;border:1px solid #bfdbfe;margin-bottom:1.5rem;box-shadow:0 2px 8px rgba(37,99,235,.08);">
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

    {{-- Property grid --}}
    @if($properties->count() > 0)
        <div class="pl-grid">
            @foreach($properties as $row)
                <div class="pl-card">

                    {{-- Image --}}
                    <div class="pl-img-wrap">
                        @if($row->property_images->first())
                            <img src="{{ asset_s3($row->property_images->first()->thumb) }}" alt="{{ $row->name }}">
                        @else
                            <div class="pl-img-placeholder">
                                <i class="fa fa-home" style="font-size:2.2rem;color:#d1d5db;"></i>
                            </div>
                        @endif
                        <span class="pl-status"
                              style="{{ $row->published ? 'background:rgba(220,252,231,0.95);color:#15803d;' : 'background:rgba(254,249,195,0.95);color:#92400e;' }}">
                            {{ $row->published ? 'Published' : 'Draft' }}
                        </span>
                        @if(!empty($row->price))
                            <span class="pl-price">${{ $row->price }}</span>
                        @endif
                    </div>

                    {{-- Body --}}
                    <div class="pl-body">
                        <div class="pl-name">{{ $row->name ?: $row->address_line_1 }}</div>
                        <div class="pl-addr">
                            <i class="fa fa-map-marker-alt" style="margin-right:3px;"></i>{{ $row->address_line_1 }}{{ !empty($row->city) ? ', '.$row->city : '' }}
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pl-footer">
                        <a href="{{ url('agent/property/address/'.$row->id) }}" class="pl-btn pl-btn-edit">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ url($row->unique_url) }}" target="_blank" class="pl-btn pl-btn-preview">
                            <i class="fa fa-eye"></i> Preview
                        </a>
                        @if(!$row->published)
                            <a href="{{ url('share/'.$row->unique_url) }}" target="_blank" class="pl-btn pl-btn-share">
                                <i class="fa fa-share-alt"></i> Share
                            </a>
                            <button wire:click="publishProperty({{ $row->id }})" class="pl-btn pl-btn-publish">
                                <i class="fa fa-globe"></i> Publish
                            </button>
                            <button type="button" @click="confirmDeleteId = {{ $row->id }}" class="pl-btn pl-btn-delete">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div style="display:flex;justify-content:center;padding:8px 0 4px;">
            {{ $properties->links() }}
        </div>
    @else
        <div class="pl-empty">
            <i class="fa fa-home" style="font-size:2.2rem;margin-bottom:10px;display:block;"></i>
            <p style="font-weight:600;color:#6b7280;margin-bottom:3px;">No properties yet</p>
            <p style="font-size:0.82rem;margin:0;">Click "New Property" to create your first listing.</p>
        </div>
    @endif

    {{-- Delete Confirmation --}}
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

    {{-- Publish Confirmation --}}
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
