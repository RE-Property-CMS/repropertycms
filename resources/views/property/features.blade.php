<section class="bg-white">
    <div class="container-fluid px-0">
        <div class="row m-0 p-0">
            @if(!is_null($property->bedroom))
                @if(!empty($property->bedroom_image))
                    <div class="col-6 col-md-4 p-0 order-0">
                        <img src="{{asset_s3($property->bedroom_image)}}" class="feature-property-img" alt="" loading="lazy">
                    </div>
                @else
                    <div class="col-6 col-md-4 p-0 order-0">
                        <img src="{{asset('images/placeholder-bedroom.jpg')}}" class="feature-property-img" alt="" loading="lazy">
                    </div>
                @endif

                <div class="col-6 col-md-4 p-0 bg-grey text-white order-1">
                    @if(count($property_images) > 0)
                        <p class="fs-2 property-feature">{{ $property->bedroom }} BEDROOMS</p>
                    @endif
                </div>
            @endif

            @if(!is_null($property->bathroom))
                @if(!empty($property->bathroom_image))
                    <div class="col-6 col-md-4 p-0 order-3 order-md-2">
                        <img src="{{asset_s3($property->bathroom_image)}}" class="feature-property-img" alt="" loading="lazy">
                    </div>
                @else
                    <div class="col-6 col-md-4 p-0 order-3 order-md-2">
                        <img src="{{asset('images/placeholder-fireplace-room.jpg')}}" class="feature-property-img" alt="" loading="lazy">
                    </div>
                @endif

                <div class="col-6 col-md-4 p-0 bg-yellow-200 text-white order-2 order-md-3">
                    @if(count($property_images) > 0)
                        <p class="fs-2 property-feature">{{ $property->bathroom }} BATHS</p>
                    @endif
                </div>
            @endif

            @if(!is_null( $property->levels ))
                @if(!empty($property->levels_image))
                    <div class="col-6 col-md-4 p-0 order-4">
                        <img src="{{asset_s3($property->levels_image)}}" class="feature-property-img" alt="" loading="lazy">
                    </div>
                @else
                    <div class="col-6 col-md-4 p-0 order-4">
                        <img src="{{asset('images/placeholder-living-room.jpg')}}" class="feature-property-img" alt="" loading="lazy">
                    </div>
                @endif

                <div class="col-6 col-md-4 p-0 bg-dark text-white order-5">
                    @if(count($property_images) > 0)
                        <!-- <p class="fs-2 property-feature">{{ $property->levels }} LEVELS</p>  -->
                        <p class="fs-2 property-feature">{{ $property->property_area != '' ? $property->property_area : '0.00' }}
                            SQ. FT</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>