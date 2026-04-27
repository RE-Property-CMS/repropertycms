@extends('admin.layouts.default')

@section('title', 'Edit Content — ' . $page->title)

@section('content')
@php
    $c      = $content;
    $isHome = $page->key === 'home';
    $isDemo = $page->key === 'demo-landing';
    $tabs   = $isHome
        ? ['hero'=>'Hero','stats'=>'Stats','features'=>'Features','roles'=>'Roles','stack'=>'Tech Stack','cta'=>'Demo CTA','footer'=>'Footer']
        : ['hero'=>'Hero','roles'=>'Role Cards','stats'=>'Sandbox Stats','form'=>'Form Card'];
@endphp

<div style="max-width:980px;">

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #86efac;color:#166534;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;">
            <i class="fa fa-circle-check" style="margin-right:6px;"></i>{{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                <span style="font-size:11px;font-weight:700;background:#dbeafe;color:#1d4ed8;padding:2px 10px;border-radius:99px;letter-spacing:.04em;">
                    <i class="fa fa-lock fa-xs"></i> system
                </span>
                <h5 style="margin:0;">{{ $page->title }}</h5>
            </div>
            <p style="font-size:13px;color:#6b7280;margin:0;">Edit text content only. Layout and design are preserved.</p>
        </div>
        <a href="{{ route('admin.pages.lists') }}" class="btn-grey m-0">
            <i class="fa fa-arrow-left mr-1"></i> Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.pages.update', $page->id) }}">
        @csrf

        {{-- Published toggle --}}
        <div style="display:flex;align-items:center;gap:10px;background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:12px 18px;margin-bottom:20px;">
            <input type="checkbox" name="action" id="pub-toggle" value="1"
                   {{ ($page->action ?? true) ? 'checked' : '' }}
                   style="width:15px;height:15px;accent-color:#16a34a;cursor:pointer;flex-shrink:0;">
            <label for="pub-toggle" style="font-size:13px;font-weight:600;color:#166534;cursor:pointer;margin:0;background:none;">
                Published — visible on the live site
            </label>
        </div>

        {{-- ── Tab bar ── --}}
        <div style="display:flex;border-bottom:2px solid #e5e7eb;margin-bottom:0;overflow-x:auto;">
            @foreach($tabs as $key => $label)
            <button type="button" id="tab-btn-{{ $key }}"
                    onclick="ceTab('{{ $key }}')"
                    style="padding:10px 20px;font-size:13px;font-weight:600;color:#6b7280;border:none;background:transparent;border-bottom:2px solid transparent;margin-bottom:-2px;cursor:pointer;white-space:nowrap;transition:color .15s;{{ $loop->first ? 'color:#1d4ed8;border-bottom-color:#1d4ed8;' : '' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- ── Tab body ── --}}
        <div style="background:#fff;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 12px 12px;padding:28px;">

        @if($isHome)

            {{-- HERO --}}
            <div id="panel-hero" class="ce-panel">
                @include('admin.pages._section', ['title' => 'Hero Section'])
                <div class="row g-3">
                    <div class="col-12">@include('admin.pages._cf', ['l'=>'Badge Text','n'=>'hero_badge_text','v'=>$c['hero_badge_text']??'White-Label Real Estate Platform — Full Source Code'])</div>
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Headline Line 1','n'=>'hero_h1_line1','v'=>$c['hero_h1_line1']??'The CMS that runs'])</div>
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Headline Line 2 (gradient)','n'=>'hero_h1_line2','v'=>$c['hero_h1_line2']??'your real estate business'])</div>
                    <div class="col-12">@include('admin.pages._cf', ['l'=>'Sub-headline','n'=>'hero_subheading','v'=>$c['hero_subheading']??'Agent portal. Admin dashboard. Stripe billing. Rich property media. Self-hosted on your server — branded as your product.','ta'=>true])</div>
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Primary Button Text','n'=>'hero_btn_primary','v'=>$c['hero_btn_primary']??'Try Live Demo — Free, No Sign-Up'])</div>
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Secondary Button Text','n'=>'hero_btn_secondary','v'=>$c['hero_btn_secondary']??'Explore Features'])</div>
                    <div class="col-md-3">@include('admin.pages._cf', ['l'=>'Feature Pill 1','n'=>'hero_pill_1','v'=>$c['hero_pill_1']??'60-min demo session'])</div>
                    <div class="col-md-3">@include('admin.pages._cf', ['l'=>'Feature Pill 2','n'=>'hero_pill_2','v'=>$c['hero_pill_2']??'Fully isolated sandbox'])</div>
                    <div class="col-md-3">@include('admin.pages._cf', ['l'=>'Feature Pill 3','n'=>'hero_pill_3','v'=>$c['hero_pill_3']??'Auto-deleted after expiry'])</div>
                    <div class="col-md-3">@include('admin.pages._cf', ['l'=>'Feature Pill 4','n'=>'hero_pill_4','v'=>$c['hero_pill_4']??'Laravel 11 + Livewire 3'])</div>
                </div>
            </div>

            {{-- STATS --}}
            <div id="panel-stats" class="ce-panel" style="display:none;">
                @include('admin.pages._section', ['title' => 'Stats Bar'])
                @foreach([1=>['3','User Roles','Admin · Agent · Buyer'],2=>['14+','Property Features','Images, Tours, Docs & more'],3=>['52+','UI Components','Livewire reactive views'],4=>['1×','Purchase','Full source, yours forever']] as $i=>$d)
                @include('admin.pages._card', ['title' => "Stat {$i}"])
                <div class="row g-3">
                    <div class="col-md-3">@include('admin.pages._cf', ['l'=>'Number','n'=>"stat_{$i}_number",'v'=>$c["stat_{$i}_number"]??$d[0]])</div>
                    <div class="col-md-4">@include('admin.pages._cf', ['l'=>'Label','n'=>"stat_{$i}_label",'v'=>$c["stat_{$i}_label"]??$d[1]])</div>
                    <div class="col-md-5">@include('admin.pages._cf', ['l'=>'Sub-label','n'=>"stat_{$i}_sub",'v'=>$c["stat_{$i}_sub"]??$d[2]])</div>
                </div>
                </div>
                @endforeach
            </div>

            {{-- FEATURES --}}
            <div id="panel-features" class="ce-panel" style="display:none;">
                @include('admin.pages._section', ['title' => 'Features Section'])
                @include('admin.pages._card', ['title' => 'Section Header'])
                <div class="row g-3">
                    <div class="col-md-4">@include('admin.pages._cf', ['l'=>'Badge','n'=>'features_badge','v'=>$c['features_badge']??'Core Features'])</div>
                    <div class="col-md-8">@include('admin.pages._cf', ['l'=>'Heading','n'=>'features_heading','v'=>$c['features_heading']??'Everything to run your platform'])</div>
                    <div class="col-12">@include('admin.pages._cf', ['l'=>'Sub-heading','n'=>'features_subheading','v'=>$c['features_subheading']??'Every feature a modern real estate business needs — built in, production-ready, fully customizable.','ta'=>true])</div>
                </div>
                </div>
                @php $fd=[1=>['Rich Property Listings','Create detailed listings with price, bedrooms, bathrooms, area, description, amenities, and a unique public URL — sharable via QR code.'],2=>['Multi-Format Property Media','Drag-and-drop image upload, photo galleries, YouTube/Vimeo embeds, 360° panoramas via Pannellum, Matterport 3D tours, and PDF documents.'],3=>['Interactive Floor Plans','Upload floor plan images and add clickable hotspots linked to room photos — giving buyers an intuitive way to explore property layout.'],4=>['Full Agent Portal','Agents manage their own listings, profile, address, social links, logo, and billing — all within a clean, modern Livewire-powered portal.'],5=>['Super Admin Panel','Full control over agents, subscription plans, brand identity, integrations (Stripe, mail, storage, reCAPTCHA), and platform-wide settings.'],6=>['Stripe Subscription Billing','Create monthly/yearly plans synced to Stripe Products. Agents subscribe via Checkout, webhooks automate lifecycle events, and credits are tracked.'],7=>['White-Label & Branding','Set your logo, favicon, primary/secondary colors, and fonts through the admin panel. CSS variables cascade globally — no code edits needed.'],8=>['Maps & Location','Google Maps integration for each property listing. Agents set address and coordinates — buyers see an embedded map on the public listing page.'],9=>['10-Step Setup Wizard','Guided installer walks through requirements, database, admin account, mail, Stripe, storage, reCAPTCHA, and branding — no server skills needed.']]; @endphp
                @foreach($fd as $i=>$d)
                @include('admin.pages._card', ['title' => "Feature {$i} — {$d[0]}"])
                <div class="row g-3">
                    <div class="col-md-4">@include('admin.pages._cf', ['l'=>'Title','n'=>"feature_{$i}_title",'v'=>$c["feature_{$i}_title"]??$d[0]])</div>
                    <div class="col-md-8">@include('admin.pages._cf', ['l'=>'Description','n'=>"feature_{$i}_desc",'v'=>$c["feature_{$i}_desc"]??$d[1],'ta'=>true])</div>
                </div>
                </div>
                @endforeach
            </div>

            {{-- ROLES --}}
            <div id="panel-roles" class="ce-panel" style="display:none;">
                @include('admin.pages._section', ['title' => 'Roles Section'])
                @include('admin.pages._card', ['title' => 'Section Header'])
                <div class="row g-3">
                    <div class="col-md-4">@include('admin.pages._cf', ['l'=>'Badge','n'=>'roles_badge','v'=>$c['roles_badge']??'Three Roles, One Platform'])</div>
                    <div class="col-md-8">@include('admin.pages._cf', ['l'=>'Heading','n'=>'roles_heading','v'=>$c['roles_heading']??'Built for everyone in the chain'])</div>
                    <div class="col-12">@include('admin.pages._cf', ['l'=>'Sub-heading','n'=>'roles_subheading','v'=>$c['roles_subheading']??'Each role gets a purpose-built interface — no shared screens, no confusion.','ta'=>true])</div>
                </div>
                </div>
                @php $rd=[1=>['Super Admin','The platform owner. Controls every aspect — agents, plans, revenue, configuration, and brand identity.',"Manage agents — approve, view profiles, monitor activity\nCreate subscription plans synced to Stripe Products\nView revenue dashboard and subscription metrics\nConfigure brand identity, mail, storage, and API keys\nManage content pages and public site copy"],2=>['Real Estate Agent','The listing creator. Manages their own property portfolio, subscription, and public-facing profile.',"Create and manage property listings end-to-end\nUpload images, videos, floor plans, 360° tours, documents\nConfigure topbar (image / video / slider) per property\nReceive buyer inquiries via email notifications\nSubscribe to plans and manage billing via Stripe"],3=>['Property Buyer','The end customer. Browses a media-rich public listing page and sends an inquiry directly to the agent.',"View image gallery, photo slider, and hero media\nExplore interactive floor plans with room hotspots\nWatch property videos and Matterport virtual tours\nView property on Google Maps and check address\nSend a contact inquiry — routed to the agent by email"]]; @endphp
                @foreach($rd as $i=>$d)
                @include('admin.pages._card', ['title' => "Role {$i} — {$d[0]}"])
                <div class="row g-3">
                    <div class="col-md-4">@include('admin.pages._cf', ['l'=>'Title','n'=>"role_{$i}_title",'v'=>$c["role_{$i}_title"]??$d[0]])</div>
                    <div class="col-md-8">@include('admin.pages._cf', ['l'=>'Description','n'=>"role_{$i}_desc",'v'=>$c["role_{$i}_desc"]??$d[1],'ta'=>true])</div>
                    <div class="col-12">@include('admin.pages._cf', ['l'=>'Bullet Points (one per line)','n'=>"role_{$i}_bullets",'v'=>$c["role_{$i}_bullets"]??$d[2],'ta'=>true,'rows'=>6])</div>
                </div>
                </div>
                @endforeach
            </div>

            {{-- TECH STACK --}}
            <div id="panel-stack" class="ce-panel" style="display:none;">
                @include('admin.pages._section', ['title' => 'Tech Stack Section'])
                @include('admin.pages._card', ['title' => 'Section Header'])
                <div class="row g-3">
                    <div class="col-md-4">@include('admin.pages._cf', ['l'=>'Badge','n'=>'stack_badge','v'=>$c['stack_badge']??'Built on a proven stack'])</div>
                    <div class="col-md-4">@include('admin.pages._cf', ['l'=>'Heading Line 1','n'=>'stack_h1','v'=>$c['stack_h1']??'Modern PHP.'])</div>
                    <div class="col-md-4">@include('admin.pages._cf', ['l'=>'Heading Line 2 (gradient)','n'=>'stack_h2','v'=>$c['stack_h2']??'No compromises.'])</div>
                    <div class="col-12">@include('admin.pages._cf', ['l'=>'Description','n'=>'stack_desc','v'=>$c['stack_desc']??'Built on Laravel 11 and Livewire 3 — the most productive PHP stack available today. Your developers feel right at home. Your ops team gets a standard Nginx + MySQL app.','ta'=>true])</div>
                </div>
                </div>
                @php $si=[1=>['Laravel 11 + PHP 8.2+','Full MVC, Eloquent ORM, Artisan CLI, Sanctum API'],2=>['Livewire 3 + Tailwind CSS','52+ reactive components, zero page reload UX'],3=>['Stripe Payments + Webhooks','Checkout, subscriptions, invoice lifecycle events'],4=>['MySQL + Local / AWS S3 Storage','Switchable filesystem driver via .env — no code changes'],5=>['SendGrid + reCAPTCHA + Google Maps','Pre-wired — just add your API keys in the wizard']]; @endphp
                @foreach($si as $i=>$d)
                @include('admin.pages._card', ['title' => "Stack Item {$i}"])
                <div class="row g-3">
                    <div class="col-md-5">@include('admin.pages._cf', ['l'=>'Title','n'=>"stack_{$i}_title",'v'=>$c["stack_{$i}_title"]??$d[0]])</div>
                    <div class="col-md-7">@include('admin.pages._cf', ['l'=>'Sub-text','n'=>"stack_{$i}_sub",'v'=>$c["stack_{$i}_sub"]??$d[1]])</div>
                </div>
                </div>
                @endforeach
                @include('admin.pages._section', ['title' => 'Ownership Pillars'])
                @php $pi=[1=>['Full Source Code Ownership','You receive every file — controllers, migrations, views, config. Modify, extend, or resell however you choose. No SaaS fees. No runtime license checks. No vendor lock-in.'],2=>['Self-Hosted','Your server, your DB, your rules. Deploy on shared hosting, VPS, or cloud.'],3=>['Your Brand','Logo, colors, fonts — fully configurable without touching code.'],4=>['No Per-Agent Fees','Own the platform. Add unlimited agents. Keep all subscription revenue.'],5=>['Guided Setup','10-step wizard — configure and go live in minutes, not days.']]; @endphp
                @foreach($pi as $i=>$d)
                @include('admin.pages._card', ['title' => "Pillar {$i} — {$d[0]}"])
                <div class="row g-3">
                    <div class="col-md-4">@include('admin.pages._cf', ['l'=>'Title','n'=>"pillar_{$i}_title",'v'=>$c["pillar_{$i}_title"]??$d[0]])</div>
                    <div class="col-md-8">@include('admin.pages._cf', ['l'=>'Description','n'=>"pillar_{$i}_desc",'v'=>$c["pillar_{$i}_desc"]??$d[1],'ta'=>true])</div>
                </div>
                </div>
                @endforeach
            </div>

            {{-- DEMO CTA --}}
            <div id="panel-cta" class="ce-panel" style="display:none;">
                @include('admin.pages._section', ['title' => 'Demo CTA Section'])
                <div class="row g-3">
                    <div class="col-12">@include('admin.pages._cf', ['l'=>'Badge Text','n'=>'cta_badge','v'=>$c['cta_badge']??'Interactive Demo — Available Right Now'])</div>
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Heading Line 1','n'=>'cta_h1','v'=>$c['cta_h1']??'See it live before'])</div>
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Heading Line 2 (gradient)','n'=>'cta_h2','v'=>$c['cta_h2']??'you decide'])</div>
                    <div class="col-12">@include('admin.pages._cf', ['l'=>'Description','n'=>'cta_desc','v'=>$c['cta_desc']??'Your own private sandbox — pre-loaded with an Admin account, an Agent account, and two property listings. Switch between all three roles freely. Everything resets automatically after 60 minutes.','ta'=>true])</div>
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Button Text','n'=>'cta_btn','v'=>$c['cta_btn']??'Start My Free Demo'])</div>
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Footer Note (below button)','n'=>'cta_footer_note','v'=>$c['cta_footer_note']??'No account · No credit card · Fully isolated · 60 minutes · Auto-deleted'])</div>
                </div>
                @include('admin.pages._section', ['title' => 'Demo Role Previews'])
                <div class="row g-3">
                    @foreach([[1,'Admin','Dashboard & Config'],[2,'Agent','Listings & Billing'],[3,'Buyer','Public Property']] as [$i,$lbl,$sub])
                    <div class="col-md-2">@include('admin.pages._cf', ['l'=>"Role {$i} Label",'n'=>"cta_role{$i}_label",'v'=>$c["cta_role{$i}_label"]??$lbl])</div>
                    <div class="col-md-4">@include('admin.pages._cf', ['l'=>"Role {$i} Sub-text",'n'=>"cta_role{$i}_sub",'v'=>$c["cta_role{$i}_sub"]??$sub])</div>
                    @endforeach
                </div>
            </div>

            {{-- FOOTER --}}
            <div id="panel-footer" class="ce-panel" style="display:none;">
                @include('admin.pages._section', ['title' => 'Footer'])
                <div class="row g-3">
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Tagline (beside logo)','n'=>'footer_tagline','v'=>$c['footer_tagline']??'· White-Label Real Estate CMS'])</div>
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Tech Stack Note (right side)','n'=>'footer_tech','v'=>$c['footer_tech']??'Built with Laravel 11 · PHP 8.2 · Livewire 3'])</div>
                </div>
            </div>

        @elseif($isDemo)

            {{-- DEMO HERO --}}
            <div id="panel-hero" class="ce-panel">
                @include('admin.pages._section', ['title' => 'Hero Section'])
                <div class="row g-3">
                    <div class="col-12">@include('admin.pages._cf', ['l'=>'Badge Text','n'=>'hero_badge','v'=>$c['hero_badge']??'Live Interactive Demo — Credentials sent to your email'])</div>
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Headline Line 1','n'=>'hero_h1','v'=>$c['hero_h1']??'Experience the Full System'])</div>
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Headline Line 2','n'=>'hero_h2','v'=>$c['hero_h2']??'in 60 Minutes'])</div>
                    <div class="col-12">@include('admin.pages._cf', ['l'=>'Description','n'=>'hero_desc','v'=>$c['hero_desc']??'Get your own private sandbox. Play as Admin, Agent, or Buyer — all three roles, complete isolation, automatically cleaned up.','ta'=>true])</div>
                </div>
            </div>

            {{-- DEMO ROLE CARDS --}}
            <div id="panel-roles" class="ce-panel" style="display:none;">
                @include('admin.pages._section', ['title' => 'Role Cards'])
                @php $dr=[1=>['Super Admin','Manage agents, create subscription plans, configure settings, and view revenue dashboards.','Dashboard,Plans,Settings'],2=>['Real Estate Agent','Create property listings, upload images, manage floor plans, videos, documents and track inquiries.','Listings,Media,Billing'],3=>['Property Buyer','Browse the public property page — view gallery, floor plans, virtual tour, and send an inquiry.','Gallery,Floor Plans,Inquiry']]; @endphp
                @foreach($dr as $i=>$d)
                @include('admin.pages._card', ['title' => "Card {$i} — {$d[0]}"])
                <div class="row g-3">
                    <div class="col-md-4">@include('admin.pages._cf', ['l'=>'Title','n'=>"demo_role_{$i}_title",'v'=>$c["demo_role_{$i}_title"]??$d[0]])</div>
                    <div class="col-md-5">@include('admin.pages._cf', ['l'=>'Description','n'=>"demo_role_{$i}_desc",'v'=>$c["demo_role_{$i}_desc"]??$d[1],'ta'=>true])</div>
                    <div class="col-md-3">@include('admin.pages._cf', ['l'=>'Tags (comma-separated)','n'=>"demo_role_{$i}_tags",'v'=>$c["demo_role_{$i}_tags"]??$d[2]])</div>
                </div>
                </div>
                @endforeach
            </div>

            {{-- DEMO SANDBOX STATS --}}
            <div id="panel-stats" class="ce-panel" style="display:none;">
                @include('admin.pages._section', ['title' => 'Sandbox Stats'])
                <div class="row g-3" style="margin-bottom:16px;">
                    <div class="col-12">@include('admin.pages._cf', ['l'=>'Section Heading','n'=>'sandbox_heading','v'=>$c['sandbox_heading']??"What's pre-loaded in your sandbox"])</div>
                </div>
                @foreach([1=>['1','Admin Account'],2=>['1','Agent Account'],3=>['2','Live Properties'],4=>['1','Active Plan']] as $i=>$d)
                @include('admin.pages._card', ['title' => "Stat {$i}"])
                <div class="row g-3">
                    <div class="col-md-3">@include('admin.pages._cf', ['l'=>'Number','n'=>"sandbox_{$i}_num",'v'=>$c["sandbox_{$i}_num"]??$d[0]])</div>
                    <div class="col-md-9">@include('admin.pages._cf', ['l'=>'Label','n'=>"sandbox_{$i}_label",'v'=>$c["sandbox_{$i}_label"]??$d[1]])</div>
                </div>
                </div>
                @endforeach
            </div>

            {{-- DEMO FORM CARD --}}
            <div id="panel-form" class="ce-panel" style="display:none;">
                @include('admin.pages._section', ['title' => 'Form Card'])
                <div class="row g-3">
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Card Title','n'=>'form_title','v'=>$c['form_title']??'Get Your Private Demo'])</div>
                    <div class="col-md-6">@include('admin.pages._cf', ['l'=>'Card Subtitle','n'=>'form_subtitle','v'=>$c['form_subtitle']??'Credentials will be emailed to you instantly.'])</div>
                </div>
            </div>

        @endif

        </div>{{-- tab body --}}

        {{-- Save bar --}}
        <div style="display:flex;align-items:center;gap:10px;padding-top:18px;border-top:1px solid #f3f4f6;margin-top:20px;">
            <button type="submit" class="btn-blue m-0">
                <i class="fa fa-floppy-disk mr-1"></i> Save Content
            </button>
            <a href="{{ route('admin.pages.lists') }}" class="btn-grey m-0">Cancel</a>
            <span style="font-size:12px;color:#9ca3af;margin-left:4px;">Changes go live immediately on save.</span>
        </div>

    </form>
</div>

<script>
function ceTab(key) {
    document.querySelectorAll('.ce-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('[id^="tab-btn-"]').forEach(b => {
        b.style.color = '#6b7280';
        b.style.borderBottomColor = 'transparent';
    });
    document.getElementById('panel-' + key).style.display = 'block';
    var btn = document.getElementById('tab-btn-' + key);
    btn.style.color = '#1d4ed8';
    btn.style.borderBottomColor = '#1d4ed8';
}
</script>
@endsection
