<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('code', 'Error') &mdash; @yield('title', 'Something went wrong')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root { --err: @yield('color', '#4f46e5'); }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .err-wrap {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
            text-align: center;
            width: 100%;
        }

        /* giant ghost number behind card */
        .err-ghost {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 38vw;
            font-weight: 900;
            color: rgba(0,0,0,.04);
            user-select: none;
            pointer-events: none;
            line-height: 1;
            white-space: nowrap;
        }

        .err-card {
            position: relative;
            z-index: 1;
            background: #fff;
            border-radius: 24px;
            padding: 3rem 2.75rem 2.5rem;
            box-shadow: 0 8px 48px rgba(0,0,0,.10);
            max-width: 500px;
            width: 100%;
            border-top: 5px solid var(--err);
        }

        .err-logo {
            width: 72px;
            height: auto;
            margin: 0 auto 1.75rem;
            display: block;
        }

        .err-icon-wrap {
            width: 68px;
            height: 68px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 1.65rem;
            color: #fff;
            background: var(--err);
            box-shadow: 0 6px 20px rgba(0,0,0,.15);
        }

        .err-code {
            font-size: 4.5rem;
            font-weight: 900;
            color: var(--err);
            line-height: 1;
            margin-bottom: 0.4rem;
            letter-spacing: -2px;
        }

        .err-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.75rem;
        }

        .err-desc {
            font-size: 0.875rem;
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .err-divider {
            width: 48px;
            height: 3px;
            background: var(--err);
            border-radius: 99px;
            margin: 0 auto 1.25rem;
            opacity: .35;
        }

        .err-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .err-btn {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .err-btn-primary {
            background: var(--err);
            color: #fff;
            border-color: var(--err);
        }
        .err-btn-primary:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,.18);
        }

        .err-btn-ghost {
            background: transparent;
            color: var(--err);
            border-color: var(--err);
        }
        .err-btn-ghost:hover {
            background: var(--err);
            color: #fff;
            transform: translateY(-2px);
        }

        .err-footer {
            margin-top: 2rem;
            font-size: 0.72rem;
            color: #9ca3af;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 480px) {
            .err-card { padding: 2.25rem 1.5rem 2rem; border-radius: 16px; }
            .err-code  { font-size: 3.5rem; }
        }
    </style>
</head>
<body>

<div class="err-wrap">

    <div class="err-ghost">@yield('code', '?')</div>

    <div class="err-card">
        <img src="{{ asset('images/logo-placeholder-small.png') }}" class="err-logo" alt="Logo">

        <div class="err-icon-wrap">
            <i class="fa @yield('icon', 'fa-circle-exclamation')"></i>
        </div>

        <p class="err-code">@yield('code', '???')</p>
        <h1 class="err-title">@yield('title', 'Something went wrong')</h1>
        <div class="err-divider"></div>
        <p class="err-desc">@yield('description', 'An unexpected error occurred. Please try again.')</p>

        @php
            $ref = request()->server('HTTP_REFERER', '');
            $homeUrl = str_contains($ref, '/admin') ? route('admin.dashboard') : url('/');
        @endphp

        <div class="err-actions">
            <a href="{{ $homeUrl }}" class="err-btn err-btn-primary">
                <i class="fa fa-house"></i> Go Home
            </a>
            <a href="javascript:history.back()" class="err-btn err-btn-ghost">
                <i class="fa fa-arrow-left"></i> Go Back
            </a>
        </div>
    </div>

    <p class="err-footer">&copy; {{ date('Y') }} {{ config('app.name') }} &mdash; All rights reserved</p>

</div>

</body>
</html>
