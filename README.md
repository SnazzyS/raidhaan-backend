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

## Local receipt printing

1. Open an order.
2. Click **Print Receipt**.
3. A local print window opens and triggers the browser print dialog.

## Notes

- Receipt endpoint: `/api/orders/{order}/receipt`
- Inertia pages are in `resources/js/Pages/**/*.vue`
