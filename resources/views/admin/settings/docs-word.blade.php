<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:w="urn:schemas-microsoft-com:office:word"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="utf-8">
<meta name="ProgId" content="Word.Document">
<meta name="Generator" content="Microsoft Word 15">
<title>{{ config('app.name') }} — Admin Documentation</title>
<style>
    body {
        font-family: Calibri, Arial, sans-serif;
        font-size: 11pt;
        color: #1a1a1a;
        margin: 2cm 2.5cm;
        line-height: 1.5;
    }
    h1 { font-size: 26pt; font-weight: bold; color: #111827; margin: 0 0 6pt; page-break-before: always; }
    h1.cover { page-break-before: avoid; }
    h2 { font-size: 15pt; font-weight: bold; color: #1d4ed8; border-bottom: 1pt solid #e5e7eb; padding-bottom: 4pt; margin: 20pt 0 10pt; }
    h3 { font-size: 12pt; font-weight: bold; color: #374151; margin: 14pt 0 6pt; }
    p  { font-size: 11pt; color: #374151; margin: 0 0 8pt; }
    ul, ol { font-size: 11pt; color: #374151; margin: 0 0 8pt; padding-left: 18pt; }
    li { margin-bottom: 3pt; }
    code { font-family: Consolas, monospace; font-size: 10pt; background: #f3f4f6; color: #dc2626; padding: 1pt 4pt; }
    table { border-collapse: collapse; width: 100%; margin: 8pt 0 14pt; font-size: 10.5pt; }
    th { background: #f9fafb; font-weight: bold; color: #374151; text-align: left; padding: 6pt 10pt; border: 1pt solid #d1d5db; }
    td { padding: 5pt 10pt; border: 1pt solid #d1d5db; color: #374151; vertical-align: top; }
    tr:nth-child(even) td { background: #fafafa; }
    .callout { background: #eff6ff; border-left: 4pt solid #2563eb; padding: 8pt 12pt; margin: 10pt 0; font-size: 10.5pt; color: #1e40af; }
    .cover-block { text-align: center; margin: 60pt 0 40pt; }
    .subtitle { font-size: 14pt; color: #6b7280; }
    .version  { font-size: 10pt; color: #9ca3af; margin-top: 6pt; }
    .section-num { font-size: 10pt; font-weight: bold; color: #9ca3af; text-transform: uppercase; letter-spacing: 2pt; margin-bottom: 4pt; }
    .toc li { margin-bottom: 5pt; }
    .toc a  { color: #2563eb; text-decoration: none; }
</style>
</head>
<body>

{{-- Cover --}}
<div class="cover-block">
    <h1 class="cover">{{ config('app.name') }}</h1>
    <p class="subtitle">Admin Panel — Help &amp; Documentation</p>
    <p class="version">Version 1.0 &nbsp;·&nbsp; {{ now()->format('F Y') }}</p>
</div>

{{-- TOC --}}
<h2>Table of Contents</h2>
<ol class="toc">
    <li>Getting Started</li>
    <li>Dashboard Overview</li>
    <li>Managing Agents</li>
    <li>Property Listings</li>
    <li>Subscription Plans</li>
    <li>Mail Configuration</li>
    <li>Stripe &amp; Payments</li>
    <li>File Storage</li>
    <li>reCAPTCHA Protection</li>
    <li>Google Maps</li>
    <li>Brand &amp; Appearance</li>
    <li>Page Builder</li>
    <li>Frequently Asked Questions</li>
</ol>

{{-- Section 1 --}}
<p class="section-num">01</p>
<h1>Getting Started</h1>
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
<table>
    <thead><tr><th>URL</th><th>Description</th></tr></thead>
    <tbody>
        <tr><td><code>/admin/sign-in</code></td><td>Admin login page</td></tr>
        <tr><td><code>/admin/dashboard</code></td><td>Main admin dashboard</td></tr>
        <tr><td><code>/admin/sign-out</code></td><td>Log out of the admin panel</td></tr>
    </tbody>
</table>

{{-- Section 2 --}}
<p class="section-num">02</p>
<h1>Dashboard Overview</h1>
<p>The dashboard gives you a real-time summary of your platform's activity.</p>
<h3>What You'll See</h3>
<table>
    <thead><tr><th>Widget</th><th>Description</th></tr></thead>
    <tbody>
        <tr><td>Total Agents</td><td>Number of registered agent accounts</td></tr>
        <tr><td>Active Subscriptions</td><td>Agents currently on a paid plan</td></tr>
        <tr><td>Total Properties</td><td>All property listings across all agents</td></tr>
        <tr><td>Recent Payments</td><td>Latest subscription payments received</td></tr>
    </tbody>
</table>

{{-- Section 3 --}}
<p class="section-num">03</p>
<h1>Managing Agents</h1>
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

{{-- Section 4 --}}
<p class="section-num">04</p>
<h1>Property Listings</h1>
<p>Properties are managed by agents through their portal. As admin, you have read access to all properties across the platform.</p>
<h3>Property Features</h3>
<table>
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

{{-- Section 5 --}}
<p class="section-num">05</p>
<h1>Subscription Plans</h1>
<p>Plans control what agents can access and for how long. Plans are synced to Stripe so payments are processed automatically.</p>
<h3>Creating a Plan</h3>
<ol>
    <li>Go to <strong>Plans</strong> in the sidebar.</li>
    <li>Click <strong>Create Plan</strong>.</li>
    <li>Set the plan name, price, billing interval (monthly/yearly), and feature limits.</li>
    <li>Save — the plan is automatically created as a Stripe Product.</li>
</ol>
<div class="callout"><strong>Important:</strong> Stripe webhooks must be configured correctly for subscriptions to activate automatically. See Section 7 — Stripe &amp; Payments for details.</div>

{{-- Section 6 --}}
<p class="section-num">06</p>
<h1>Mail Configuration</h1>
<p>Go to <strong>Settings → Mail</strong> to configure outgoing email. Compatible with SendGrid, Mailgun, Gmail, and any standard SMTP provider.</p>
<table>
    <thead><tr><th>Field</th><th>Description</th></tr></thead>
    <tbody>
        <tr><td>SMTP Host</td><td>Your mail server (e.g. <code>smtp.sendgrid.net</code>)</td></tr>
        <tr><td>Port</td><td>Usually 587 (TLS) or 465 (SSL)</td></tr>
        <tr><td>Username</td><td>Your SMTP username or API key</td></tr>
        <tr><td>Password</td><td>Your SMTP password</td></tr>
        <tr><td>From Address</td><td>The sender email shown to recipients</td></tr>
        <tr><td>From Name</td><td>The sender name shown to recipients</td></tr>
    </tbody>
</table>

{{-- Section 7 --}}
<p class="section-num">07</p>
<h1>Stripe &amp; Payments</h1>
<p>Go to <strong>Settings → Payments</strong> to configure Stripe. You need a Stripe account at <strong>stripe.com</strong>.</p>
<table>
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
    <li>Click <strong>Add endpoint</strong> and enter: <code>https://yourdomain.com/webhook/stripe</code></li>
    <li>Select events: <code>invoice.payment_succeeded</code>, <code>customer.subscription.deleted</code>, <code>checkout.session.completed</code></li>
    <li>Copy the <strong>Signing secret</strong> and paste it into Settings → Payments → Webhook Secret.</li>
</ol>

{{-- Section 8 --}}
<p class="section-num">08</p>
<h1>File Storage</h1>
<table>
    <thead><tr><th>Driver</th><th>Best For</th><th>Requirements</th></tr></thead>
    <tbody>
        <tr><td>Local</td><td>Small to medium deployments</td><td>None — files stored on your server</td></tr>
        <tr><td>AWS S3</td><td>Large scale or CDN delivery</td><td>AWS account, S3 bucket, IAM credentials</td></tr>
    </tbody>
</table>

{{-- Section 9 --}}
<p class="section-num">09</p>
<h1>reCAPTCHA Protection</h1>
<p>Go to <strong>Settings → reCAPTCHA</strong> to protect agent registration from spam bots.</p>
<ol>
    <li>Visit <strong>google.com/recaptcha</strong> and sign in.</li>
    <li>Click <strong>+ Create</strong> and choose reCAPTCHA v2 → "I'm not a robot".</li>
    <li>Add your domain to the allowed domains list.</li>
    <li>Copy the <strong>Site Key</strong> and <strong>Secret Key</strong> into Settings → reCAPTCHA.</li>
</ol>

{{-- Section 10 --}}
<p class="section-num">10</p>
<h1>Google Maps</h1>
<p>Go to <strong>Settings → Google Maps</strong> to enable interactive maps on property listings.</p>
<ol>
    <li>Go to <code>console.cloud.google.com</code> and sign in.</li>
    <li>Enable the <strong>Maps JavaScript API</strong> under APIs &amp; Services → Library.</li>
    <li>Go to <strong>APIs &amp; Services → Credentials</strong> and create an API Key.</li>
    <li>Paste the key into Settings → Google Maps and save.</li>
</ol>
<div class="callout">One API key powers all map features: property maps, address geocoding, and nearby places search.</div>

{{-- Section 11 --}}
<p class="section-num">11</p>
<h1>Brand &amp; Appearance</h1>
<table>
    <thead><tr><th>Setting</th><th>Description</th></tr></thead>
    <tbody>
        <tr><td>Logo</td><td>Appears in the admin sidebar and agent portal header</td></tr>
        <tr><td>Favicon</td><td>Small icon shown in browser tabs</td></tr>
        <tr><td>Primary Colour</td><td>Main action colour (buttons, links, highlights)</td></tr>
        <tr><td>Secondary Colour</td><td>Supporting accent colour</td></tr>
        <tr><td>Sidebar Colour</td><td>Admin sidebar background</td></tr>
        <tr><td>Body Font</td><td>Main text font across the platform</td></tr>
        <tr><td>Heading Font</td><td>Titles and headings font</td></tr>
        <tr><td>Admin Font</td><td>Font used in the admin panel</td></tr>
    </tbody>
</table>

{{-- Section 12 --}}
<p class="section-num">12</p>
<h1>Page Builder</h1>
<p>Go to <strong>Page Builder</strong> to create and edit custom pages using the visual drag-and-drop editor.</p>
<h3>System Pages</h3>
<table>
    <thead><tr><th>Page</th><th>URL</th><th>Description</th></tr></thead>
    <tbody>
        <tr><td>Home Page</td><td><code>/</code></td><td>The public-facing landing page</td></tr>
        <tr><td>Demo Landing</td><td><code>/demo</code></td><td>The demo sign-up page</td></tr>
    </tbody>
</table>

{{-- Section 13 --}}
<p class="section-num">13</p>
<h1>Frequently Asked Questions</h1>

<h3>An agent didn't receive their verification email. What do I do?</h3>
<p>Check that mail is configured correctly in Settings → Mail. Ask the agent to click "Resend verification email". If still failing, check your SMTP credentials and spam folders.</p>

<h3>A payment was made but the agent's account wasn't activated. Why?</h3>
<p>This is almost always a webhook issue. Verify that your Stripe webhook URL and Webhook Secret in Settings → Payments are correct. Check Stripe Dashboard → Developers → Webhooks → Recent Deliveries for errors.</p>

<h3>How do I reset the setup wizard?</h3>
<p>Open your <code>.env</code> file on the server and set <code>APP_INSTALLED=false</code>. The wizard becomes accessible again at <code>/setup</code>. No data is deleted.</p>

<h3>How do I update the app name?</h3>
<p>Edit <code>APP_NAME</code> in your <code>.env</code> file, then run <code>php artisan config:clear</code>.</p>

<h3>Where are uploaded files stored by default?</h3>
<p>With local storage, files are in <code>storage/app/public</code>. Make sure a symlink exists: run <code>php artisan storage:link</code> after deployment.</p>

<br><br>
<p style="text-align:center;font-size:10pt;color:#9ca3af;border-top:1pt solid #e5e7eb;padding-top:10pt;">
    {{ config('app.name') }} &nbsp;·&nbsp; Admin Documentation &nbsp;·&nbsp; {{ now()->format('Y') }}
</p>

</body>
</html>
