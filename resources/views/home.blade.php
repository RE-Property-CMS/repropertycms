<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} — White-Label Real Estate CMS</title>
    <meta name="description" content="A complete white-label real estate CMS built on Laravel. Full source code, self-hosted, Stripe billing, agent portal, admin dashboard, and rich property media. One-time purchase.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background: #050d1a; }

        .hero-bg { background: #050d1a; }
        #hero-canvas { position: absolute; inset: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; display: block; opacity: 0.1; }

        .gradient-text {
            background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 50%, #34d399 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glass {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            backdrop-filter: blur(12px);
        }

        .glass-hover { transition: all 0.25s ease; }
        .glass-hover:hover {
            background: rgba(255,255,255,0.07);
            border-color: rgba(59,130,246,0.3);
            transform: translateY(-3px);
            box-shadow: 0 20px 48px rgba(0,0,0,0.4);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            box-shadow: 0 0 32px rgba(59,130,246,0.35);
            transition: all 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            box-shadow: 0 0 52px rgba(59,130,246,0.55);
            transform: translateY(-2px);
        }

        .glow-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }

        .nav-link { color: rgba(255,255,255,0.6); transition: color 0.2s; }
        .nav-link:hover { color: #fff; }

        .icon-ring {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
        }

        .divider {
            border: none; height: 1px;
            background: linear-gradient(to right, transparent, rgba(255,255,255,0.07), transparent);
        }

        .badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(59,130,246,0.12);
            border: 1px solid rgba(59,130,246,0.25);
            border-radius: 999px;
            padding: 6px 16px;
            font-size: 13px;
            color: #93c5fd;
            font-weight: 500;
        }

        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:0.35} }
        .pulse-dot { animation: pulse-dot 2s infinite; }

        .reveal { opacity: 0; transform: translateY(22px); transition: opacity 0.55s ease, transform 0.55s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        .tag { font-size: 11px; padding: 3px 10px; border-radius: 999px; font-weight: 500; }

        nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; transition: all 0.3s; }
        .nav-scrolled {
            background: rgba(5,13,26,0.93) !important;
            border-bottom: 1px solid rgba(255,255,255,0.06) !important;
            backdrop-filter: blur(16px);
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #050d1a; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 3px; }
    </style>
</head>
<body class="text-white antialiased">

{{-- ========================= NAV ========================= --}}
<nav id="navbar" style="background:transparent; border-bottom:1px solid transparent;">
    <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">

        <a href="{{ route('home') }}" class="flex items-center gap-3 flex-shrink-0">
            @php $brand = cache('brand_settings'); @endphp
            @if($brand && $brand->logo_path && file_exists(public_path($brand->logo_path)))
                <img src="{{ asset($brand->logo_path) }}" alt="{{ config('app.name') }}" class="h-7 w-auto">
            @else
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i class="fa fa-home text-white text-sm"></i>
                </div>
                <span class="font-bold text-base text-white">{{ config('app.name') }}</span>
            @endif
        </a>

        <div class="hidden md:flex items-center gap-8">
            <a href="#features" class="nav-link text-sm font-medium">Features</a>
            <a href="#roles"    class="nav-link text-sm font-medium">Roles</a>
            <a href="#stack"    class="nav-link text-sm font-medium">Tech Stack</a>
            <a href="#demo-cta" class="nav-link text-sm font-medium">Demo</a>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}" class="hidden md:block text-sm text-white/50 hover:text-white transition-colors font-medium">Sign In</a>
            <a href="{{ route('demo.landing') }}" class="btn-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl">
                <i class="fa fa-play mr-2 text-xs"></i>Live Demo
            </a>
        </div>
    </div>
</nav>

{{-- ========================= HERO ========================= --}}
<section class="relative min-h-screen flex items-center hero-bg overflow-hidden pt-16">

    <canvas id="hero-canvas"></canvas>

    <div class="glow-orb w-[500px] h-[500px] bg-blue-600/18 -top-20 -left-24"></div>
    <div class="glow-orb w-96 h-96 bg-purple-600/14 top-40 right-[-60px]"></div>
    <div class="glow-orb w-64 h-64 bg-emerald-600/10 bottom-24 left-1/3"></div>

    <div class="relative max-w-7xl mx-auto px-6 py-24 text-center w-full">

        <div class="badge mb-8 mx-auto w-fit">
            <span class="pulse-dot w-2 h-2 bg-blue-400 rounded-full"></span>
            White-Label Real Estate Platform — Full Source Code
        </div>

        <h1 class="text-5xl md:text-7xl font-black leading-[1.08] tracking-tight mb-6">
            The CMS that runs<br>
            <span class="gradient-text">your real estate business</span>
        </h1>

        <p class="text-gray-300 text-xl md:text-2xl max-w-3xl mx-auto leading-relaxed mb-10 font-light">
            Agent portal. Admin dashboard. Stripe billing. Rich property media.<br class="hidden md:block">
            Self-hosted on <strong class="text-white font-semibold">your</strong> server — branded as <strong class="text-white font-semibold">your</strong> product.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-14">
            <a href="{{ route('demo.landing') }}"
               class="btn-primary text-white font-bold text-base px-10 py-4 rounded-2xl inline-flex items-center gap-3">
                <i class="fa fa-play text-sm"></i>
                Try Live Demo — Free, No Sign-Up
            </a>
            <a href="#features"
               class="glass text-white font-semibold text-base px-10 py-4 rounded-2xl inline-flex items-center gap-3 hover:border-white/20 transition-all">
                <i class="fa fa-layer-group text-sm text-blue-400"></i>
                Explore Features
            </a>
        </div>

        <div class="flex flex-wrap justify-center gap-x-8 gap-y-2 text-sm text-gray-400 mb-20">
            <span><i class="fa fa-clock mr-1.5 text-blue-400"></i>60-min demo session</span>
            <span><i class="fa fa-shield-halved mr-1.5 text-blue-400"></i>Fully isolated sandbox</span>
            <span><i class="fa fa-trash mr-1.5 text-blue-400"></i>Auto-deleted after expiry</span>
            <span><i class="fa fa-code mr-1.5 text-blue-400"></i>Laravel 11 + Livewire 3</span>
        </div>

        {{-- Stats bar --}}
        <div class="glass rounded-2xl p-8 max-w-4xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div>
                    <div class="text-4xl font-black text-blue-400">3</div>
                    <div class="text-gray-300 text-sm mt-1 font-medium">User Roles</div>
                    <div class="text-gray-500 text-xs mt-0.5">Admin · Agent · Buyer</div>
                </div>
                <div>
                    <div class="text-4xl font-black text-purple-400">14+</div>
                    <div class="text-gray-300 text-sm mt-1 font-medium">Property Features</div>
                    <div class="text-gray-500 text-xs mt-0.5">Images, Tours, Docs & more</div>
                </div>
                <div>
                    <div class="text-4xl font-black text-emerald-400">52+</div>
                    <div class="text-gray-300 text-sm mt-1 font-medium">UI Components</div>
                    <div class="text-gray-500 text-xs mt-0.5">Livewire reactive views</div>
                </div>
                <div>
                    <div class="text-4xl font-black text-amber-400">1×</div>
                    <div class="text-gray-300 text-sm mt-1 font-medium">Purchase</div>
                    <div class="text-gray-500 text-xs mt-0.5">Full source, yours forever</div>
                </div>
            </div>
        </div>

    </div>
</section>

<hr class="divider">

{{-- ========================= FEATURES ========================= --}}
<section id="features" class="relative py-28 overflow-hidden">

    <div class="glow-orb w-80 h-80 bg-blue-700/12 top-0 right-0"></div>

    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-16 reveal">
            <div class="badge mb-5 mx-auto w-fit">Core Features</div>
            <h2 class="text-4xl md:text-5xl font-black mb-5">Everything to run your platform</h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">Every feature a modern real estate business needs — built in, production-ready, fully customizable.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

            <div class="glass glass-hover rounded-2xl p-7 reveal">
                <div class="icon-ring bg-blue-500/15 mb-5">
                    <i class="fa fa-house-chimney text-blue-400 text-xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-3">Rich Property Listings</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">Create detailed listings with price, bedrooms, bathrooms, area, description, amenities, and a unique public URL — sharable via QR code.</p>
                <div class="flex flex-wrap gap-2">
                    <span class="tag bg-blue-500/15 text-blue-300">Unique URLs</span>
                    <span class="tag bg-blue-500/15 text-blue-300">QR Codes</span>
                    <span class="tag bg-blue-500/15 text-blue-300">Amenities</span>
                    <span class="tag bg-blue-500/15 text-blue-300">Status Control</span>
                </div>
            </div>

            <div class="glass glass-hover rounded-2xl p-7 reveal">
                <div class="icon-ring bg-purple-500/15 mb-5">
                    <i class="fa fa-photo-film text-purple-400 text-xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-3">Multi-Format Property Media</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">Drag-and-drop image upload, photo galleries, YouTube/Vimeo embeds, 360° panoramas via Pannellum, Matterport 3D tours, and PDF documents.</p>
                <div class="flex flex-wrap gap-2">
                    <span class="tag bg-purple-500/15 text-purple-300">360° Panorama</span>
                    <span class="tag bg-purple-500/15 text-purple-300">Matterport</span>
                    <span class="tag bg-purple-500/15 text-purple-300">Galleries</span>
                    <span class="tag bg-purple-500/15 text-purple-300">Documents</span>
                </div>
            </div>

            <div class="glass glass-hover rounded-2xl p-7 reveal">
                <div class="icon-ring bg-emerald-500/15 mb-5">
                    <i class="fa fa-map-location-dot text-emerald-400 text-xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-3">Interactive Floor Plans</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">Upload floor plan images and add clickable hotspots linked to room photos — giving buyers an intuitive way to explore property layout.</p>
                <div class="flex flex-wrap gap-2">
                    <span class="tag bg-emerald-500/15 text-emerald-300">Canvas Editor</span>
                    <span class="tag bg-emerald-500/15 text-emerald-300">Hotspots</span>
                    <span class="tag bg-emerald-500/15 text-emerald-300">Photo Links</span>
                </div>
            </div>

            <div class="glass glass-hover rounded-2xl p-7 reveal">
                <div class="icon-ring bg-sky-500/15 mb-5">
                    <i class="fa fa-user-tie text-sky-400 text-xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-3">Full Agent Portal</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">Agents manage their own listings, profile, address, social links, logo, and billing — all within a clean, modern Livewire-powered portal.</p>
                <div class="flex flex-wrap gap-2">
                    <span class="tag bg-sky-500/15 text-sky-300">Dashboard</span>
                    <span class="tag bg-sky-500/15 text-sky-300">Profile</span>
                    <span class="tag bg-sky-500/15 text-sky-300">Notifications</span>
                    <span class="tag bg-sky-500/15 text-sky-300">Billing</span>
                </div>
            </div>

            <div class="glass glass-hover rounded-2xl p-7 reveal">
                <div class="icon-ring bg-rose-500/15 mb-5">
                    <i class="fa fa-shield-halved text-rose-400 text-xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-3">Super Admin Panel</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">Full control over agents, subscription plans, brand identity, integrations (Stripe, mail, storage, reCAPTCHA), and platform-wide settings.</p>
                <div class="flex flex-wrap gap-2">
                    <span class="tag bg-rose-500/15 text-rose-300">Agent Mgmt</span>
                    <span class="tag bg-rose-500/15 text-rose-300">Plans</span>
                    <span class="tag bg-rose-500/15 text-rose-300">Revenue</span>
                    <span class="tag bg-rose-500/15 text-rose-300">Settings</span>
                </div>
            </div>

            <div class="glass glass-hover rounded-2xl p-7 reveal">
                <div class="icon-ring bg-amber-500/15 mb-5">
                    <i class="fa-brands fa-stripe text-amber-400 text-xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-3">Stripe Subscription Billing</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">Create monthly/yearly plans synced to Stripe Products. Agents subscribe via Checkout, webhooks automate lifecycle events, and credits are tracked.</p>
                <div class="flex flex-wrap gap-2">
                    <span class="tag bg-amber-500/15 text-amber-300">Checkout</span>
                    <span class="tag bg-amber-500/15 text-amber-300">Webhooks</span>
                    <span class="tag bg-amber-500/15 text-amber-300">Credits</span>
                    <span class="tag bg-amber-500/15 text-amber-300">Plans</span>
                </div>
            </div>

            <div class="glass glass-hover rounded-2xl p-7 reveal">
                <div class="icon-ring bg-violet-500/15 mb-5">
                    <i class="fa fa-palette text-violet-400 text-xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-3">White-Label & Branding</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">Set your logo, favicon, primary/secondary colors, and fonts through the admin panel. CSS variables cascade globally — no code edits needed.</p>
                <div class="flex flex-wrap gap-2">
                    <span class="tag bg-violet-500/15 text-violet-300">Logo Upload</span>
                    <span class="tag bg-violet-500/15 text-violet-300">Color Pickers</span>
                    <span class="tag bg-violet-500/15 text-violet-300">Fonts</span>
                    <span class="tag bg-violet-500/15 text-violet-300">CSS Variables</span>
                </div>
            </div>

            <div class="glass glass-hover rounded-2xl p-7 reveal">
                <div class="icon-ring bg-teal-500/15 mb-5">
                    <i class="fa fa-map-pin text-teal-400 text-xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-3">Maps & Location</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">Google Maps integration for each property listing. Agents set address and coordinates — buyers see an embedded map on the public listing page.</p>
                <div class="flex flex-wrap gap-2">
                    <span class="tag bg-teal-500/15 text-teal-300">Google Maps</span>
                    <span class="tag bg-teal-500/15 text-teal-300">Coordinates</span>
                    <span class="tag bg-teal-500/15 text-teal-300">Public Embed</span>
                </div>
            </div>

            <div class="glass glass-hover rounded-2xl p-7 reveal">
                <div class="icon-ring bg-orange-500/15 mb-5">
                    <i class="fa fa-wand-magic-sparkles text-orange-400 text-xl"></i>
                </div>
                <h3 class="font-bold text-lg mb-3">10-Step Setup Wizard</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">Guided installer walks through requirements, database, admin account, mail, Stripe, storage, reCAPTCHA, and branding — no server skills needed.</p>
                <div class="flex flex-wrap gap-2">
                    <span class="tag bg-orange-500/15 text-orange-300">No CLI Required</span>
                    <span class="tag bg-orange-500/15 text-orange-300">AJAX Tests</span>
                    <span class="tag bg-orange-500/15 text-orange-300">Skippable Steps</span>
                </div>
            </div>

        </div>
    </div>
</section>

<hr class="divider">

{{-- ========================= ROLES ========================= --}}
<section id="roles" class="relative py-28 overflow-hidden">

    <div class="glow-orb w-96 h-96 bg-purple-700/11 bottom-0 left-0"></div>

    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-16 reveal">
            <div class="badge mb-5 mx-auto w-fit">Three Roles, One Platform</div>
            <h2 class="text-4xl md:text-5xl font-black mb-5">Built for everyone in the chain</h2>
            <p class="text-gray-400 text-lg max-w-2xl mx-auto">Each role gets a purpose-built interface — no shared screens, no confusion.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="glass rounded-2xl overflow-hidden reveal">
                <div class="bg-gradient-to-br from-rose-900/40 to-purple-900/20 p-8 border-b border-white/5">
                    <div class="w-14 h-14 bg-rose-500/20 rounded-2xl flex items-center justify-center mb-5">
                        <i class="fa fa-shield-halved text-rose-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Super Admin</h3>
                    <p class="text-gray-300 text-sm leading-relaxed">The platform owner. Controls every aspect — agents, plans, revenue, configuration, and brand identity.</p>
                </div>
                <div class="p-8 space-y-3">
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-rose-400 mt-0.5 flex-shrink-0"></i>
                        Manage agents — approve, view profiles, monitor activity
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-rose-400 mt-0.5 flex-shrink-0"></i>
                        Create subscription plans synced to Stripe Products
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-rose-400 mt-0.5 flex-shrink-0"></i>
                        View revenue dashboard and subscription metrics
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-rose-400 mt-0.5 flex-shrink-0"></i>
                        Configure brand identity, mail, storage, and API keys
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-rose-400 mt-0.5 flex-shrink-0"></i>
                        Manage content pages and public site copy
                    </div>
                </div>
            </div>

            <div class="glass rounded-2xl overflow-hidden reveal" style="transition-delay:0.1s">
                <div class="bg-gradient-to-br from-blue-900/40 to-sky-900/20 p-8 border-b border-white/5">
                    <div class="w-14 h-14 bg-blue-500/20 rounded-2xl flex items-center justify-center mb-5">
                        <i class="fa fa-user-tie text-blue-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Real Estate Agent</h3>
                    <p class="text-gray-300 text-sm leading-relaxed">The listing creator. Manages their own property portfolio, subscription, and public-facing profile.</p>
                </div>
                <div class="p-8 space-y-3">
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-blue-400 mt-0.5 flex-shrink-0"></i>
                        Create and manage property listings end-to-end
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-blue-400 mt-0.5 flex-shrink-0"></i>
                        Upload images, videos, floor plans, 360° tours, documents
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-blue-400 mt-0.5 flex-shrink-0"></i>
                        Configure topbar (image / video / slider) per property
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-blue-400 mt-0.5 flex-shrink-0"></i>
                        Receive buyer inquiries via email notifications
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-blue-400 mt-0.5 flex-shrink-0"></i>
                        Subscribe to plans and manage billing via Stripe
                    </div>
                </div>
            </div>

            <div class="glass rounded-2xl overflow-hidden reveal" style="transition-delay:0.2s">
                <div class="bg-gradient-to-br from-emerald-900/40 to-teal-900/20 p-8 border-b border-white/5">
                    <div class="w-14 h-14 bg-emerald-500/20 rounded-2xl flex items-center justify-center mb-5">
                        <i class="fa fa-magnifying-glass-dollar text-emerald-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Property Buyer</h3>
                    <p class="text-gray-300 text-sm leading-relaxed">The end customer. Browses a media-rich public listing page and sends an inquiry directly to the agent.</p>
                </div>
                <div class="p-8 space-y-3">
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-emerald-400 mt-0.5 flex-shrink-0"></i>
                        View image gallery, photo slider, and hero media
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-emerald-400 mt-0.5 flex-shrink-0"></i>
                        Explore interactive floor plans with room hotspots
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-emerald-400 mt-0.5 flex-shrink-0"></i>
                        Watch property videos and Matterport virtual tours
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-emerald-400 mt-0.5 flex-shrink-0"></i>
                        View property on Google Maps and check address
                    </div>
                    <div class="flex items-start gap-3 text-sm text-gray-300">
                        <i class="fa fa-check text-emerald-400 mt-0.5 flex-shrink-0"></i>
                        Send a contact inquiry — routed to the agent by email
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<hr class="divider">

{{-- ========================= TECH STACK ========================= --}}
<section id="stack" class="relative py-28 overflow-hidden">

    <div class="glow-orb w-72 h-72 bg-blue-600/10 top-10 right-10"></div>

    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- Left --}}
            <div class="reveal">
                <div class="badge mb-6 w-fit">Built on a proven stack</div>
                <h2 class="text-4xl md:text-5xl font-black mb-6 leading-tight">
                    Modern PHP.<br>
                    <span class="gradient-text">No compromises.</span>
                </h2>
                <p class="text-gray-400 text-lg leading-relaxed mb-8">
                    Built on Laravel 11 and Livewire 3 — the most productive PHP stack available today. Your developers feel right at home. Your ops team gets a standard Nginx + MySQL app.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-red-500/15 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa fa-code text-red-400"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-sm">Laravel 11 + PHP 8.2+</div>
                            <div class="text-gray-500 text-xs">Full MVC, Eloquent ORM, Artisan CLI, Sanctum API</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-blue-500/15 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa fa-bolt text-blue-400"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-sm">Livewire 3 + Tailwind CSS</div>
                            <div class="text-gray-500 text-xs">52+ reactive components, zero page reload UX</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-green-500/15 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa-brands fa-stripe text-green-400"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-sm">Stripe Payments + Webhooks</div>
                            <div class="text-gray-500 text-xs">Checkout, subscriptions, invoice lifecycle events</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-amber-500/15 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa fa-server text-amber-400"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-sm">MySQL + Local / AWS S3 Storage</div>
                            <div class="text-gray-500 text-xs">Switchable filesystem driver via .env — no code changes</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-purple-500/15 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa fa-envelope text-purple-400"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-sm">SendGrid + reCAPTCHA + Google Maps</div>
                            <div class="text-gray-500 text-xs">Pre-wired — just add your API keys in the wizard</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: ownership pillars --}}
            <div class="grid grid-cols-2 gap-4 reveal">
                <div class="glass rounded-2xl p-6 glass-hover col-span-2">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-emerald-500/15 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa fa-file-code text-emerald-400 text-lg"></i>
                        </div>
                        <div>
                            <div class="font-bold mb-1">Full Source Code Ownership</div>
                            <div class="text-gray-400 text-sm leading-relaxed">You receive every file — controllers, migrations, views, config. Modify, extend, or resell however you choose. No SaaS fees. No runtime license checks. No vendor lock-in.</div>
                        </div>
                    </div>
                </div>
                <div class="glass rounded-2xl p-6 glass-hover">
                    <div class="w-10 h-10 bg-blue-500/15 rounded-xl flex items-center justify-center mb-3">
                        <i class="fa fa-server text-blue-400"></i>
                    </div>
                    <div class="font-semibold text-sm mb-1">Self-Hosted</div>
                    <div class="text-gray-400 text-xs leading-relaxed">Your server, your DB, your rules. Deploy on shared hosting, VPS, or cloud.</div>
                </div>
                <div class="glass rounded-2xl p-6 glass-hover">
                    <div class="w-10 h-10 bg-violet-500/15 rounded-xl flex items-center justify-center mb-3">
                        <i class="fa fa-palette text-violet-400"></i>
                    </div>
                    <div class="font-semibold text-sm mb-1">Your Brand</div>
                    <div class="text-gray-400 text-xs leading-relaxed">Logo, colors, fonts — fully configurable without touching code.</div>
                </div>
                <div class="glass rounded-2xl p-6 glass-hover">
                    <div class="w-10 h-10 bg-rose-500/15 rounded-xl flex items-center justify-center mb-3">
                        <i class="fa fa-infinity text-rose-400"></i>
                    </div>
                    <div class="font-semibold text-sm mb-1">No Per-Agent Fees</div>
                    <div class="text-gray-400 text-xs leading-relaxed">Own the platform. Add unlimited agents. Keep all subscription revenue.</div>
                </div>
                <div class="glass rounded-2xl p-6 glass-hover">
                    <div class="w-10 h-10 bg-amber-500/15 rounded-xl flex items-center justify-center mb-3">
                        <i class="fa fa-wand-magic-sparkles text-amber-400"></i>
                    </div>
                    <div class="font-semibold text-sm mb-1">Guided Setup</div>
                    <div class="text-gray-400 text-xs leading-relaxed">10-step wizard — configure and go live in minutes, not days.</div>
                </div>
            </div>

        </div>
    </div>
</section>

<hr class="divider">

{{-- ========================= DEMO CTA ========================= --}}
<section id="demo-cta" class="relative py-36 overflow-hidden">

    <div class="glow-orb w-[700px] h-[400px] bg-blue-600/18 top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>

    <div class="max-w-4xl mx-auto px-6 text-center relative reveal">

        <div class="badge mb-8 mx-auto w-fit">
            <span class="pulse-dot w-2 h-2 bg-emerald-400 rounded-full"></span>
            Interactive Demo — Available Right Now
        </div>

        <h2 class="text-5xl md:text-6xl font-black mb-6 leading-tight">
            See it live before<br>
            <span class="gradient-text">you decide</span>
        </h2>

        <p class="text-gray-300 text-xl leading-relaxed mb-10 max-w-2xl mx-auto">
            Your own private sandbox — pre-loaded with an Admin account, an Agent account, and two property listings. Switch between all three roles freely. Everything resets automatically after 60 minutes.
        </p>

        <div class="grid grid-cols-3 gap-3 max-w-lg mx-auto mb-10">
            <div class="glass rounded-xl p-4 text-center">
                <i class="fa fa-shield-halved text-rose-400 text-xl mb-2 block"></i>
                <div class="text-xs font-semibold">Admin</div>
                <div class="text-gray-500 text-xs">Dashboard & Config</div>
            </div>
            <div class="glass rounded-xl p-4 text-center" style="border-color:rgba(59,130,246,0.4)">
                <i class="fa fa-user-tie text-blue-400 text-xl mb-2 block"></i>
                <div class="text-xs font-semibold">Agent</div>
                <div class="text-gray-500 text-xs">Listings & Billing</div>
            </div>
            <div class="glass rounded-xl p-4 text-center">
                <i class="fa fa-magnifying-glass-dollar text-emerald-400 text-xl mb-2 block"></i>
                <div class="text-xs font-semibold">Buyer</div>
                <div class="text-gray-500 text-xs">Public Property</div>
            </div>
        </div>

        <a href="{{ route('demo.landing') }}"
           class="btn-primary inline-flex items-center gap-3 text-white font-bold text-xl px-14 py-5 rounded-2xl">
            <i class="fa fa-play"></i>
            Start My Free Demo
        </a>

        <p class="text-gray-500 text-sm mt-5">
            No account &nbsp;·&nbsp; No credit card &nbsp;·&nbsp; Fully isolated &nbsp;·&nbsp; 60 minutes &nbsp;·&nbsp; Auto-deleted
        </p>

    </div>
</section>

{{-- ========================= FOOTER ========================= --}}
<footer class="border-t border-white/5 py-12">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                @if($brand && $brand->logo_path && file_exists(public_path($brand->logo_path)))
                    <img src="{{ asset($brand->logo_path) }}" alt="{{ config('app.name') }}" class="h-6 w-auto">
                @else
                    <div class="w-7 h-7 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fa fa-home text-white text-xs"></i>
                    </div>
                    <span class="font-bold text-sm text-white">{{ config('app.name') }}</span>
                @endif
                <span class="text-gray-600 text-sm">· White-Label Real Estate CMS</span>
            </div>
            <div class="flex items-center gap-6 text-sm text-gray-500">
                <a href="{{ route('demo.landing') }}" class="hover:text-white transition-colors">Live Demo</a>
                <a href="{{ route('termsAndConditions') }}" class="hover:text-white transition-colors">Terms</a>
                <a href="{{ route('login') }}" class="hover:text-white transition-colors">Sign In</a>
            </div>
            <div class="text-gray-600 text-sm">
                Built with Laravel 11 &nbsp;·&nbsp; PHP 8.2 &nbsp;·&nbsp; Livewire 3
            </div>
        </div>
    </div>
</footer>

<script>
    // Sticky nav
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('nav-scrolled', window.scrollY > 40);
    });

    // Scroll reveal
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
</script>

<script>
/* ── Hero shader animation (vanilla WebGL, no dependencies) ── */
(function () {
    var canvas = document.getElementById('hero-canvas');
    var gl = canvas.getContext('webgl') || canvas.getContext('experimental-webgl');
    if (!gl) return;

    var vertSrc = [
        'attribute vec2 pos;',
        'void main(){gl_Position=vec4(pos,0.0,1.0);}'
    ].join('\n');

    var fragSrc = [
        'precision highp float;',
        'uniform vec2  resolution;',
        'uniform float time;',
        'void main(void){',
        '  vec2 uv=(gl_FragCoord.xy*2.0-resolution.xy)/min(resolution.x,resolution.y);',
        '  float t=time*0.04;',
        '  float lw=0.003;',
        '  vec3 raw=vec3(0.0);',
        '  for(int j=0;j<3;j++){',
        '    for(int i=0;i<5;i++){',
        '      raw[j]+=lw*float(i*i)/abs(fract(t-0.01*float(j)+float(i)*0.01)*5.0-length(uv)+mod(uv.x+uv.y,0.2));',
        '    }',
        '  }',
        /* map raw RGB → brand palette: blue / purple / emerald */
        '  vec3 bCol=vec3(0.231,0.510,0.965);',  /* #3b82f6 */
        '  vec3 pCol=vec3(0.486,0.361,0.980);',  /* #7c5cf9 */
        '  vec3 eCol=vec3(0.063,0.725,0.506);',  /* #10b981 */
        '  vec3 glow=raw.r*bCol+raw.g*pCol+raw.b*eCol;',
        '  vec3 bg=vec3(0.020,0.051,0.102);',    /* #050d1a */
        '  float br=length(raw);',
        '  vec3 col=mix(bg,glow,clamp(br*0.9,0.0,1.0));',
        '  gl_FragColor=vec4(col,1.0);',
        '}'
    ].join('\n');

    function mkShader(type, src) {
        var s = gl.createShader(type);
        gl.shaderSource(s, src); gl.compileShader(s); return s;
    }
    var prog = gl.createProgram();
    gl.attachShader(prog, mkShader(gl.VERTEX_SHADER,   vertSrc));
    gl.attachShader(prog, mkShader(gl.FRAGMENT_SHADER, fragSrc));
    gl.linkProgram(prog); gl.useProgram(prog);

    var buf = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, buf);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([-1,-1, 1,-1, -1,1, -1,1, 1,-1, 1,1]), gl.STATIC_DRAW);
    var posLoc = gl.getAttribLocation(prog, 'pos');
    gl.enableVertexAttribArray(posLoc);
    gl.vertexAttribPointer(posLoc, 2, gl.FLOAT, false, 0, 0);

    var resLoc  = gl.getUniformLocation(prog, 'resolution');
    var timeLoc = gl.getUniformLocation(prog, 'time');

    function resize() {
        canvas.width  = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
        gl.viewport(0, 0, canvas.width, canvas.height);
        gl.uniform2f(resLoc, canvas.width, canvas.height);
    }
    resize();
    window.addEventListener('resize', resize);

    var t = 0;
    function render() {
        t += 0.015;
        gl.uniform1f(timeLoc, t);
        gl.drawArrays(gl.TRIANGLES, 0, 6);
        requestAnimationFrame(render);
    }
    render();
})();
</script>

</body>
</html>
