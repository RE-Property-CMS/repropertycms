@php
    $brand = cache()->remember('brand_settings', 3600, fn() => \App\Models\BrandSettings::first());
    $preloaderLogo = $brand?->logo_path ? asset($brand->logo_path) : asset('images/logo-placeholder-small.png');
@endphp

<div id="site-preloader">
    <img src="{{ $preloaderLogo }}" alt="Loading" class="preloader-logo">
    <div class="preloader-ring"></div>
</div>

<style>
#site-preloader {
    position: fixed;
    inset: 0;
    z-index: 99999;
    background: #0a0a12;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1.75rem;
    transition: opacity .55s ease, visibility .55s ease;
}
#site-preloader.preloader-done {
    opacity: 0;
    visibility: hidden;
}
.preloader-logo {
    width: 160px;
    height: auto;
    display: block;
    animation: preloaderBreath 2s ease-in-out infinite;
    filter: drop-shadow(0 0 18px rgba(255,255,255,.12));
}
@keyframes preloaderBreath {
    0%, 100% { transform: scale(1);    opacity: 1; }
    50%       { transform: scale(1.08); opacity: .8; }
}
.preloader-ring {
    width: 44px;
    height: 44px;
    border: 3px solid rgba(255,255,255,.08);
    border-top-color: var(--primary-color, #4f46e5);
    border-radius: 50%;
    animation: preloaderSpin .85s linear infinite;
}
@keyframes preloaderSpin {
    to { transform: rotate(360deg); }
}
</style>

<script>
(function () {
    window.addEventListener('load', function () {
        var el = document.getElementById('site-preloader');
        if (el) {
            el.classList.add('preloader-done');
            setTimeout(function () { el.style.display = 'none'; }, 600);
        }
    });
})();
</script>
