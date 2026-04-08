<!doctype html>
@php
    $property = session('property');
@endphp
<html lang="en" dir="ltr">

@include('admin.includes.head')

<body class="app sidebar-mini ltr light-mode admin-page">
@include('includes.preloader')
<!-- Flash message -->
@include('flash-message')

<!-- PAGE -->
<div class="page w-full">
    <!-- admin dashboard header -->
    <div class="agent_menu">
        @include('admin.includes.header')
    </div>
    <div class="main-content">
        <!-- admin content section -->
        @yield('content')
    </div>
</div>

<!-- admin dashboard footer -->
@include('admin.includes.footer')
<!-- admin dashboard foot -->
@include('admin.includes.foot')
@livewireScripts
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<x-livewire-alert::scripts/>
@stack('scripts')
</body>
</html>