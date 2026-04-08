@props([
    'property'
])

<style>
    .prop-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        background: #f3f4f6;
        color: #374151;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
        white-space: nowrap;
    }
    .prop-action-btn:hover { background: #e5e7eb; color: #111827; text-decoration: none; }
    .prop-action-btn.edit   { background: #eff6ff; color: #2563eb; }
    .prop-action-btn.edit:hover { background: #dbeafe; }
    .prop-action-btn.preview { background: #f0fdf4; color: #16a34a; }
    .prop-action-btn.preview:hover { background: #dcfce7; }
    .prop-action-btn.share  { background: #f5f3ff; color: #7c3aed; }
    .prop-action-btn.share:hover { background: #ede9fe; }
    .prop-action-btn.publish { background: #eff6ff; color: #0284c7; }
    .prop-action-btn.publish:hover { background: #bae6fd; }
    .prop-action-btn.delete { background: #fef2f2; color: #dc2626; }
    .prop-action-btn.delete:hover { background: #fee2e2; }
    .prop-card {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e8e8e8;
        box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        background: #fff;
        transition: box-shadow 0.2s, transform 0.2s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .prop-card:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,0.12);
        transform: translateY(-2px);
    }
</style>

<div x-data="{ confirmDeleteId: null }">

    @if($property->count() > 0)
        <div class="row" style="row-gap: 20px;">
            @foreach($property as $row)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="prop-card">

                        {{-- Image --}}
                        <div style="position:relative;height:190px;overflow:hidden;flex-shrink:0;">
                            @if($row->property_images->first())
                                <img src="{{ asset_s3($row->property_images->first()->thumb) }}"
                                     alt="{{ $row->name }}"
                                     style="width:100%;height:100%;object-fit:cover;">
                            @else
                                <div style="width:100%;height:100%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;">
                                    <i class="fa fa-home" style="font-size:2.5rem;color:#d1d5db;"></i>
                                </div>
                            @endif

                            {{-- Status pill --}}
                            <span style="position:absolute;top:10px;left:10px;padding:3px 10px;border-radius:20px;font-size:0.7rem;font-weight:700;letter-spacing:0.4px;
                                {{ $row->published
                                    ? 'background:rgba(220,252,231,0.95);color:#15803d;'
                                    : 'background:rgba(254,249,195,0.95);color:#92400e;' }}">
                                {{ $row->published ? 'Published' : 'Draft' }}
                            </span>

                            {{-- Price --}}
                            @if(!empty($row->price))
                                <span style="position:absolute;bottom:10px;right:10px;padding:4px 10px;border-radius:20px;font-size:0.76rem;font-weight:700;background:rgba(0,0,0,0.55);color:#fff;backdrop-filter:blur(2px);">
                                    ${{ $row->price }}
                                </span>
                            @endif
                        </div>

                        {{-- Body --}}
                        <div style="padding:14px 14px 10px;flex:1;">
                            <h6 style="font-weight:700;margin-bottom:4px;font-size:0.88rem;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $row->name ?: $row->address_line_1 }}
                            </h6>
                            <p style="font-size:0.76rem;color:#9ca3af;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                <i class="fa fa-map-marker-alt" style="margin-right:4px;"></i>{{ $row->address_line_1 }}{{ !empty($row->city) ? ', ' . $row->city : '' }}
                            </p>
                        </div>

                        {{-- Actions --}}
                        <div style="padding:10px 12px 12px;border-top:1px solid #f3f4f6;display:flex;flex-wrap:wrap;gap:5px;">
                            <a href="{{ url('agent/property/address/' . $row->id) }}"
                               class="prop-action-btn edit">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="{{ url($row->unique_url) }}" target="_blank"
                               class="prop-action-btn preview">
                                <i class="fa fa-eye"></i> Preview
                            </a>
                            @if(!$row->published)
                                <a href="{{ url('share/' . $row->unique_url) }}" target="_blank"
                                   class="prop-action-btn share">
                                    <i class="fa fa-share-alt"></i> Share
                                </a>
                                <button wire:click="publishProperty({{ $row->id }})"
                                        class="prop-action-btn publish">
                                    <i class="fa fa-globe"></i> Publish
                                </button>
                                <button type="button" @click="confirmDeleteId = {{ $row->id }}"
                                        class="prop-action-btn delete">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            @endif
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align:center;padding:48px 24px;background:#fff;border-radius:12px;border:1px dashed #e5e7eb;">
            <i class="fa fa-home" style="font-size:2.5rem;color:#d1d5db;margin-bottom:12px;display:block;"></i>
            <p style="font-size:0.95rem;font-weight:600;color:#6b7280;margin-bottom:4px;">No properties here yet</p>
            <p style="font-size:0.82rem;color:#9ca3af;margin:0;">Properties will appear here once added.</p>
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
    <div x-show="$wire.confirmPublishId != null" x-cloak style="position:fixed;inset:0;z-index:9999;">
        <div style="position:absolute;inset:0;background:rgba(0,0,0,0.5);" @click="$wire.cancelConfirmPublish()"></div>
        <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
            <div style="position:relative;background:white;border-radius:12px;max-width:400px;width:90%;padding:24px;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
                <h3 style="font-size:1.1rem;font-weight:600;margin-bottom:12px;">Publish Property</h3>
                <p style="color:#6b7280;margin-bottom:20px;">Are you sure you want to publish this property? It will be visible to the public.</p>
                <div style="display:flex;gap:8px;justify-content:flex-end;">
                    <button type="button" @click="$wire.cancelConfirmPublish()" class="button button-grey">Cancel</button>
                    <button type="button" @click="$wire.doPublishProperty()" class="button button-green">Publish</button>
                </div>
            </div>
        </div>
    </div>

</div>
