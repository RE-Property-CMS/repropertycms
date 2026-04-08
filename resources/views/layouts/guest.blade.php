<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ! empty( $property ) ? $property->name : config('app.name') }} - Property Site</title>

    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
          rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js"
            integrity="sha512-7x3zila4t2qNycrtZ31HO0NnJr8kg2VI67YLoRSyi9hGhRN66FHYWr7Axa9Y1J9tGYHVBPqIjSE1ogHrJTz51g=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @livewireStyles

    <!-- Brand CSS variables (colors + fonts) -->
    @include('includes.brand-styles')

{{-- Shared JS (axios + CSRF guard) --}}
@if (file_exists(public_path('build/manifest.json')))
    {{-- Vite --}}
    @vite(['resources/js/app.js'])
@elseif (file_exists(public_path('mix-manifest.json')))
    {{-- Laravel Mix --}}
    <script src="{{ mix('js/app.js') }}" defer></script>
@endif

</head>

<body class="font-body">
@include('includes.preloader')

<!-- home section -->
<section class="bg-gray-800 py-10 md:mb-10">

    <div class="container max-w-screen-xl mx-auto px-4">

        <nav class="flex-wrap lg:flex items-center" x-data="{navbarOpen:false}">
            <div class="flex items-center mb-10 lg:mb-0">
                <img src="{{ asset('images/logo-placeholder.png') }}" width="150px" alt="Logo">

                <button class="lg:hidden w-10 h-10 ml-auto flex items-center justify-center border border-blue-500 text-blue-500 rounded-md"
                        @click="navbarOpen = !navbarOpen">
                    <i data-feather="menu"></i>
                </button>
            </div>

            <ul class="lg:flex flex-col lg:flex-row lg:items-center lg:mx-auto lg:space-x-8 xl:space-x-14"
                :class="{'hidden':!navbarOpen,'flex':navbarOpen}">
                <li class="font-semibold text-white hover:text-blue-800 transition ease-in-out duration-300 mb-5 lg:mb-0">
                    <a href="{{ url('/') }}">Home</a>
                </li>
                <li class="font-semibold text-white hover:text-blue-800 transition ease-in-out duration-300 mb-5 lg:mb-0">
                    <a href="{{ route('pricing') }}">Pricing</a>
                </li>
                <li class="font-semibold text-white hover:text-blue-800 transition ease-in-out duration-300 mb-5 lg:mb-0">
                    <a href="#">About us</a>
                </li>
            </ul>

            <div class="lg:flex flex-col md:flex-row md:items-center text-center md:space-x-6"
                 :class="{'hidden':!navbarOpen,'flex':navbarOpen}">
                <a href="{{ route('register') }}"
                   class="px-6 py-4 bg-blue-500 text-white font-semibold text-lg rounded-xl hover:bg-blue-700 transition ease-in-out duration-500 mb-5 md:mb-0">Become
                    an Agent</a>
            </div>
        </nav>

        <div class="flex flex-col lg:flex-row justify-between gap-10 my-20 ">
            <div class="text-center lg:text-left mt-10 lg:w-1/2">
                <h1 class="font-semibold text-white text-3xl md:text-6xl leading-normal mb-6">Get Started</h1>

                <p class="font-light text-gray-400 text-md md:text-lg leading-normal mb-12">Create and have access to
                    all our features for your Luxury Property Website.
                </p>

                <button class="px-6 py-4 bg-info font-semibold text-white text-lg rounded-xl hover:bg-blue-700 transition ease-in-out duration-500">
                    Get started
                </button>
            </div>

            <div class="mt-0 lg:w-1/2">
                <img src="{{ asset('images/auth-side-panel.jpg') }}" alt="Image" class="w-full h-full object-cover rounded-lg">
            </div>
        </div>

    </div> <!-- container.// -->

</section>
<!-- home section //end -->

{{ $slot }}

<footer class="bg-gray-800 py-16">

    <div class="container max-w-screen-xl mx-auto px-4">
        <div class="flex flex-col lg:flex-row lg:justify-between">

            <div class="space-y-7 mb-10 lg:mb-0">
                <div class="flex justify-center lg:justify-start">
                    <img src="{{ asset('images/logo-placeholder.png') }}" width="150px" alt="Image">
                </div>

                <p class="font-light text-gray-400 text-md md:text-lg text-center lg:text-left">{{ config('app.name') }}
                    &copy; {{ date('Y') }}</p>

                <div class="flex items-center justify-center lg:justify-start space-x-5">
                    <a href="#"
                       class="px-3 py-3 bg-gray-200 text-gray-700 rounded-full hover:bg-info hover:text-white transition ease-in-out duration-500">
                        <i data-feather="facebook"></i>
                    </a>

                    <a href="#"
                       class="px-3 py-3 bg-gray-200 text-gray-700 rounded-full hover:bg-info hover:text-white transition ease-in-out duration-500">
                        <i data-feather="youtube"></i>
                    </a>
                </div>
            </div>

            <div class="text-center lg:text-left space-y-7 mb-10 lg:mb-0">
                <h4 class="font-semibold text-white text-lg md:text-2xl">Quick links</h4>
                <a href="#"
                   class="block font-light text-gray-400 text-sm md:text-lg hover:text-blue-800 transition ease-in-out duration-300">About Us</a>

                <a href="#"
                   class="block font-light text-gray-400 text-sm md:text-lg hover:text-blue-800 transition ease-in-out duration-300">Contact</a>
            </div>

            <div class="text-center lg:text-left space-y-7 mb-10 lg:mb-0">
                <h4 class="font-semibold text-white text-lg md:text-2xl">Company</h4>

                <a href="{{ route('register') }}"
                   class="block font-light text-gray-400 text-sm md:text-lg hover:text-blue-800 transition ease-in-out duration-300">Signup</a>

                <a href="{{ route('login') }}"
                   class="block font-light text-gray-400 text-sm md:text-lg hover:text-blue-800 transition ease-in-out duration-300">Signin</a>

            </div>

            <div class="text-center lg:text-left space-y-7 mb-10 lg:mb-0">
                <h4 class="font-semibold text-white text-lg md:text-2xl">Legal</h4>

                <a href="{{ route('termsAndConditions') }}"
                   class="block font-light text-gray-400 text-sm md:text-lg hover:text-blue-800 transition ease-in-out duration-300">Privacy
                    Policy</a>

                <a href="{{ route('termsAndConditions') }}"
                   class="block font-light text-gray-400 text-sm md:text-lg hover:text-blue-800 transition ease-in-out duration-300">Terms
                    & Conditions</a>

                <a href="{{ route('termsAndConditions') }}"
                   class="block font-light text-gray-400 text-sm md:text-lg hover:text-blue-800 transition ease-in-out duration-300">DMCA</a>
            </div>

        </div>
    </div> <!-- container.// -->

</footer>

<script>
    feather.replace()
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
</body>
</html>
