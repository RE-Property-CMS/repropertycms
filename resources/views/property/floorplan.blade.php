<section class="floor-plans-main">
    <h2 class="px-0 py-5 text-center property-page-title site-color">Floor Plan Tour</h2>
    <div class="p-0">
        <div class="d-flex justify-content-center mb-4 flex-wrap">
            @foreach($property_floorplans as $property_floorplan)
                <button class="btn fw-bold btn @if($currentFloorplanTab == $property_floorplan->id) active @endif"
                        wire:click="floorplanTab({{ $property_floorplan->id }})">
                    {{ $property_floorplan->name }}
                </button>
            @endforeach
        </div>

        <!-- Scrollable container -->
        <div class="floor-plan-container">
            <div class="d-block floor-plans card-floor-plans">
                <div class="panel panel-primary h-auto p-3">
                    @foreach($property_floorplans as $property_floorplan)
                        <div class="floorplan fp-image" wire:key="{{ $property_floorplan->id }}"
                             @if($currentFloorplanTab != $property_floorplan->id) style="display: none;" @endif
                             id="floorplan{{ $property_floorplan->id }}">

                            <!-- Floor plan image with touch zoom & scroll -->
                            <div class="text-center fp-image">
                                <img src="{{ asset_s3($property_floorplan->file_name) }}"
                                     class="cursor-pointer img-fluid floorplan-img"
                                     style="touch-action: pan-x pan-y;" loading="lazy">
                                @foreach($property_floorplan->hotspots as $hotspot)
                                    <div class='photo-box' data-html='true' data-sanitize='false'
                                         data-areaid='{{ $hotspot->id }}' data-title='' data-toggle='popover'
                                         data-placement="top"
                                         title="{{ $hotspot->propertyImages()->count() }} photos. Click to view."
                                         style="top:{{ $hotspot->top }}%; left:{{ $hotspot->left }}%; width:{{ $hotspot->width }}%; height:{{ $hotspot->height }}%">
                                    </div>
                                    @foreach($hotspot->propertyImages as $property_image)
                                        <div class="fp-area-{{ $hotspot->id }}" style="display:none"
                                             data-photourl="{{ asset_s3($property_image->file_name) }}"
                                             data-caption=""></div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>
</section>
