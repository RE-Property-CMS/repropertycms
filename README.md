# RePropertyCMS — Developer Reference

> **White-label real estate CMS** sold as full source code.
> Every buyer self-hosts their own instance. This document is the complete onboarding guide for any developer who purchases or inherits this codebase.

---

## Table of Contents

1. [Product Overview](#1-product-overview)
2. [Tech Stack](#2-tech-stack)
3. [Local Development Setup](#3-local-development-setup)
4. [Architecture Overview](#4-architecture-overview)
5. [Directory Structure](#5-directory-structure)
6. [Authentication & Guards](#6-authentication--guards)
7. [Routing Architecture](#7-routing-architecture)
8. [Database Schema](#8-database-schema)
9. [Models & Relationships](#9-models--relationships)
10. [Design Patterns](#10-design-patterns)
11. [Livewire Components](#11-livewire-components)
12. [Subscription & Payments (Stripe)](#12-subscription--payments-stripe)
13. [Setup Wizard](#13-setup-wizard)
14. [Brand & Appearance System](#14-brand--appearance-system)
15. [Frontend Architecture](#15-frontend-architecture)
16. [Email & Notifications](#16-email--notifications)
17. [File Storage](#17-file-storage)
18. [Deployment & CI/CD](#18-deployment--cicd)
19. [Coding Conventions](#19-coding-conventions)
20. [Known Issues & Tech Debt](#20-known-issues--tech-debt)

---

## 1. Product Overview

**RePropertyCMS** is a multi-role real estate property listing platform sold as white-label source code. Buyers purchase the code, configure it via an installation wizard, and self-host on their own server.

### Roles

| Role | Access | Auth Guard |
|------|--------|-----------|
| **Super Admin** | Full platform control — agents, plans, properties, revenue | `admin` |
| **Agent** | Manages own property listings, subscription, billing | `agent` |
| **Public User** | Browses and contacts agents via public property pages | none |

### Core Concepts

- **Agent** — a paying customer who creates and publishes property listings
- **Property** — a real estate listing owned by an agent; can be draft or published
- **Plan** — a Stripe subscription plan with a `credits` limit (max published properties)
- **Subscription** — links an agent to a plan via Stripe; controls what the agent can publish
- **Brand** — configurable colors, fonts, logo, favicon stored in `brand_settings` DB table

---

## 2. Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Framework | Laravel | 11.9 |
| Language | PHP | 8.2+ |
| Reactive UI | Livewire | 3.5 |
| CSS (agent/auth/Livewire) | Tailwind CSS | 3.4.14 |
| CSS (public property pages) | Bootstrap | 5.3.2 |
| CSS (admin panel) | Material Tailwind | custom |
| Build tool | Vite | 4.0 |
| Auth | Session-based (custom multi-guard) + Laravel Sanctum (API) |
| Payments | Stripe (`stripe/stripe-php ^14.10`) |
| Webhooks | `spatie/laravel-stripe-webhooks ^3.8` |
| Email | SendGrid (`sendgrid/sendgrid ^8.1`) |
| Storage | Local or AWS S3 (`league/flysystem-aws-s3-v3`) |
| Maps | Google Maps (embed + Places API) |
| reCAPTCHA | Google reCAPTCHA v3 |
| Real-time | Pusher + Laravel Echo (configured; client-side disabled) |
| Error tracking | Sentry (`sentry/sentry-laravel ^4.13`) |
| Queue / Cache | Database driver (default) |
| Database (dev) | SQLite or MySQL |
| Database (prod) | MySQL |

---

## 3. Local Development Setup

### Requirements

- PHP 8.2+
- Composer
- Node.js 18+ and npm
- MySQL (or SQLite for quick start)
- Laragon / Valet / Sail

### Steps

```bash
# 1. Clone the repository
git clone <repo-url> && cd RePropertyCMS

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies and build assets
npm install
npm run dev         # or npm run build for production

# 4. Copy environment file and configure
cp .env.example .env
php artisan key:generate

# 5. Configure .env (DB, Stripe, Mail at minimum)
# Then run migrations
php artisan migrate

# 6. Open your browser and complete the setup wizard at /setup
```

### Key .env Variables

```dotenv
APP_NAME="Your Brand Name"
APP_URL=https://yoursite.com
APP_INSTALLED=false          # set to true after setup wizard
SETUP_KEY=your_secret_key    # required to access the setup wizard

DB_CONNECTION=mysql
DB_DATABASE=your_db
DB_USERNAME=your_user
DB_PASSWORD=your_pass

STRIPE_PUBLIC_KEY=pk_live_...
STRIPE_SECRET_KEY=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

MAIL_MAILER=sendgrid         # or smtp
SENDGRID_API_KEY=SG...

FILESYSTEM_DISK=local        # or s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_BUCKET=...

RECAPTCHA_SITE_KEY=...
RECAPTCHA_SECRET_KEY=...

GOOGLE_MAPS_API_KEY=...

DEPLOY_TOKEN=                # webhook secret for CI/CD
DEPLOY_BRANCH=main
```

---

## 4. Architecture Overview

```
Browser / Client
       │
       ├── Public pages  (Bootstrap 5, Alpine.js via Livewire)
       ├── Agent portal  (Tailwind CSS + Livewire 3)
       └── Admin panel   (Material Tailwind + Bootstrap)
              │
       ┌──────▼──────┐
       │  Laravel 11  │
       │   Web Layer  │
       ├──────────────┤
       │  Middleware  │  CheckIfAppSetup → AgentMiddleware → BackendMiddleware
       │  Controllers │  Agent/ · Backend/ · Auth/ · Setup
       │  Livewire    │  52+ reactive components
       │  Services    │  EnvService
       │  Observers   │  PropertyObserver
       │  Jobs        │  SendContactFormEmail (queued)
       │  Listeners   │  SendEmailVerification · SendRegisteredNotification
       └──────┬───────┘
              │
       ┌──────▼───────┐
       │ MySQL Database│  26 models · 41 migrations
       └──────────────┘
              │
       External APIs:
       Stripe · SendGrid · AWS S3 · Google Maps · reCAPTCHA · Sentry
```

### Request Lifecycle

1. Every request passes through `CheckIfAppSetup` middleware first
   - If `APP_INSTALLED != true` → redirect all traffic to `/setup`
   - If installed → verify `integration_settings` DB record exists
2. Route-specific middleware then applies (`agent`, `admin`, `verified`)
3. Controller or Livewire component handles the request
4. Livewire components communicate via browser events (not HTTP)

---

## 5. Directory Structure

```
app/
  Console/Commands/          # Artisan commands
  Enums/
    BannerType.php           # Image | Video | Slider (property header type)
    VideoType.php            # YouTube | Vimeo | Wistia | Dropzone
  Helper/
    Helpers.php              # Global helpers (asset_s3, etc.) — autoloaded
  Http/
    Controllers/
      Agent/                 # 13 controllers (property, images, video, floorplan…)
      Backend/               # 9 controllers (admin panel)
      Auth/                  # 8 controllers (register, login, password, verify)
      SetupController.php    # Installation wizard (9 steps)
      StripeWebhookController.php
      PropertyController.php # Public property display
      SubscriptionController.php
    Middleware/
      CheckIfAppSetup.php    # Installation gatekeeper (global on 'web' group)
      AgentMiddleware.php    # Agent portal guard
      AdminMiddleware.php    # Admin panel guard
      VerifyRecaptcha.php    # reCAPTCHA v3 validation
    Requests/                # (empty — validation done inline; see §20)
  Jobs/
    SendContactFormEmail.php # Queued dual-email for contact forms
  Listeners/
    SendEmailVerificationNotification.php
    SendRegisteredNotification.php
  Livewire/                  # 36+ reactive UI components (see §11)
  Mail/
    ContactAgentMail.php
    ContactUserMail.php
  Models/                    # 26 Eloquent models (see §9)
  Notifications/             # 18 notification classes (mail-channel only)
  Observers/
    PropertyObserver.php     # Fires on property creation → notify admin
  Services/
    EnvService.php           # Safe .env read/write
  Traits/
    ManagesStripeCustomers.php  # On Agents model — Stripe customer management

config/
  app.php                    # installed flag, setup_key, APP_ADDRESS
  stripe.php                 # Stripe API keys
  services.php               # recaptcha, google_map, mail services

database/
  migrations/                # 41 migration files

resources/
  views/
    setup/                   # 9 wizard step views
    agent/                   # Agent portal views (100+)
    admin/                   # Admin panel views
    livewire/                # Livewire component views (37 files)
    layouts/
      app.blade.php           # Agent portal layout
      guest.blade.php         # Public/auth layout
      default.blade.php       # Public property layout
      agents/default1.blade.php  # Agent portal layout (primary)
    includes/brand-styles.blade.php  # Injected into ALL layout heads
    mail/                    # Email templates
  js/
    app.js                   # Vite entry
    bootstrap.js             # Axios + CSRF setup
  css/
    app.css                  # Vite CSS entry

routes/
  web.php                    # Agent + setup + public + webhooks
  admin.php                  # Admin panel (prefix: /admin)
  auth.php                   # Auth routes
  api.php                    # Sanctum API (/api/user)

public/
  build/                     # Vite compiled assets
  css/                       # Pre-compiled CSS
  js/                        # Pre-compiled JS
  images/brand/              # Logo and favicon (set via admin/wizard)
  deploy.php                 # CI/CD webhook receiver

deploy.sh                    # Server deployment script
```

---

## 6. Authentication & Guards

The application has **three separate authentication guards**:

| Guard | Model | Middleware | Login Route |
|-------|-------|-----------|------------|
| `agent` | `App\Models\Agents` | `AgentMiddleware` | `/login` |
| `admin` | `App\Models\Backend\Admin` | `AdminMiddleware` | `/admin/sign-in` |
| `web` | `App\Models\User` | default | — |

### Agent Guard

- `Agents` model implements `Authenticatable` and `MustVerifyEmail`
- Registration → email verification required before portal access
- Password reset sends `CustomResetPasswordNotification`
- reCAPTCHA v3 required on registration (`VerifyRecaptcha` middleware)

### Admin Guard

- `Admin` model (in `app/Models/Backend/Admin.php`)
- Login at `/admin/sign-in`
- No self-registration — admins created via setup wizard or seeder

### Middleware Logic

**`CheckIfAppSetup`** runs on every web request:
```
APP_INSTALLED != true  →  Only /setup* allowed; everything else → /setup
APP_INSTALLED == true  →  Block /setup* (except /setup/database-repair)
                          Verify integration_settings DB row exists
                          If DB row missing → /setup/database-repair
```

**`AgentMiddleware`**: Checks `Auth::guard('agent')` — redirects to `/login` if unauthenticated.

**`AdminMiddleware`**: Checks `Auth::guard('admin')` — redirects to `/admin/sign-in` if unauthenticated.

**`VerifyRecaptcha`**: Applied to agent registration route — validates reCAPTCHA v3 token with 0.5 score threshold.

---

## 7. Routing Architecture

| File | URL Prefix | Middleware | Purpose |
|------|-----------|-----------|---------|
| `routes/web.php` | `/` | `web`, `CheckIfAppSetup` | Agent portal, setup, public pages |
| `routes/admin.php` | `/admin` | `admin` | Admin panel |
| `routes/auth.php` | `/` | `guest` / `auth` | Registration, login, password |
| `routes/api.php` | `/api` | `auth:sanctum` | Sanctum REST API |

### Key Route Groups

**Setup Wizard (`/setup/*` — public pre-install):**
- `/setup` → key verification entry
- `/setup/requirements` → `/setup/branding` → `/setup/final` — wizard steps
- `POST /setup/test-database|mail|stripe|storage` — AJAX connection tests

**Agent Portal (`/agent/*` — middleware: `agent`, `verified`):**
- `/agent/dashboard` — agent home
- `/agent/property/address/{id?}` — property create/edit (multi-step form, single route)
- `/agent/property/*` — amenities, description, price, images, galleries, floorplans, videos, documents, topbar
- `/agent/profile` — profile management
- `/agent/billing` — subscription and payments

**Admin Panel (`/admin/*`):**
- `/admin` — admin dashboard (KPIs, revenue charts)
- `/admin/agents` — agent listing, status, delete, password reset
- `/admin/properties` — all properties with expiry tracking
- `/admin/plans` — plan CRUD with Stripe sync
- `/admin/subscriptions` — subscription listing
- `/admin/revenue` — payment history
- `/admin/pages` — CMS page management
- `/admin/settings/*` — mail, stripe, storage, captcha, brand

**Public:**
- `/pricing` — subscription plans for public
- `/{unique_url}` — property detail page (catch-all, must be last route)
- `/share/{unique_url}` — shareable property link (no login required)
- `POST /property-view` — contact form AJAX handler
- `POST /webhook/stripe` — Stripe webhook

---

## 8. Database Schema

### Core Tables

| Table | Purpose | Soft Deletes |
|-------|---------|-------------|
| `agents` | Agent accounts (auth) | No |
| `agent_addresses` | Agent address/map data | No |
| `admins` | Admin accounts | No |
| `users` | Standard Laravel users | No |
| `properties` | Property listings | Yes (2026-03) |
| `property_images` | Property image files | No |
| `property_videos` | YouTube/Vimeo/upload URLs | No |
| `property_matterport` | 3D tour embed URLs | No |
| `property_floorplans` | Floor plan images | No |
| `property_floorplan_images` | Individual hotspot images | No |
| `property_galleries` | Gallery groups | No |
| `property_gallery_images` | Gallery images | No |
| `property_documents` | PDF/file attachments | No |
| `property_slider` | Hero slider images | No |
| `property_amenities` | Property ↔ amenity links | No |
| `amenities` | Global amenity tags | No |
| `hotspots` | Floor plan interactive points | No |
| `hotspot_property_images` | Hotspot ↔ image pivot | No |
| `plans` | Subscription plans (Stripe-synced) | No |
| `subscriptions` | Agent subscriptions | No |
| `payments` | Payment transaction log | No |
| `credit_logs` | Agent credit activity | No |
| `countries` | Country lookup | No |
| `states` | State/province lookup | No |
| `pages` | CMS pages | No |
| `brand_settings` | Single-row brand config | No |
| `integration_settings` | Setup wizard completion flags | No |
| `notifications` | Laravel notification queue | No |

### Key Foreign Key Constraints

```sql
properties.agent_id         → agents.id  (cascade delete)
property_amenities.property_id → properties.id (cascade delete)
property_galleries.property_id → properties.id (cascade delete)
hotspot_property_images: pivot between hotspots ↔ property_images
```

### Indexes

```sql
INDEX properties(expiry_date)
INDEX properties(agent_id, expiry_date)
```

---

## 9. Models & Relationships

### `Agents` (primary auth model)

```
Agents
 ├── hasMany  Properties           (agent_id)
 ├── hasMany  publishedProperties  (agent_id, published=1)
 ├── hasMany  Subscription         (agent_id)
 ├── hasOne   Agent_addresses      (agent_id)
 ├── hasOne   activeSubscription() (latest active Subscription)
 └── trait    ManagesStripeCustomers → createOrGetStripeCustomer()

Methods:
 hasActiveSubscription() → bool
 getTotalPublishedPropertiesCountAttribute() → int
 sendCustomEmailVerificationNotification()
 sendPasswordResetNotification()
```

### `Properties` (central entity)

```
Properties
 ├── belongsTo  Agents             (agent_id)
 ├── hasMany    PropertyAmenities
 ├── hasMany    Property_images
 ├── hasMany    Property_videos
 ├── hasMany    Property_matterport
 ├── hasMany    PropertyFloorplans
 ├── hasMany    PropertyDocuments
 ├── hasMany    PropertySlider
 ├── hasMany    PropertyGalleries
 ├── hasOne     States             (state_id)
 └── hasOne     Countries          (country_id)

Casts:
 main_section → BannerType enum (Image|Video|Slider)

Scopes:
 isPublished() → published=1 AND expiry_date > now()

Soft Deletes: yes
```

### `Subscription`

```
Subscription
 ├── belongsTo  Agents (agent_id)
 └── belongsTo  Plan   (stripe_price → stripe_plan_id)
```

### `Plan`

```
Plan
 └── hasOne  Subscription (stripe_plan_id → stripe_price)

Fields: name, price, stripe_plan_id, interval, credits, active
```

### `PropertyFloorplans`

```
PropertyFloorplans
 ├── hasMany  Hotspot
 └── hasMany  PropertyFloorplanImages
```

### `Hotspot`

```
Hotspot
 ├── belongsTo    PropertyFloorplans
 └── belongsToMany  Property_images (pivot: hotspot_property_images)
```

### `PropertyGalleries`

```
PropertyGalleries
 └── hasMany  PropertyGalleryImages
```

---

## 10. Design Patterns

### Observer Pattern

**`PropertyObserver`** — registered in `AppServiceProvider`:
- `created()` → sends `PropertyCreated` notification to admin email
- `updated()`, `deleted()` — stubs (currently empty)

### Event / Listener Pattern

| Event | Listener | Action |
|-------|---------|--------|
| `Illuminate\Auth\Events\Registered` | `SendEmailVerificationNotification` | Sends `CustomVerifyEmailNotification` to agent |
| `Illuminate\Auth\Events\Registered` | `SendRegisteredNotification` | Sends `AgentRegistered` to agent + `AdminRegisteredNotification` to admin |

### Queue / Job Pattern

**`SendContactFormEmail`** (`ShouldQueue`):
- Triggered from: `PropertyController::Contact_Form()` and `Livewire\PropertyView`
- Sends two emails: one to the user (contact confirmation), one to the agent (lead notification)
- Runs on the `database` queue driver by default

### Service Pattern

**`EnvService`** (`app/Services/EnvService.php`):
- Single responsibility: safely read and write `.env` variables
- `set(array $data)` — updates or appends keys without breaking other values
- Calls `config:clear` after writes in production, `config:cache` when safe
- Used exclusively by: `SetupController`, `SettingsController`

### Trait Pattern

**`ManagesStripeCustomers`** (on `Agents` model):
- `createOrGetStripeCustomer()` — creates Stripe customer if none exists; handles graceful recovery if customer was deleted from Stripe dashboard
- Stores `customer_id` on agent record

### Livewire Event Pattern

Livewire components communicate via **browser events** (not HTTP):

```php
// Sender (any component):
$this->dispatch('show-publish-confirm', id: $id);

// Receiver (same or parent component — Alpine):
x-on:show-publish-confirm.window="confirmPublishId = $event.detail.id"

// Receiver (another Livewire component):
#[On('refresh')]
class Dashboard extends Component { ... }
```

### Enum Pattern

```php
// app/Enums/BannerType.php
enum BannerType: string {
    case Image  = 'image';
    case Video  = 'video';
    case Slider = 'slider';
}

// Used as model cast:
protected $casts = ['main_section' => BannerType::class];
```

---

## 11. Livewire Components

All components are in `app/Livewire/` with views in `resources/views/livewire/`.

### Component Architecture Rules

1. **Single root element** — every Livewire view must have exactly one root element. Sibling `<style>` blocks break wire:click and `$wire`.
2. **Alpine event listeners** — use `x-on:event-name.window` syntax, never `@event-name` (Blade parses `@show`, `@click`, etc. as directives).
3. **`@verbatim`** — wrap `<style>` blocks containing `@media` in `@verbatim/@endverbatim` if they are siblings to Blade expressions.
4. **`#[On('event')]`** — Livewire 3 attribute for cross-component listening.

### Component Reference

| Component | Route/Context | Key Features |
|-----------|--------------|-------------|
| `Agent\Dashboard` | `/agent/dashboard` | Subscription alerts, stats, publish/delete, Stripe portal |
| `Agent\Properties\Index` | `/agent/properties` | Paginated grid, publish/delete modals, credit check |
| `Agent\Plans` | Modal (global) | Stripe Checkout, promo codes; listens `open-agent-plans` |
| `Agent\Billing` | `/agent/billing` | Subscription overview |
| `Agent\Notifications` | `/agent/notifications` | Notification list |
| `Agent\NotificationCount` | Topbar | Real-time badge count |
| `Agent\Slider\Index` | Property slider tab | Drag-and-drop ordering via livewire-sortable |
| `Agent\Topbar\Choose` | Property topbar tab | Image/Video/Slider type selection |
| `Agent\Topbar\Image` | Property topbar tab | Hero image upload |
| `Agent\Topbar\Video` | Property topbar tab | Hero video URL |
| `Agent\Document\Index` | Property documents tab | Document upload/delete/rename |
| `Agent\Profile\Index` | `/agent/profile` | Profile overview |
| `Agent\Profile\EditDetails` | Profile | Name, bio, contact |
| `Agent\Profile\EditAddress` | Profile | Address editing |
| `Agent\Profile\EditSocialMedia` | Profile | Social links |
| `Agent\Profile\AddProfileImage` | Profile | Profile photo with Cropper.js |
| `Agent\Profile\AddLogoImage` | Profile | Agency logo with Cropper.js |
| `Agent\Profile\ChangePassword` | Profile | Password change |
| `Admin\Plans\Index` | `/admin/plans` | Stripe plan sync, delete; listens `refresh` |
| `Admin\Plans\Create` | Modal | Plan creation; listens `open-create-plan` |
| `Admin\Plans\Edit` | Modal | Plan editing; listens `open-edit-plan` |
| `Admin\Pages\Create` | Modal | CMS page creation; listens `save-page` |
| `Admin\Pages\Lists` | `/admin/pages` | Paginated CMS page list |
| `Admin\Subscriber\Index` | `/admin/subscriptions` | Paginated subscriber list |
| `Amenity\Index` | Property amenities tab | Amenity checkbox selection |
| `Amenity\AddNewAmenity` | Modal | New amenity; listens `open-add-new-amenity` |
| `Document\Index` | Property documents tab | Document listing |
| `Floorplan\Index` | Property floorplans tab | Floor plan + hotspot editor |
| `Map\Index` | Property map tab | Google Maps display |
| `Map\UpdateMap` | Property map tab | Map coordinate editing |
| `PhotoLibrary\Index` | Property images tab | Image gallery management |
| `PhotoLibrary\AddNewImage` | Property images tab | Livewire Dropzone upload |
| `Video\Index` | Property video tab | Video listing |
| `Video\Add` | Property video tab | Video URL/upload form |
| `Video\View` | Property video tab | Video player |
| `Tour\Index` | Property 3D tour tab | Matterport embed |
| `Public\Subscribe` | `/pricing` | Unauthenticated plan selection |
| `PropertyView` | `/{unique_url}` | Full public property detail + contact form |

---

## 12. Subscription & Payments (Stripe)

### Flow

```
Agent signs up
  → visits /agent/billing or /pricing
  → Livewire Agent\Plans modal opens (event: open-agent-plans)
  → selects plan → POST /agent/checkout
  → SubscriptionController creates Stripe Checkout Session
  → redirect to Stripe hosted checkout
  → Stripe processes payment
  → Stripe fires webhook to POST /webhook/stripe
  → StripeWebhookController handles event
```

### Webhook Events Handled

| Stripe Event | Handler | Effect |
|-------------|---------|--------|
| `customer.subscription.created` | `handleSubscriptionCreated()` | Creates local Subscription record; sends welcome emails |
| `customer.subscription.updated` | `handleSubscriptionUpdated()` | Updates subscription period; auto-unpublishes if over credit limit |
| `customer.subscription.deleted` | `handleSubscriptionDeleted()` | Soft-deletes all agent properties; sends expiration emails |

### Credit System

- Each Plan has a `credits` integer (max published properties)
- On `publishProperty()`, Livewire checks `published count < plan.credits`
- On subscription downgrade (webhook updated): oldest published properties are auto-unpublished to fit new credit limit
- Agent can buy additional credits at `/agent/credit-plans`

### Billing Portal

Agents with active subscriptions can access the Stripe Billing Portal:
```php
// Agent\Dashboard::stripePortal()
$stripe->billingPortal->sessions->create([
    'customer' => $agent->customer_id,
    'return_url' => route('agent.dashboard'),
]);
```

---

## 13. Setup Wizard

### Access Control

1. Visit `/setup` — prompted for `SETUP_KEY` from `.env`
2. Session key `setup_verified` is set after correct key entry
3. All subsequent steps require this session key

### Steps

| Step | Route | Saves To |
|------|-------|---------|
| 0 | `/setup` | Session (`setup_verified`) |
| 1 | `/setup/requirements` | Nothing — display only |
| 2 | `/setup/database` | `.env` via EnvService |
| 3 | `/setup/admin` | Database (`admins` table) |
| 4 | `/setup/mail` | `.env` via EnvService |
| 5 | `/setup/stripe` | `.env` via EnvService |
| 6 | `/setup/storage` | `.env` via EnvService |
| 7 | `/setup/captcha` | `.env` via EnvService |
| 8 | `/setup/branding` | `public/images/brand/` + `brand_settings` table |
| 9 | `/setup/final` | `.env`: `APP_INSTALLED=true` + `integration_settings` DB row |

### AJAX Test Endpoints

```
POST /setup/test-database  → validates PDO connection
POST /setup/test-mail      → sends test email
POST /setup/test-stripe    → validates Stripe API key via Balance::retrieve()
POST /setup/test-storage   → validates S3 bucket access
```

### After Completion

- `APP_INSTALLED=true` written to `.env`
- `integration_settings` row created with `is_setup=true`
- `CheckIfAppSetup` middleware then blocks all `/setup/*` routes

---

## 14. Brand & Appearance System

### Storage

Single-row table `brand_settings`:

| Column | Type | Purpose |
|--------|------|---------|
| `primary_color` | string | Main brand color (e.g., buttons, accents) |
| `secondary_color` | string | Secondary UI color |
| `accent_color` | string | Highlight color |
| `accent_2_color` | string | Second highlight |
| `sidebar_color` | string | Admin sidebar background |
| `font_body` | string | Body text font (Google Fonts name) |
| `font_heading` | string | Heading font |
| `font_admin` | string | Admin panel font |
| `logo_path` | string\|null | Relative path to logo file |
| `favicon_path` | string\|null | Relative path to favicon file |
| `website_url` | string\|null | Brand website URL |

### How It Works

`resources/views/includes/brand-styles.blade.php` is included in the `<head>` of every layout:

```html
<style>
  :root {
    --primary-color: #...;
    --secondary-color: #...;
    /* etc. */
  }
</style>
<link rel="icon" href="/images/brand/favicon.png">
<!-- Google Fonts dynamically loaded based on selected fonts -->
```

Brand settings are **cached for 1 hour** under key `brand_settings`. Cache is cleared whenever `saveBrand()` or `saveBranding()` is called.

### Updating Brand

- **Via admin panel**: `/admin/settings/brand` — color pickers, font dropdowns, file upload
- **Via setup wizard**: `/setup/branding` step

Logo and favicon are stored at `public/images/brand/logo.{ext}` and `public/images/brand/favicon.{ext}`.

---

## 15. Frontend Architecture

### CSS Stack by Area

| Area | Framework |
|------|---------|
| Agent portal, auth, Livewire views | Tailwind CSS |
| Public property detail pages | Bootstrap 5 |
| Admin panel | Material Tailwind + Bootstrap |
| Setup wizard | Tailwind Play CDN + Alpine.js CDN (no build step needed) |

### CSS Files (`public/css/`)

| File | Size | Purpose |
|------|------|---------|
| `brand.css` | 14.9 KB | Main custom theme (renamed from `realtyniterface.css`) |
| `custom.css` | 14.7 KB | Admin custom theme |
| `public-custom.css` | 9.3 KB | Public site styles |
| `responsive.css` | 8.7 KB | Responsive overrides |
| `view-property.css` | 3.2 KB | Property detail page |
| `admin/custom.css` | — | Admin-only overrides |

### CSS Custom Properties

Brand colors are injected as CSS variables at root level — use them in all custom CSS:

```css
color: var(--primary-color);
background: var(--secondary-color);
font-family: var(--font-body);
```

### JavaScript

**Entry point**: `resources/js/app.js` → compiled by Vite to `public/build/`

```js
// app.js
import './bootstrap';          // Axios + CSRF setup
import 'livewire-sortable';    // Drag-and-drop for Livewire
window.App = { csrf: ... };    // Legacy CSRF compatibility
```

**CSRF setup** (`bootstrap.js`):
- Reads token from meta tag or `XSRF-TOKEN` cookie
- Attaches to mutating requests (POST, PUT, PATCH, DELETE)
- Axios configured with `withCredentials: true`

### Icons

- **FontAwesome 6.4.2** — primary (`fa fa-*`, `fas fa-*`)
- **Feather Icons** — used with `data-feather` attribute via Alpine.js

### JS Plugins (`public/plugins/`)

| Plugin | Purpose |
|--------|---------|
| Pannellum | 360° panorama viewer |
| Magnific Popup | Lightbox gallery |
| Owl Carousel / Slick / FlexSlider / Jssor | Slider/carousel options |
| Cropper.js | In-browser image cropping |
| SummerNote | WYSIWYG rich text editor |
| Flatpickr | Date/time picker |
| Sortable.js | Drag-and-drop (also via livewire-sortable) |
| QRCode.min.js | QR code generation for property URLs |
| floor-plans-edit.js | Custom canvas hotspot floor plan editor |

### Build Commands

```bash
npm run dev      # Vite dev server with HMR
npm run build    # Production build → public/build/
```

---

## 16. Email & Notifications

### Channels

All notifications use the **mail channel only**. No SMS or Slack channels are configured.

### Notification Classes (`app/Notifications/`)

| Class | Recipient | Trigger |
|-------|---------|---------|
| `AgentRegistered` | Agent | Registration |
| `AdminRegisteredNotification` | Admin | Agent registration |
| `CustomVerifyEmailNotification` | Agent | Registration (email verify) |
| `CustomResetPasswordNotification` | Agent | Password reset request |
| `SubscriptionWelcome` | Agent | Stripe subscription.created webhook |
| `AdminSubscriptionNotification` | Admin | Stripe subscription.created webhook |
| `AgentSubscriptionRenewed` | Agent | Stripe subscription.updated webhook |
| `AdminSubscriptionRenewed` | Admin | Stripe subscription.updated webhook |
| `AgentSubscriptionExpired` | Agent | Stripe subscription.deleted webhook |
| `AdminSubscriptionExpired` | Admin | Stripe subscription.deleted webhook |
| `PropertyCreated` | Admin | Property Observer created() |
| `AgentPropertyPublished` | Agent | Property published |
| `AdminPropertyPublished` | Admin | Property published |
| `AgentUnpublishProperty` | Agent | Auto-unpublish on downgrade |
| `RenewalReminder` | Agent | Subscription expiry warning |
| `GenericNotification` | Any | Utility class |

### Mailable Classes (`app/Mail/`)

| Class | Purpose |
|-------|---------|
| `ContactAgentMail` | Notifies agent of property inquiry |
| `ContactUserMail` | Confirms inquiry to the user |

### Email Templates

All email views are in `resources/views/mail/`. They use a shared layout at `resources/views/mail/layout.blade.php`.

### Admin Email Configuration

Admin email address is read from `config('services.admin_email')` or `.env ADMIN_EMAIL`. All admin notifications are sent there.

---

## 17. File Storage

### Configuration

Controlled by `FILESYSTEM_DISK` in `.env`:

| Value | Storage | Helper output |
|-------|---------|--------------|
| `local` | `public/` directory | `asset($path)` |
| `s3` | AWS S3 bucket | S3 URL |

### `asset_s3()` Helper

Defined in `app/Helper/Helpers.php`, auto-loaded globally:

```php
// Use this instead of asset() for any user-uploaded file:
asset_s3($property->property_images->first()->thumb)
```

It checks `FILESYSTEM_DISK` and returns the correct URL automatically.

### Upload Security

- File type whitelisting on all upload controllers
- Files stored outside of `public/` where possible, or in subdirectories not publicly browsable
- Image paths stored as relative paths in DB; resolved at render time via `asset_s3()`

### Brand Files

Logo and favicon are stored at:
```
public/images/brand/logo.{ext}
public/images/brand/favicon.{ext}
```
These are served directly via `asset()` (always local, not S3).

---

## 18. Deployment & CI/CD

### Files

| File | Purpose |
|------|---------|
| `deploy.sh` | Server-side deployment script |
| `public/deploy.php` | Git-provider-agnostic webhook receiver |

### How It Works

Any git provider (GitHub, GitLab, Bitbucket, Gitea, etc.) sends a POST webhook to your server when you push code. `deploy.php` verifies the token and runs `deploy.sh` in the background.

### Setup (one-time per server)

1. Edit `deploy.sh` — set `SITE_DIR` to your server's absolute path
2. Add to `.env`:
   ```dotenv
   DEPLOY_TOKEN=long-random-secret
   DEPLOY_BRANCH=main
   ```
3. In your git provider, add a webhook:
   - **URL**: `https://yoursite.com/deploy.php?token=long-random-secret`
   - **Event**: Push
   - **Content-Type**: `application/json`

### Multi-Environment (Staging + Production)

Each server has its own `.env` with its own `DEPLOY_BRANCH`. Register one webhook URL per server in your git provider. The branch filter in `deploy.php` prevents a staging push from deploying to production.

```
Staging server .env:   DEPLOY_BRANCH=staging
Production server .env: DEPLOY_BRANCH=main
```

### What `deploy.sh` Does

```
1. Prevent concurrent deploys (lock file)
2. Load .env
3. git fetch + git reset --hard origin/$BRANCH
4. git clean (preserves .env, storage/, public/images/brand/)
5. composer install --no-dev
6. php artisan migrate --force
7. php artisan config:cache + route:cache + view:cache + event:cache
8. Create storage symlink if missing
9. npm ci + npm run build
10. php artisan queue:restart
11. php artisan horizon:terminate (if enabled)
12. chmod 775 storage/ bootstrap/cache/
13. Log start/end times to storage/logs/deploy.log
```

### Manual Artisan Commands

```bash
php artisan migrate             # Run pending migrations
php artisan config:clear        # After .env changes
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan queue:restart       # After code changes affecting jobs
./vendor/bin/pint               # Code formatting
```

---

## 19. Coding Conventions

### PHP / Laravel

- **PHP version**: 8.2+ — use enums, named arguments, and match expressions where appropriate
- **Naming**:
  - Controllers: `PascalCase` + `Controller` suffix
  - Models: `PascalCase` (note: some legacy models use `snake_case` — do not repeat this)
  - Livewire components: `PascalCase` class, kebab-case view path
  - Blade views: `kebab-case.blade.php`
  - Routes: `kebab-case` URLs, named as `area.resource.action` (e.g., `agent.property.edit`)
- **Return types**: Add return types to all new methods
- **Validation**: New code should use Form Request classes (`app/Http/Requests/`) — not inline `Validator::make()`
- **Queries**: Always scope queries to `agent_id` in agent-facing code; never trust user input for ownership

### Blade

- Prefer `@endsection` over `@stop` for clarity
- Never use `@event-name` as an Alpine listener — it collides with Blade directives. Use `x-on:event-name` instead
- Inline `<style>` blocks inside Livewire views must be **inside** the single root `<div>`, not siblings to it
- Use `@verbatim / @endverbatim` around `<style>` blocks that contain `@media` queries

### Livewire

- Every component view must have exactly **one root element**
- Use `$this->dispatch('event-name', key: value)` for browser events (Livewire 3 syntax)
- Use `#[On('event-name')]` attribute for cross-component listeners
- Do not store sensitive IDs in public Livewire properties without authorization checks

### CSS

- Use CSS custom properties (`var(--primary-color)`) — never hardcode brand colors
- New UI in the agent portal and admin: Tailwind utility classes
- New UI in public property pages: Bootstrap 5 grid + utility classes
- Component-scoped styles: use short unique prefixes (e.g., `.pl-` for property listing, `.ag-` for agent dashboard)

### Security

- Always use `escapeshellarg()` before passing any variable to `exec()` or shell commands
- Never expose raw file system paths in responses
- File uploads: whitelist allowed MIME types and extensions; validate server-side
- Stripe webhooks: always verify signature via `Webhook::constructEvent()`

---

## 20. Known Issues & Tech Debt

These are documented for the next developer — fix them as time allows.

| # | Location | Issue | Priority |
|---|---------|-------|---------|
| 1 | `StripeWebhookController::handleSubscriptionUpdated()` | Hardcoded email `ra@odysseydesign.us` exempt from auto-unpublish. **Must be removed** before distributing as white-label. | Critical |
| 2 | `app/Http/Requests/` | Empty — all validation is done inline in controllers with `Validator::make()`. New features should use Form Request classes. | Medium |
| 3 | `SetupController.php` | Monolithic (18K+ lines). Should be split into step-specific controllers or invokable action classes. | Medium |
| 4 | `Properties` model | Three overlapping `agent` relationships: `agent()`, `agentRelation()`, `agents()`. Consolidate to one. | Low |
| 5 | `PropertyAmenities` model | `protected $with = ['Amenities']` causes N+1 eager loading globally. Replace with explicit `->with('Amenities')` at query time. | Low |
| 6 | `Agents` model | `protected $with = ['properties']` — same N+1 risk on all agent queries. | Low |
| 7 | Model naming | Some models use `PascalCase` (`Properties`) while others use `snake_case` (`Property_images`, `credit_logs`). Standardize to `PascalCase`. | Low |
| 8 | Form Request validation | No form request classes exist. Validation is scattered across controllers. | Medium |
| 9 | Pusher / Laravel Echo | Client-side Echo is commented out in `resources/js/bootstrap.js`. Real-time notifications are not functional. | Low |
| 10 | `app/Http/Controllers/Auth/` | Some controllers lack return type declarations. | Low |
| 11 | Property contact form | `PropertyController::Contact_Form()` sends mail directly (not queued). Move to the existing `SendContactFormEmail` job. | Low |

---

*This document should be kept up to date as the codebase evolves. When adding a new major feature, add a section here.*
