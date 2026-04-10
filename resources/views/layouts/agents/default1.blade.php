<!doctype html>
@php
    $property = session('property');
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('includes.agents.head')


<body class="app sidebar-mini ltr light-mode">
@include('includes.preloader')
<!-- Demo session banner (demo branch only) -->
@include('includes.demo-banner')
<!-- Flash message -->
@include('flash-message')

<!-- PAGE -->
<div class="page w-full">
    @if($property)
        @if($property['id'])
            <div id="agents_sidebar" class="overflow-auto ">
                <!-- admin dashboard sidebar -->
                @include('includes.agents.sidebar')
            </div>
            <!-- admin dashboard header -->
            <div class="agent_menu">
                @include('includes.agents.header')
            </div>
            <div class="default-page-content">
                <div class="address_bar">
                    <div class="w-full p-5">
                        <div class="pb-0">
                            <div class="d-flex align-items-center flex-wrap justify-content-between main-heading">
                                @if(!empty(session('property')->address_line_1) || !empty(session('property')->state) || !empty(session('property')->zip))
                                    <h3 class="mb-0 text-white">
                                        @if(!empty(session('property')->address_line_1))
                                            {{ session('property')->address_line_1 }},
                                        @endif
                                        @if(!empty(session('property')->city))
                                            {{ $property->city }},
                                        @endif

                                        @if(!empty(session('property')->zip))
                                            {{ session('property')->zip }}
                                        @endif
                                    </h3>
                                    <div class="col-span-2">
                                        <a href="{{url(session('property')->unique_url)}}"
                                           class="button button-cyan py-1 px-2 mb-1 block" target="_blank">
                                            <i class="fa fa-eye mr-2"></i> Property Preview
                                        </a>
                                        <a href="#"
                                           class="button button-blue py-1 px-2 mb-1 block" target="_blank">
                                            <i class="fa fa-eye mr-2"></i> Tutorials
                                        </a>
                                    </div>
                                @else
                                    <a class="nav-brand mr-auto ml-0"
                                       href="#">@php echo session('agent')->first_name . " " . session('agent')->last_name; @endphp</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- admin content section -->
                <div class="w-full px-5">
                    @yield('content')
                </div>
            </div>
        @endif
    @else
        <!-- admin dashboard header -->
        <div class="agent_menu">
            @include('includes.agents.header')
        </div>
        <div class="main-content">
            <!-- admin content section -->
            <div>
                @yield('content')
            </div>
        </div>
    @endif

</div>

<!-- admin dashboard footer -->
@include('includes.agents.footer')

<!-- admin dashboard foot -->
@include('includes.agents.foot')
<script src="https://js.sentry-cdn.com/ac2a864e10873b117bc3feac3ecd6fbe.min.js" crossorigin="anonymous"></script>
<script>
    Sentry.onLoad(function() {
      Sentry.init({
        environment: '{{ env('APP_ENV') ?? config('app.env', 'staging') }}', // Get environment from .env or config
        tracesSampleRate: 1.0, // optional
      });
    });
  </script>
@livewire('agent.plans')
@livewireScripts
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-livewire-alert::scripts/>
@stack('scripts')
</body>

</html>