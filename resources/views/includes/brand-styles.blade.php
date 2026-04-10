@php
    // Demo sessions store their own brand overrides in the PHP session so that
    // concurrent demo users are fully isolated from each other and from the real DB row.
    $demoBrand = session('demo_brand_settings');
    $brand = $demoBrand
        ? (object) $demoBrand
        : cache()->remember('brand_settings', 3600, fn() =>
            \Illuminate\Support\Facades\DB::table('brand_settings')->first()
        );
    $fonts = array_unique([
        $brand->font_body    ?? 'Lato',
        $brand->font_heading ?? 'Julius Sans One',
        $brand->font_admin   ?? 'Lato',
    ]);
    $googleFontsUrl = 'https://fonts.googleapis.com/css2?'
        . implode('&', array_map(fn($f) => 'family=' . str_replace(' ', '+', $f) . ':wght@300;400;700', $fonts))
        . '&display=swap';
@endphp
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="{{ $googleFontsUrl }}">
<style>
    :root {
        --primary-color:   {{ $brand->primary_color   ?? '#28ADC4' }};
        --secondary-color: {{ $brand->secondary_color ?? '#0069a6' }};
        --accent-color:    {{ $brand->accent_color    ?? '#008a8f' }};
        --accent-2-color:  {{ $brand->accent_2_color  ?? '#6f4693' }};
        --sidebar-color:   {{ $brand->sidebar_color   ?? '#152636' }};
        --font-body:    '{{ $brand->font_body    ?? 'Lato' }}', sans-serif;
        --font-heading: '{{ $brand->font_heading ?? 'Julius Sans One' }}', serif;
        --font-admin:   '{{ $brand->font_admin   ?? 'Lato' }}', sans-serif;
    }
</style>
@if(!empty($brand->favicon_path))
<link rel="icon" href="{{ asset($brand->favicon_path) }}">
@endif
