<div class="col-md-12 p-0 position-relative main-banner">
    @if($property->main_section == \App\Enums\BannerType::Image)
        @if($property_topbar_images)
            <img src="{{asset_s3($property_topbar_images->file_name)}}" class="w-100" alt="">
        @endif
    @elseif($property->main_section == \App\Enums\BannerType::Slider)
        <x-property.slider :property_slider="$property_slider"/>
    @elseif($property->main_section == \App\Enums\BannerType::Video)
        <x-property.video :property="$property" :property_videos="$property_videos"/>
    @endif
    @if(!is_null($property->address_line_1) ||  !is_null($property->address_line_2) || $property->city || $property->zip)
        <div class="fs-2 p-4 text-center w-100 fixed-bottom position-absolute text-white top-tittle-caption text-uppercase"
             style="background-color: rgba(0,0,0,0.3);">
            <p class=" align-middle property-page-title">
                @if(!empty($property->address_line_1))
                    {{ $property->address_line_1 }},
                @endif

                @if(!empty($property->address_line_2))
                    {{ $property->address_line_2 }},
                @endif

                <br>

                @if(!empty($property->city))
                    {{ $property->city }},
                @endif

                {{ isset($state) ? $state->name : '' }}

                @if(!empty($property->zip))
                    {{ $property->zip }}
                @endif
            </p>
        </div>
    @endif
</div>