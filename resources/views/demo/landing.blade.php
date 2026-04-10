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

    {{-- Expired notice --}}
    @if($expired)
        <div class="max-w-2xl mx-auto px-6 mb-4">
            <div class="bg-amber-500/20 border border-amber-400/30 rounded-xl px-5 py-4 flex items-center gap-3">
                <i class="fa fa-clock text-amber-400"></i>
                <p class="text-amber-200 text-sm">Your demo session expired after 60 minutes. Start a new one below.</p>
            </div>
        </div>
    @endif

    {{-- Info notice --}}
    @if(session('info'))
        <div class="max-w-2xl mx-auto px-6 mb-4">
            <div class="bg-blue-500/20 border border-blue-400/30 rounded-xl px-5 py-4 flex items-center gap-3">
                <i class="fa fa-info-circle text-blue-400"></i>
                <p class="text-blue-200 text-sm">{{ session('info') }}</p>
            </div>
        </div>
    @endif

    {{-- Hero --}}
    <main class="max-w-6xl mx-auto px-6 py-12">
        <div class="text-center mb-14">
            <div class="inline-flex items-center gap-2 bg-blue-500/20 border border-blue-400/30 rounded-full px-4 py-2 text-blue-300 text-sm mb-6">
                <span class="pulse-dot w-2 h-2 bg-blue-400 rounded-full"></span>
                Live Interactive Demo — No sign-up required
            </div>
            <h1 class="text-5xl font-bold mb-5 leading-tight">
                Experience the Full System<br>
                <span class="text-blue-400">in 60 Minutes</span>
            </h1>
            <p class="text-gray-300 text-xl max-w-2xl mx-auto leading-relaxed">
                Get your own private sandbox. Play as Admin, Agent, or Buyer — all three roles, complete isolation, automatically cleaned up.
            </p>
        </div>

        {{-- Role cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-14">
            <div class="card-hover bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-6">
                <div class="w-12 h-12 bg-purple-500/30 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa fa-shield-halved text-purple-400 text-xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">Super Admin</h3>
                <p class="text-gray-300 text-sm leading-relaxed">Manage agents, create subscription plans, configure settings, and view revenue dashboards.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="text-xs bg-purple-500/20 text-purple-300 rounded-full px-3 py-1">Dashboard</span>
                    <span class="text-xs bg-purple-500/20 text-purple-300 rounded-full px-3 py-1">Plans</span>
                    <span class="text-xs bg-purple-500/20 text-purple-300 rounded-full px-3 py-1">Settings</span>
                </div>
            </div>

            <div class="card-hover bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-6">
                <div class="w-12 h-12 bg-blue-500/30 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa fa-user-tie text-blue-400 text-xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">Real Estate Agent</h3>
                <p class="text-gray-300 text-sm leading-relaxed">Create property listings, upload images, manage floor plans, videos, documents and track inquiries.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="text-xs bg-blue-500/20 text-blue-300 rounded-full px-3 py-1">Listings</span>
                    <span class="text-xs bg-blue-500/20 text-blue-300 rounded-full px-3 py-1">Media</span>
                    <span class="text-xs bg-blue-500/20 text-blue-300 rounded-full px-3 py-1">Billing</span>
                </div>
            </div>

            <div class="card-hover bg-white/10 backdrop-blur border border-white/20 rounded-2xl p-6">
                <div class="w-12 h-12 bg-green-500/30 rounded-xl flex items-center justify-center mb-4">
                    <i class="fa fa-magnifying-glass-dollar text-green-400 text-xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">Property Buyer</h3>
                <p class="text-gray-300 text-sm leading-relaxed">Browse the public property page — view gallery, floor plans, virtual tour, and send an inquiry.</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="text-xs bg-green-500/20 text-green-300 rounded-full px-3 py-1">Gallery</span>
                    <span class="text-xs bg-green-500/20 text-green-300 rounded-full px-3 py-1">Floor Plans</span>
                    <span class="text-xs bg-green-500/20 text-green-300 rounded-full px-3 py-1">Inquiry</span>
                </div>
            </div>
        </div>

        {{-- CTA --}}
        <div class="text-center">
            <form method="POST" action="{{ route('demo.start') }}" class="inline-block">
                @csrf
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-400 text-white font-bold text-lg px-12 py-5 rounded-2xl transition-all duration-200 shadow-2xl shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-105">
                    <i class="fa fa-play mr-3"></i>
                    Start My Private Demo
                </button>
            </form>
            <p class="text-gray-400 text-sm mt-4">
                <i class="fa fa-clock mr-1"></i> 60-minute session &nbsp;&bull;&nbsp;
                <i class="fa fa-trash mr-1"></i> Auto-deleted after expiry &nbsp;&bull;&nbsp;
                <i class="fa fa-lock mr-1"></i> Fully isolated
            </p>
        </div>

        {{-- What's pre-loaded --}}
        <div class="mt-16 bg-white/5 border border-white/10 rounded-2xl p-8">
            <h2 class="text-xl font-bold mb-6 text-center">What's pre-loaded in your sandbox</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div>
                    <div class="text-3xl font-bold text-blue-400">1</div>
                    <div class="text-gray-300 text-sm mt-1">Admin Account</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-blue-400">1</div>
                    <div class="text-gray-300 text-sm mt-1">Agent Account</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-blue-400">2</div>
                    <div class="text-gray-300 text-sm mt-1">Live Properties</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-blue-400">1</div>
                    <div class="text-gray-300 text-sm mt-1">Active Plan</div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
