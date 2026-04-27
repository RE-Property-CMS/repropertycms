<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Demo — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .gradient-bg { background: linear-gradient(135deg, #1e3a5f 0%, #0f2340 50%, #162d4a 100%); }
        .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.15); }
        .pulse-dot { animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    </style>
</head>
<body class="gradient-bg min-h-screen text-white">

    {{-- Header --}}
    <header class="px-6 py-5 flex items-center justify-between max-w-6xl mx-auto">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                <i class="fa fa-home text-white text-sm"></i>
            </div>
            <span class="font-bold text-lg">{{ config('app.name') }}</span>
        </div>
        <span class="text-sm text-blue-300 font-medium">Interactive Demo</span>
    </header>

    {{-- Hero --}}
    <main class="max-w-6xl mx-auto px-6 py-12">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 bg-blue-500/20 border border-blue-400/30 rounded-full px-4 py-2 text-blue-300 text-sm mb-6">
                <span class="pulse-dot w-2 h-2 bg-blue-400 rounded-full"></span>
                {{ sc('demo-landing','hero_badge','Live Interactive Demo — Credentials sent to your email') }}
            </div>
            <h1 class="text-5xl font-bold mb-5 leading-tight">
                {{ sc('demo-landing','hero_h1','Experience the Full System') }}<br>
                <span class="text-blue-400">{{ sc('demo-landing','hero_h2','in 60 Minutes') }}</span>
            </h1>
            <p class="text-gray-300 text-xl max-w-2xl mx-auto leading-relaxed">
                {{ sc('demo-landing','hero_desc','Get your own private sandbox. Play as Admin, Agent, or Buyer — all three roles, complete isolation, automatically cleaned up.') }}
            </p>
        </div>

        {{-- Role cards --}}
        @php
        $demoRoleDefaults = [
            1 => ['Super Admin','Manage agents, create subscription plans, configure settings, and view revenue dashboards.','Dashboard,Plans,Settings','purple'],
            2 => ['Real Estate Agent','Create property listings, upload images, manage floor plans, videos, documents and track inquiries.','Listings,Media,Billing','blue'],
            3 => ['Property Buyer','Browse the public property page — view gallery, floor plans, virtual tour, and send an inquiry.','Gallery,Floor Plans,Inquiry','green'],
        ];
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-14">
            @foreach($demoRoleDefaults as $i => $rd)
            @php
                $title = sc('demo-landing',"demo_role_{$i}_title", $rd[0]);
                $desc  = sc('demo-landing',"demo_role_{$i}_desc",  $rd[1]);
                $tags  = array_filter(array_map('trim', explode(',', sc('demo-landing',"demo_role_{$i}_tags", $rd[2]))));
                $col   = $rd[3];
            @endphp
            <div class="card-hover bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-6">
                <div class="w-12 h-12 bg-{{ $col }}-500/30 rounded-xl flex items-center justify-center mb-4">
                    @if($i===1)<i class="fa fa-shield-halved text-{{ $col }}-400 text-xl"></i>
                    @elseif($i===2)<i class="fa fa-user-tie text-{{ $col }}-400 text-xl"></i>
                    @else<i class="fa fa-magnifying-glass-dollar text-{{ $col }}-400 text-xl"></i>
                    @endif
                </div>
                <h3 class="font-bold text-lg mb-2">{{ $title }}</h3>
                <p class="text-gray-300 text-sm leading-relaxed">{{ $desc }}</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                        <span class="text-xs bg-{{ $col }}-500/20 text-{{ $col }}-300 rounded-full px-3 py-1">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- CTA form --}}
        <div class="flex justify-center">
            @include('demo.partials.landing-form', ['expired' => $expired])
        </div>

        {{-- What's pre-loaded --}}
        <div class="mt-16 bg-white/5 border border-white/10 rounded-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-center">
                {{ sc('demo-landing','sandbox_heading',"What's pre-loaded in your sandbox") }}
            </h2>
            @php
            $sandboxDefaults = [1=>['1','Admin Account'],2=>['1','Agent Account'],3=>['2','Live Properties'],4=>['1','Active Plan']];
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                @foreach($sandboxDefaults as $i => $sd)
                <div>
                    <div class="text-3xl font-bold text-blue-400">{{ sc('demo-landing',"sandbox_{$i}_num",$sd[0]) }}</div>
                    <div class="text-gray-300 text-sm mt-1">{{ sc('demo-landing',"sandbox_{$i}_label",$sd[1]) }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </main>

</body>
</html>
