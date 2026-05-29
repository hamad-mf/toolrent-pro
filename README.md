# ToolRent Pro

A multi-tenant tool & equipment rental management system built with Laravel 12 and Bootstrap 5.3 (served via CDN — **no Node.js build step required**).

## Features

- Multi-tenancy with automatic tenant scoping (`TenantScope`, `BelongsToTenant`)
- Role-based access control: Super Admin, Shop Admin, Manager, Counter Staff, Floor Staff
- Tool inventory with categories, images, QR codes, and maintenance status
- Full rental lifecycle: booking/reservation → checkout → return → PDF invoice
- Feature-flag enforcement and tenant plan limits for enabled modules, users, and tools
- Per-tenant theming (accent color, logo, favicon, custom CSS) with light/dark/auto mode
- Business reports and analytics

## Requirements

- PHP 8.2+
- Composer
- SQLite (default) or MySQL/PostgreSQL

> This project uses Bootstrap and supporting libraries via CDN. There is **no** Vite/npm build step. The `package.json`, `vite.config.js`, `resources/css`, and `resources/sass` files from the default Laravel scaffold are not used.

## Setup

```bash
# Install PHP dependencies, copy env, generate key, migrate + seed, and link storage
composer run setup
```

The `setup` script runs:

1. `composer install`
2. Copies `.env.example` to `.env` (if missing)
3. `php artisan key:generate`
4. `php artisan migrate --force --seed`
5. `php artisan storage:link`

### Manual setup

If you prefer to run the steps yourself:

```bash
composer install
copy .env.example .env        # Windows (use `cp` on macOS/Linux)
php artisan key:generate
php artisan migrate --seed
php artisan storage:link      # REQUIRED: makes uploaded tool images publicly accessible
```

> **Important:** `php artisan storage:link` must be run once. Without it, uploaded tool
> images stored in `storage/app/public` will not be reachable from the browser. Re-run it
> after a fresh clone or if `public/storage` is missing.

## Running

```bash
php artisan serve
```

Then visit the URL shown in the terminal (default `http://127.0.0.1:8000`).

## Scheduled tasks

Overdue rentals are flagged by a scheduled console command. To process the schedule locally:

```bash
php artisan schedule:work
```

## Testing

```bash
composer test
```

## License

MIT.
