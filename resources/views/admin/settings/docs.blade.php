@extends('admin.layouts.default')

@section('title', 'Help & Documentation')

@section('content')

<div class="d-flex align-items-center justify-content-between my-4 flex-wrap page-heading no-print">
    <div>
        <h5 class="mb-0">Help &amp; Documentation</h5>
        <p class="text-sm text-gray-500 mb-0 mt-1">Everything you need to get the most out of your CMS.</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('admin.settings.docs.download') }}"
            style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#16a34a;border:none;border-radius:8px;font-size:13px;font-weight:500;color:white;text-decoration:none;">
            <i class="fas fa-file-word" style="font-size:11px;"></i> Download Word
        </a>
        <button onclick="window.print()"
            style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#2563eb;border:none;border-radius:8px;font-size:13px;font-weight:500;color:white;cursor:pointer;">
            <i class="fas fa-file-pdf" style="font-size:11px;"></i> Download PDF
        </button>
        <a href="{{ route('admin.settings.index') }}"
            style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:white;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-weight:500;color:#374151;text-decoration:none;">
            <i class="fas fa-arrow-left" style="font-size:11px;"></i> Back to Settings
        </a>
    </div>
</div>

{{-- PDF Document --}}
<div class="doc-page">

    {{-- Cover --}}
    <div class="doc-cover">
        <div class="doc-cover-logo">
            @php
                $brand = cache('brand_settings');
                $logo  = ($brand && $brand->logo_path) ? asset($brand->logo_path) : asset('images/logo-placeholder-small.png');
            @endphp
            <img src="{{ $logo }}" alt="{{ config('app.name') }}" style="max-height:48px;object-fit:contain;">
        </div>
        <h1 class="doc-title">{{ config('app.name') }}</h1>
        <p class="doc-subtitle">Admin Panel — Help &amp; Documentation</p>
        <p class="doc-version">Version 1.0 &nbsp;·&nbsp; {{ now()->format('F Y') }}</p>
    </div>

    {{-- TOC --}}
    <div class="doc-toc page-break">
        <h2 class="doc-section-title">Table of Contents</h2>
        <ol class="toc-list">
            <li><a href="#s1">Getting Started</a></li>
            <li><a href="#s2">Dashboard Overview</a></li>
            <li><a href="#s3">Managing Agents</a></li>
            <li><a href="#s4">Property Listings</a></li>
            <li><a href="#s5">Subscription Plans</a></li>
            <li><a href="#s6">Mail Configuration</a></li>
            <li><a href="#s7">Stripe &amp; Payments</a></li>
            <li><a href="#s8">File Storage</a></li>
            <li><a href="#s9">reCAPTCHA Protection</a></li>
            <li><a href="#s10">Brand &amp; Appearance</a></li>
            <li><a href="#s11">Page Builder</a></li>
            <li><a href="#s12">Frequently Asked Questions</a></li>
        </ol>
    </div>

    {{-- Section 1 --}}
    <div id="s1" class="doc-section page-break">
        <div class="section-number">01</div>
        <h2 class="doc-section-title">Getting Started</h2>
        <p>Welcome to <strong>{{ config('app.name') }}</strong> — a white-label real estate CMS built on Laravel. This guide covers everything you need to manage your platform as an administrator.</p>

        <h3>First Steps After Installation</h3>
        <ol>
            <li>Sign in at <code>/admin/sign-in</code> with the credentials you created during the setup wizard.</li>
            <li>Go to <strong>Settings → Mail</strong> and configure your outgoing email so agents receive notifications.</li>
            <li>Go to <strong>Settings → Payments</strong> and enter your Stripe keys to accept subscription payments.</li>
            <li>Go to <strong>Settings → Brand</strong> to upload your logo and favicon.</li>
            <li>Go to <strong>Plans</strong> and create at least one subscription plan for agents.</li>
        </ol>

        <h3>Admin Login</h3>
        <table class="doc-table">
            <thead><tr><th>URL</th><th>Description</th></tr></thead>
            <tbody>
                <tr><td><code>/admin/sign-in</code></td><td>Admin login page</td></tr>
                <tr><td><code>/admin/dashboard</code></td><td>Main admin dashboard</td></tr>
                <tr><td><code>/admin/sign-out</code></td><td>Log out of the admin panel</td></tr>
            </tbody>
        </table>
    </div>

    {{-- Section 2 --}}
    <div id="s2" class="doc-section">
        <div class="section-number">02</div>
        <h2 class="doc-section-title">Dashboard Overview</h2>
        <p>The dashboard gives you a real-time summary of your platform's activity.</p>

        <h3>What You'll See</h3>
        <table class="doc-table">
            <thead><tr><th>Widget</th><th>Description</th></tr></thead>
            <tbody>
                <tr><td>Total Agents</td><td>Number of registered agent accounts</td></tr>
                <tr><td>Active Subscriptions</td><td>Agents currently on a paid plan</td></tr>
                <tr><td>Total Properties</td><td>All property listings across all agents</td></tr>
                <tr><td>Recent Payments</td><td>Latest subscription payments received</td></tr>
            </tbody>
        </table>

        <h3>Navigation Sidebar</h3>
        <p>Use the left sidebar to navigate between sections. All core admin functions are accessible from the sidebar — Agents, Properties, Plans, Subscribers, Settings, and more.</p>
    </div>

    {{-- Section 3 --}}
    <div id="s3" class="doc-section page-break">
        <div class="section-number">03</div>
        <h2 class="doc-section-title">Managing Agents</h2>
        <p>Agents are the primary users of the platform. Each agent can manage their own property listings, profile, and subscription.</p>

        <h3>Agent Listing</h3>
        <p>Navigate to <strong>Agents</strong> in the sidebar to view all registered agents. From here you can:</p>
        <ul>
            <li>View agent profile details and contact information</li>
            <li>See the agent's active subscription plan and expiry date</li>
            <li>View all properties listed by the agent</li>
            <li>Suspend or remove an agent account</li>
        </ul>

        <h3>Agent Registration</h3>
        <p>Agents register themselves at <code>/register</code>. After registration, they must verify their email before accessing the agent portal. They can then subscribe to a plan at <code>/agent/billing</code>.</p>

        <h3>Agent Portal</h3>
        <p>Agents access their portal at <code>/agent/dashboard</code>. They manage their own properties, images, documents, floor plans, and account settings independently.</p>
    </div>

    {{-- Section 4 --}}
    <div id="s4" class="doc-section">
        <div class="section-number">04</div>
        <h2 class="doc-section-title">Property Listings</h2>
        <p>Properties are managed by agents through their portal. As admin, you have read access to all properties across the platform.</p>

        <h3>Viewing All Properties</h3>
        <p>Go to <strong>All Properties</strong> in the sidebar to see every listing on the platform, regardless of which agent created it.</p>

        <h3>Property Features</h3>
        <table class="doc-table">
            <thead><tr><th>Feature</th><th>Description</th></tr></thead>
            <tbody>
                <tr><td>Images</td><td>Multiple images with drag-and-drop reordering</td></tr>
                <tr><td>Photo Galleries</td><td>Grouped image collections</td></tr>
                <tr><td>Floor Plans</td><td>Upload floor plan images with interactive hotspots</td></tr>
                <tr><td>Videos</td><td>YouTube, Vimeo, or direct video links</td></tr>
                <tr><td>3D Tour</td><td>Matterport embed for virtual walkthroughs</td></tr>
                <tr><td>360° Panorama</td><td>Pannellum-powered panoramic viewer</td></tr>
                <tr><td>Documents</td><td>PDF brochures and attachments</td></tr>
                <tr><td>Map</td><td>Google Maps integration with address lookup</td></tr>
                <tr><td>Amenities</td><td>Tag-based feature listing</td></tr>
            </tbody>
        </table>
    </div>

    {{-- Section 5 --}}
    <div id="s5" class="doc-section page-break">
        <div class="section-number">05</div>
        <h2 class="doc-section-title">Subscription Plans</h2>
        <p>Plans control what agents can access and for how long. Plans are synced to Stripe so payments are processed automatically.</p>

        <h3>Creating a Plan</h3>
        <ol>
            <li>Go to <strong>Plans</strong> in the sidebar.</li>
            <li>Click <strong>Create Plan</strong>.</li>
            <li>Set the plan name, price, billing interval (monthly/yearly), and feature limits.</li>
            <li>Save — the plan is automatically created as a Stripe Product.</li>
        </ol>

        <h3>Agent Subscription Flow</h3>
        <ol>
            <li>Agent visits <code>/agent/billing</code> and selects a plan.</li>
            <li>They are redirected to Stripe Checkout to enter payment details.</li>
            <li>On successful payment, a webhook from Stripe activates their subscription.</li>
            <li>Subscription status is tracked in the <strong>Subscribers</strong> section of the admin panel.</li>
        </ol>

        <div class="doc-callout">
            <strong>Important:</strong> Stripe webhooks must be configured correctly for subscriptions to activate automatically. See Section 7 — Stripe &amp; Payments for details.
        </div>
    </div>

    {{-- Section 6 --}}
    <div id="s6" class="doc-section">
        <div class="section-number">06</div>
        <h2 class="doc-section-title">Mail Configuration</h2>
        <p>Go to <strong>Settings → Mail</strong> to configure outgoing email. The platform uses SMTP — compatible with SendGrid, Mailgun, Gmail, and any standard SMTP provider.</p>

        <h3>Required Fields</h3>
        <table class="doc-table">
            <thead><tr><th>Field</th><th>Description</th></tr></thead>
            <tbody>
                <tr><td>SMTP Host</td><td>Your mail server (e.g. <code>smtp.sendgrid.net</code>)</td></tr>
                <tr><td>Port</td><td>Usually 587 (TLS) or 465 (SSL)</td></tr>
                <tr><td>Username</td><td>Your SMTP username or API key</td></tr>
                <tr><td>Password</td><td>Your SMTP password (leave blank to keep current)</td></tr>
                <tr><td>From Address</td><td>The sender email shown to recipients</td></tr>
                <tr><td>From Name</td><td>The sender name shown to recipients</td></tr>
            </tbody>
        </table>

        <h3>What Emails Are Sent</h3>
        <ul>
            <li>Agent registration welcome email</li>
            <li>Email verification link</li>
            <li>Password reset link</li>
            <li>Property enquiry notifications</li>
            <li>Subscription confirmation</li>
        </ul>
    </div>

    {{-- Section 7 --}}
    <div id="s7" class="doc-section page-break">
        <div class="section-number">07</div>
        <h2 class="doc-section-title">Stripe &amp; Payments</h2>
        <p>Go to <strong>Settings → Payments</strong> to configure Stripe. You need a Stripe account at <strong>stripe.com</strong>.</p>

        <h3>Keys Required</h3>
        <table class="doc-table">
            <thead><tr><th>Key</th><th>Where to Find It</th></tr></thead>
            <tbody>
                <tr><td>Publishable Key</td><td>Stripe Dashboard → Developers → API Keys</td></tr>
                <tr><td>Secret Key</td><td>Stripe Dashboard → Developers → API Keys</td></tr>
                <tr><td>Webhook Secret</td><td>Stripe Dashboard → Developers → Webhooks</td></tr>
            </tbody>
        </table>

        <h3>Setting Up Webhooks</h3>
        <ol>
            <li>In Stripe Dashboard, go to <strong>Developers → Webhooks</strong>.</li>
            <li>Click <strong>Add endpoint</strong>.</li>
            <li>Enter your webhook URL: <code>https://yourdomain.com/webhook/stripe</code></li>
            <li>Select events to listen for: <code>invoice.payment_succeeded</code>, <code>customer.subscription.deleted</code>, <code>checkout.session.completed</code>.</li>
            <li>Copy the <strong>Signing secret</strong> and paste it into Settings → Payments → Webhook Secret.</li>
        </ol>

        <div class="doc-callout">
            <strong>Test Mode:</strong> Use Stripe test keys (<code>pk_test_...</code> / <code>sk_test_...</code>) during development. Switch to live keys when going live.
        </div>
    </div>

    {{-- Section 8 --}}
    <div id="s8" class="doc-section">
        <div class="section-number">08</div>
        <h2 class="doc-section-title">File Storage</h2>
        <p>Go to <strong>Settings → Storage</strong> to choose where property images and documents are stored.</p>

        <h3>Options</h3>
        <table class="doc-table">
            <thead><tr><th>Driver</th><th>Best For</th><th>Requirements</th></tr></thead>
            <tbody>
                <tr><td>Local</td><td>Small to medium deployments</td><td>None — files stored on your server</td></tr>
                <tr><td>AWS S3</td><td>Large scale or CDN delivery</td><td>AWS account, S3 bucket, IAM credentials</td></tr>
            </tbody>
        </table>

        <h3>AWS S3 Setup</h3>
        <ol>
            <li>Create an S3 bucket in your AWS region.</li>
            <li>Create an IAM user with <code>s3:GetObject</code>, <code>s3:PutObject</code>, <code>s3:DeleteObject</code> permissions on the bucket.</li>
            <li>Generate access keys for the IAM user.</li>
            <li>Enter the keys in Settings → Storage.</li>
        </ol>
    </div>

    {{-- Section 9 --}}
    <div id="s9" class="doc-section page-break">
        <div class="section-number">09</div>
        <h2 class="doc-section-title">reCAPTCHA Protection</h2>
        <p>Go to <strong>Settings → reCAPTCHA</strong> to protect agent registration from spam bots. Uses Google reCAPTCHA v2.</p>

        <h3>Setup Steps</h3>
        <ol>
            <li>Visit <strong>google.com/recaptcha</strong> and sign in.</li>
            <li>Click <strong>+ Create</strong> and choose reCAPTCHA v2 → "I'm not a robot" checkbox.</li>
            <li>Add your domain to the allowed domains list.</li>
            <li>Copy the <strong>Site Key</strong> and <strong>Secret Key</strong>.</li>
            <li>Paste them into Settings → reCAPTCHA and save.</li>
        </ol>

        <div class="doc-callout">
            If reCAPTCHA is not configured, the registration form still works — the protection is simply not shown.
        </div>
    </div>

    {{-- Section 10 --}}
    <div id="s10" class="doc-section">
        <div class="section-number">10</div>
        <h2 class="doc-section-title">Brand &amp; Appearance</h2>
        <p>Go to <strong>Settings → Brand</strong> to customise the visual identity of your platform.</p>

        <h3>What You Can Change</h3>
        <table class="doc-table">
            <thead><tr><th>Setting</th><th>Description</th></tr></thead>
            <tbody>
                <tr><td>Logo</td><td>Appears in the admin sidebar and agent portal header</td></tr>
                <tr><td>Favicon</td><td>Small icon shown in browser tabs</td></tr>
                <tr><td>Primary Colour</td><td>Main action colour (buttons, links, highlights)</td></tr>
                <tr><td>Secondary Colour</td><td>Supporting accent colour</td></tr>
                <tr><td>Sidebar Colour</td><td>Admin sidebar background</td></tr>
                <tr><td>Body Font</td><td>Main text font across the platform</td></tr>
                <tr><td>Heading Font</td><td>Titles and headings font</td></tr>
                <tr><td>Admin Font</td><td>Font used specifically in the admin panel</td></tr>
            </tbody>
        </table>

        <p>Changes take effect immediately across the platform. Fonts are loaded from Google Fonts automatically.</p>
    </div>

    {{-- Section 11 --}}
    <div id="s11" class="doc-section page-break">
        <div class="section-number">11</div>
        <h2 class="doc-section-title">Page Builder</h2>
        <p>Go to <strong>Page Builder</strong> in the sidebar to create and edit custom pages using the visual drag-and-drop editor powered by GrapesJS.</p>

        <h3>System Pages</h3>
        <p>Two system pages are pre-defined and cannot be deleted:</p>
        <table class="doc-table">
            <thead><tr><th>Page</th><th>URL</th><th>Description</th></tr></thead>
            <tbody>
                <tr><td>Home Page</td><td><code>/</code></td><td>The public-facing landing page</td></tr>
                <tr><td>Demo Landing</td><td><code>/demo</code></td><td>The demo sign-up page</td></tr>
            </tbody>
        </table>

        <h3>Creating a Custom Page</h3>
        <ol>
            <li>Go to <strong>Page Builder → Create Page</strong>.</li>
            <li>Enter the page title — the URL slug is generated automatically.</li>
            <li>Click <strong>Open Editor</strong> to launch the visual editor.</li>
            <li>Drag and drop blocks to build your layout.</li>
            <li>Click <strong>Save</strong> to publish. The page is live immediately at <code>/your-page-slug</code>.</li>
        </ol>

        <h3>Publishing a System Page</h3>
        <p>To activate the home page or demo page, edit it in the Page Builder and toggle <strong>Active</strong>. When active, the page builder output is served at that URL. When inactive, the default Blade template is used.</p>
    </div>

    {{-- Section 12 --}}
    <div id="s12" class="doc-section">
        <div class="section-number">12</div>
        <h2 class="doc-section-title">Frequently Asked Questions</h2>

        <h3>An agent says they didn't receive their verification email. What do I do?</h3>
        <p>Check that mail is configured correctly in Settings → Mail. You can also ask the agent to click "Resend verification email" on the login page. If still failing, check your SMTP credentials and spam folders.</p>

        <h3>A subscription payment was made but the agent's account wasn't activated. Why?</h3>
        <p>This is almost always a webhook issue. Verify that your Stripe webhook endpoint URL is correct and that the Webhook Secret in Settings → Payments matches the one in your Stripe Dashboard. Check the Stripe Dashboard → Developers → Webhooks → Recent Deliveries for error details.</p>

        <h3>How do I reset the setup wizard?</h3>
        <p>Open your <code>.env</code> file on the server and set <code>APP_INSTALLED=false</code>. The wizard will become accessible again at <code>/setup</code>. Note: this does not delete any data — it only makes the wizard accessible.</p>

        <h3>Can I use local storage in production?</h3>
        <p>Yes, but only if your server has sufficient disk space and you have a backup strategy. For high-traffic deployments or deployments on cloud platforms without persistent storage (e.g. Heroku), use AWS S3.</p>

        <h3>How do I update the app name shown throughout the platform?</h3>
        <p>Edit the <code>APP_NAME</code> value in your <code>.env</code> file, then run <code>php artisan config:clear</code> on the server. The new name appears everywhere automatically.</p>

        <h3>Where are uploaded files stored by default?</h3>
        <p>With local storage, files are stored in the <code>storage/app/public</code> directory on your server. Make sure a symlink exists: run <code>php artisan storage:link</code> after deployment.</p>
    </div>

    {{-- Footer --}}
    <div class="doc-footer">
        <p>{{ config('app.name') }} &nbsp;·&nbsp; Admin Documentation &nbsp;·&nbsp; {{ now()->format('Y') }}</p>
        <p style="margin-top:4px;font-size:11px;color:#9ca3af;">This document is confidential and intended for platform administrators only.</p>
    </div>

</div>

<style>
/* ── Base ─────────────────────────────────────────── */
.doc-page {
    max-width: 820px;
    margin: 0 auto 60px;
    font-family: 'Georgia', serif;
    color: #1a1a2e;
}

/* ── Cover ────────────────────────────────────────── */
.doc-cover {
    text-align: center;
    padding: 80px 40px 60px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}
.doc-cover-logo { margin-bottom: 32px; }
.doc-title { font-size: 2rem; font-weight: 700; color: #111827; margin: 0 0 8px; font-family: sans-serif; }
.doc-subtitle { font-size: 1.1rem; color: #6b7280; margin: 0 0 20px; font-family: sans-serif; }
.doc-version { font-size: .85rem; color: #9ca3af; font-family: sans-serif; margin: 0; }

/* ── TOC ──────────────────────────────────────────── */
.doc-toc {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 32px 36px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}
.toc-list { padding-left: 20px; margin: 0; }
.toc-list li { padding: 5px 0; font-size: .95rem; color: #374151; }
.toc-list a { color: #2563eb; text-decoration: none; }
.toc-list a:hover { text-decoration: underline; }

/* ── Sections ─────────────────────────────────────── */
.doc-section {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 36px 40px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
    position: relative;
}
.section-number {
    position: absolute;
    top: 32px;
    right: 36px;
    font-size: 3rem;
    font-weight: 800;
    color: #f3f4f6;
    font-family: sans-serif;
    line-height: 1;
    user-select: none;
}
.doc-section-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e5e7eb;
    font-family: sans-serif;
}
.doc-section h3 {
    font-size: 1rem;
    font-weight: 700;
    color: #374151;
    margin: 24px 0 10px;
    font-family: sans-serif;
}
.doc-section p {
    font-size: .9rem;
    line-height: 1.75;
    color: #4b5563;
    margin: 0 0 12px;
}
.doc-section ul, .doc-section ol {
    font-size: .9rem;
    color: #4b5563;
    padding-left: 22px;
    margin: 0 0 12px;
    line-height: 1.75;
}
.doc-section li { margin-bottom: 4px; }
.doc-section code {
    font-size: .8rem;
    background: #f3f4f6;
    color: #dc2626;
    padding: 1px 6px;
    border-radius: 4px;
    font-family: monospace;
}

/* ── Table ────────────────────────────────────────── */
.doc-table {
    width: 100%;
    border-collapse: collapse;
    font-size: .85rem;
    margin: 8px 0 16px;
}
.doc-table th {
    background: #f9fafb;
    font-weight: 600;
    color: #374151;
    text-align: left;
    padding: 9px 14px;
    border: 1px solid #e5e7eb;
    font-family: sans-serif;
}
.doc-table td {
    padding: 8px 14px;
    border: 1px solid #e5e7eb;
    color: #4b5563;
    vertical-align: top;
}
.doc-table tr:nth-child(even) td { background: #fafafa; }

/* ── Callout ──────────────────────────────────────── */
.doc-callout {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-left: 4px solid #2563eb;
    border-radius: 6px;
    padding: 12px 16px;
    font-size: .875rem;
    color: #1e40af;
    margin: 16px 0;
}

/* ── Footer ───────────────────────────────────────── */
.doc-footer {
    text-align: center;
    padding: 24px;
    font-size: .8rem;
    color: #6b7280;
    font-family: sans-serif;
}

/* ── Print ────────────────────────────────────────── */
@media print {
    .no-print,
    .agent_menu,
    .page-heading,
    nav,
    header,
    aside { display: none !important; }

    body, html { background: white !important; margin: 0; padding: 0; }
    .main-content, .page, .content-wrapper { margin: 0 !important; padding: 0 !important; background: white !important; }
    .doc-page { max-width: 100%; margin: 0; }
    .doc-cover, .doc-toc, .doc-section {
        border: none !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        margin-bottom: 0 !important;
    }
    .page-break { page-break-before: always; }
    .doc-section { page-break-inside: avoid; }
    a { color: #2563eb !important; }
    .section-number { color: #eeeeee !important; }
}
</style>

@endsection
