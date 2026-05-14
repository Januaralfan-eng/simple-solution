# Creative Agency Platform

> Premium creative tech agency website — Laravel Modular Monolith + HTMX + AlpineJS + TailwindCSS

## Tech Stack

| Layer       | Tech                                    |
|-------------|----------------------------------------|
| Backend     | PHP 8.2, Laravel 12                    |
| Frontend    | Blade, HTMX 2.x, AlpineJS 3.x         |
| Styling     | TailwindCSS 3.x                        |
| Database    | MySQL 8.x                              |
| Cache/Queue | Database (Redis-ready)                 |
| Auth        | Laravel Sanctum + Spatie Permissions   |
| Media       | Spatie MediaLibrary + Intervention Image |
| SEO         | Spatie Sitemap + Schema.org            |
| Deployment  | Any PHP hosting (Vercel-compatible API)|

## Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Public/          # HomeController, BlogController, PortfolioController, ContactController
│   │   ├── Admin/           # Full CMS admin panel controllers
│   │   └── Auth/            # Login/logout
│   ├── Requests/            # Form Requests (all validation here)
│   └── Middleware/
├── Services/
│   ├── CMS/                 # PageService, SettingsService
│   ├── Portfolio/           # PortfolioService
│   ├── Blog/                # BlogService
│   ├── Contact/             # ContactService
│   ├── SEO/                 # SeoService
│   ├── Analytics/           # AnalyticsService
│   └── Media/               # MediaService
├── Models/                  # Project, Post, Contact, Setting, Service, etc.
├── Jobs/                    # SendContactNotificationJob
├── Exceptions/              # ContactException, PortfolioException
└── Helpers/                 # helpers.php

resources/views/
├── layouts/                 # app.blade.php, admin.blade.php
├── components/
│   ├── layout/              # navbar, footer
│   ├── sections/            # portfolio-card, blog-card
│   ├── ui/                  # reusable UI components
│   └── admin/               # admin-specific components
├── pages/
│   ├── home/                # Landing page
│   ├── portfolio/           # Portfolio index + show
│   ├── blog/                # Blog index + show
│   ├── services/            # Services page
│   ├── about/               # About page
│   └── contact/             # Contact page
└── admin/                   # Full admin panel views
```

## Quick Start

```bash
# 1. Clone and install
git clone <repo-url> creative-agency
cd creative-agency
composer install
npm install

# 2. Environment setup
cp .env.example .env
php artisan key:generate

# 3. Database
php artisan migrate
php artisan db:seed

# 4. Assets
npm run build

# 5. Storage link
php artisan storage:link

# 6. Serve
php artisan serve
```

## Module Overview

### CMS
- Fixed layout architecture — structure is code, content is DB
- Admin edits text, images, ordering, and visibility
- Clone/duplicate pages
- Custom SEO metadata per page

### Portfolio
- Project grid with HTMX category filter (zero page reload)
- Like system (IP-based, no auth required)
- View counter
- GitHub + Live demo links
- Image gallery with fullscreen preview
- Tech stack badges

### Blog
- Full SEO: OpenGraph, Twitter Card, Article Schema, BreadcrumbList
- HTMX-powered live search
- Categories + Tags system
- Reading time calculator
- Related posts
- Author profiles

### Contact
- Honeypot spam protection
- Rate limiting (5 req/hour/IP)
- SMTP email notification via queue
- Optional WhatsApp API notification
- Admin inbox with read/unread status

### SEO
- Dynamic sitemap.xml
- Robots.txt
- Schema.org: Organization, Article, BreadcrumbList
- Canonical URLs
- OpenGraph + Twitter Card for all pages

### Analytics
- Internal page view tracking
- Portfolio views/likes analytics
- Contact form statistics
- Traffic chart (HTMX partial reload)

## User Roles

| Role   | Access                              |
|--------|-------------------------------------|
| Admin  | Full access including user management |
| Editor | CMS content management              |
| Client | Client portal only                  |

## Performance Strategy

- **TTFB**: Server-rendered HTML, no client hydration
- **LCP**: Lazy loaded images with WebP support
- **CLS**: Fixed layouts, no dynamic content shifts
- **FID**: Alpine.js for minimal interactivity
- **HTMX**: Partial DOM updates (no full page reloads for filters/search)
- **Cache**: Route-level caching + query result caching

## Environment Variables

See `.env.example` for all required variables.

Key variables:
```env
AGENCY_NAME="Your Agency Name"
AGENCY_EMAIL=hello@youragency.com
WHATSAPP_NUMBER=6281234567890
GA_MEASUREMENT_ID=G-XXXXXXXXXX
KAMUS_LOGGING_ENABLED=true
KAMUS_API_KEY=your-kamus-api-key
```

## Deployment

```bash
# Production build
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Queue worker (for email notifications)
php artisan queue:work --queue=emails --tries=3
```

## Error Logging

All exceptions are logged with structured context for `kamus.zasha.online` integration:

```php
Log::error('ServiceName::method failed', [
    'exception' => get_class($e),
    'message'   => $e->getMessage(),
    'user_id'   => auth()->id(),
    'input'     => $data,
    'trace'     => $e->getTraceAsString(),
]);
```

Set `KAMUS_LOGGING_ENABLED=true` and `KAMUS_API_KEY` to enable remote error reporting.

---

Built with ❤️ using Laravel 12 + HTMX + AlpineJS + TailwindCSS
