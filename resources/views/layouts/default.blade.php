<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- TITLE -->
    <title>{{ ! empty( $property ) ? $property->name : config('app.name') }} - Property Site </title>

    <!-- add style for emded youtube video -->
    <x-embed-styles/>
    <!-- bootstrape Css -->
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">

    <!-- Font Awesome Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link href="{{('css/lightboxed.css')}}" rel="stylesheet"/>
    <!-- julicius font for property -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Julius+Sans+One&display=swap" rel="stylesheet">

    <!-- Custom css -->
    <link rel="stylesheet" href="{{asset('css/public-custom.css')}}"/>

    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/jssor.slider-28.1.0.min.js') }}" type="text/javascript"></script>
    <!-- Notify js -->
    <script src="{{ asset('js/notify.min.js') }}"></script>
    <script src="{{ asset('js/lightboxed.js')}}"></script>
    <!-- Bootstrape js -->
    {{--    <script src="{{ asset('js/bootstrap.min.js') }}"></script>--}}
    <!-- vimeo player js -->
    <script src="https://player.vimeo.com/api/player.js"></script>

    <script src="{{ asset('js/floor.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="{{ asset('plugins/magnific-popup.css')}}">

    <!-- //  Here handle the gallery lightbox section js start  -->
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-36251023-1']);
        _gaq.push(['_setDomainName', 'jqueryscript.net']);
        _gaq.push(['_trackPageview']);

        (function () {
            var ga = document.createElement('script');
            ga.type = 'text/javascript';
            ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
        })();

    </script>
    @livewireStyles
    <script>
        // try {
        //     fetch(new Request("https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js", { method: 'HEAD', mode: 'no-cors' })).then(function(response) {
        //         return true;
        //     }).catch(function(e) {
        //         var carbonScript = document.createElement("script");
        //         carbonScript.src = "//cdn.carbonads.com/carbon.js?serve=CK7DKKQU&placement=wwwjqueryscriptnet";
        //         carbonScript.id = "_carbonads_js";
        //         document.getElementById("carbon-block").appendChild(carbonScript);
        //     });
        // } catch (error) {
        //     console.log(error);
        // }
        $(document).ready(function () {
            // Add code for Adjust property gallery image div height
            let width = window.innerWidth;
            $(".gallery-default-image").css("height", parseInt(width) / 16 * 9 + 'px');
            $(window).on('resize', function () {
                $(".gallery-default-image").css("height", $(window).outerWidth() / 16 * 9 + 'px');
            });
        });
    </script>
    <!-- Here handle the gallery lightbox section js end -->

    <!-- Brand CSS variables (colors + fonts) -->
    @include('includes.brand-styles')

    @stack('styles')
</head>
<style>


</style>
<body>
@include('includes.preloader')
<div class="container-fluid p-0">

    <!-- Display Content -->
    @yield('content')

</div>
@stack('scripts')
<script src="{{ asset('plugins/magnific-popup.min.js')}}"></script>
<script>
    $(function () {
        $('.photo-box[data-toggle="popover"]').popover({
            trigger: "hover",
        });
        $(".card-floor-plans .photo-box").each(function () {
            var areaid = $(this).data("areaid");
            var photo_items = [];
            $(".fp-area-" + areaid).each(function (e) {
                var photourl = $(this).data("photourl");
                var caption = $(this).data("caption");
                if (photourl) {
                    photo_items.push({type: "image", src: photourl, title: caption});
                }
            });
            if (photo_items.length > 0) {
                $(this).magnificPopup({
                    items: photo_items,
                    gallery: {enabled: true},
                    closeOnContentClick: photo_items.length === 1 ? true : false,
                    mainClass: "mfp-fade"
                });
            }
        });
    });
    $(function () {
        $(".area-photos").each(function (idx, elem) {
            let items = $(elem).find("img");
            $(elem).magnificPopup({
                delegate: 'a',
                type: 'image',
                gallery: {enabled: true},
                closeOnContentClick: (items.length > 1) ? false : true,
                mainClass: "mfp-fade",
            });
        });
    });
</script>

<script src="https://js.sentry-cdn.com/ac2a864e10873b117bc3feac3ecd6fbe.min.js" crossorigin="anonymous"></script>
<script>
    Sentry.onLoad(function() {
      Sentry.init({
        environment: '{{ env('APP_ENV') ?? config('app.env', 'staging') }}', // Get environment from .env or config
        tracesSampleRate: 1.0, // optional
      });
    });
  </script>
@livewireScripts
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-livewire-alert::scripts/>
</body>
</html>