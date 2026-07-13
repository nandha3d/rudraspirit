# Zolo Kart — White-Label Deployment Guide

**Zolo Kart** is the product (this codebase + custom theme + finance/inventory/pincode
modules + licensing). Each client gets the same engine, rebranded and deployed to
their own domain, licensed per-deployment.

Per-client deploy = **change the brand, point at their domain, issue a license.**

---

## 1. Brand name

```bash
php artisan brand:set "Client Store Name"
php artisan brand:set "Client Store Name" --motto="Their tagline"
```

Sets the visible brand (`site_name` / `site_motto`) used across storefront, admin,
page titles and emails. The product default (when no `site_name` is set and no
`APP_NAME` env) is **Zolo Kart** — see `config/app.php`.

- Logo / favicon: **Admin → Business Settings → Website Setup**.
- `.env`: `APP_NAME="Client Store Name"` (page-title fallback).

## 2. Domain

- `.env`: `APP_URL=https://clientdomain.com`
- Web server vhost / subdomain pointing at `public/`.
- `LICENSE_DOMAIN=clientdomain.com` (the domain the license activates under).

## 3. License (per deployment)

Issue a license + plan on the license server, then set on the client `.env`:

```
LICENSE_SERVER_URL=https://license.animazon.in
LICENSE_PRODUCT=animazon-engine
LICENSE_KEY=<the client's key>
LICENSE_SIGNING_SECRET=<must match the license server's secret>
LICENSE_ENFORCE=admin        # off | warn | addons | admin
LICENSE_FAIL_OPEN=true
```

Plans/modules are defined on the license server
(`license-server/database/seeders/DatabaseSeeder.php`). Module keys the engine
gates on: `accounting`, `gst_reports`, `profit_reports`, `partner_share`,
`purchase_inventory`, `indian_pincode`, `live_currency_rates`, plus the stock
addon identifiers. Enterprise entitles all of them.

## 4. Post-deploy data tasks

- **Live currency:** add the scheduler cron `* * * * * php /path/artisan schedule:run`
  (runs `currency:update-rates` daily), or run it manually.
- **Pincode directory:** `php artisan pincode:import storage/app/pincodes.csv`
  (All-India Pincode CSV from data.gov.in). A 12-row sample is seeded by default.

---

## What is client-specific vs product-wide

| Client-specific (per deploy) | Product-wide (ships in repo) |
|---|---|
| `site_name` / logo / colors | Theme + modules code |
| `APP_URL` / domain | `config/app.php` default name = Zolo Kart |
| `LICENSE_KEY` / plan | License client + gating logic |
| Currency + pincode data | `brand:set`, `pincode:import` tooling |
