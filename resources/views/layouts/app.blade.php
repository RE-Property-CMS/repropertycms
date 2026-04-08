<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <!-- Jquery-ui sortable library css -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />

        <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
        <!-- Jquery-ui sortable library js -->
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        <!-- Scripts -->

{{-- CSS only in head --}}
@vite('resources/css/app.css')

{{-- Livewire styles --}}
@livewireStyles

    <!-- Brand CSS variables (colors + fonts) -->
    @include('includes.brand-styles')

    </head>
    <body class="font-sans antialiased">
        @include('includes.preloader')
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
{{-- Livewire core JS (must be before your app.js) --}}
@livewireScripts

{{-- Your app JS after Livewire --}}
@vite('resources/js/app.js')

    </body>
</html>
