# Raidhaan Backend

Laravel 12 + Inertia + Vue 3 web application for POS/order management.

## Prerequisites

- PHP 8.2+
- Composer
- Node.js 20+

## Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

## Run

```bash
composer run dev
```

This starts:

- Laravel server
- Queue worker
- Log tailing
- Vite dev server

## Build frontend assets

```bash
npm run build
```

## Thermal printing (QZ Tray)

The app prints receipts through QZ Tray in web mode.

1. Ensure QZ Tray is running.
2. Configure certificate/private key via `.env` (`QZ_*` variables) or file paths.
3. Open an order and click **Print (QZ)**.

## Notes

- Receipt endpoint: `/api/orders/{order}/receipt`
- Inertia pages are in `resources/js/Pages/**/*.vue`
