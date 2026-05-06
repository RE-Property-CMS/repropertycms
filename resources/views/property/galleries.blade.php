<style type="text/css">
    @media screen and (max-width: 576px) {
        .fotorama__caption {
            display: none;
        }
    }

    .fotorama__caption {
        top: 100px;
        left: 30px;
        right: 0;
        font-family: 'Helvetica Neue', Arial, sans-se;
        font-size: 17px;
        line-height: 1.5;
        color: #000;
        opacity: 0.7;
        width: 50%;
    }

    .fotorama__caption__wrap {
        background-color: rgb(0 0 0 / 50%);
        color: #FFF;
        padding: 30px;
    }

    .fotorama__stage {
        max-height: 90vh !important;
    }

    .fotorama__stage__frame {
        max-height: 90vh !important;
    }

    .fotorama__img {
        max-height: 90vh !important;
        object-fit: cover !important;
    }
</style>
<section class="">
    <div class="row m-0 p-0" wire:ignore>
        @foreach($property_gallery_details as $key => $property_detail)
            <h2 class="px-0 mb-5 text-center property-page-title site-color"
                style="margin-top: 40px">{{ $property_detail['gallery_name'] }}</h2>
            <div class="fotorama-deferred" data-nav="thumbs" data-allowfullscreen="native" data-fit="cover" data-width="100%">
                @foreach( $property_detail['images'] as $property_images_detail)
                    <a href="{{asset_s3($property_images_detail['file_name'])}}"
                       data-caption="{{ $property_detail['short_description'] }}"><img
                                src="{{asset_s3($property_images_detail['thumb_name'])}}"></a>
                @endforeach
            </div>
        @endforeach
    </div>
</section>

<script>
(function () {
    var observer = new IntersectionObserver(function (entries, obs) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                var el = entry.target;
                el.classList.remove('fotorama-deferred');
                el.classList.add('fotorama');
                obs.unobserve(el);
            }
        });
    }, { rootMargin: '200px' });

    document.querySelectorAll('.fotorama-deferred').forEach(function (el) {
        observer.observe(el);
    });
})();
</script>