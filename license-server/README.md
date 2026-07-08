# License Server

A standalone Laravel 10 application that issues and validates license keys for
your products (built to replace the third-party activation service the base
engine used to phone home to). It is deployed **separately** from the storefront
— its own database and domain (e.g. `https://license.rudraspirit.com`).

It does **not** contain any auto-login / backdoor behaviour. A failed check only
ever reports "not licensed"; it never grants access to anything.

---

## What it does

- Issue license keys (auto-generated `XXXX-XXXX-XXXX-XXXX` or your own).
- **Domain-lock** each key to a limited number of domains (activation limit).
  New domains auto-activate while under the limit; extra domains are rejected.
- **Expiry** per license (or perpetual).
- **Status**: active / suspended / revoked.
- **Per-addon entitlements**, each with its own optional expiry.
- Signs every API response with **HMAC-SHA256** so clients can reject spoofed
  answers.
- A simple admin panel to manage all of the above.

---

## Requirements

- PHP 8.2+ (`gd`, `mbstring`, `bcmath`, `curl`, `pdo_mysql` or `pdo_sqlite`)
- MySQL 5.7+/8 (or SQLite for small installs)
- Composer 2

## Install / deploy

```bash
cd license-server
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate

# Edit .env:
#   APP_URL                → https://license.yourdomain.com
#   DB_*                   → your database
#   LICENSE_SIGNING_SECRET → a strong random value (see below)
#   ADMIN_EMAIL / ADMIN_PASSWORD → your admin login (used by db:seed)

# Generate a signing secret and paste it into LICENSE_SIGNING_SECRET
php -r "echo bin2hex(random_bytes(32)).PHP_EOL;"

php artisan migrate --force
php artisan db:seed --force        # creates the admin user + a sample license

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Point the web server document root at `license-server/public/`.

The **same** `LICENSE_SIGNING_SECRET` must be configured on every client
(`LICENSE_SIGNING_SECRET` in the storefront's `.env`).

---

## Admin panel

Visit `https://license.yourdomain.com/admin` and sign in with the seeded admin
account. From there you can create/edit licenses, set the activation limit and
expiry, suspend/revoke, view and release domain activations, and grant per-addon
entitlements.

---

## API

All responses use this envelope and carry an `X-License-Signature` header
(HMAC-SHA256 of the exact JSON body with the shared secret):

```json
{ "data": { ... }, "meta": { "server_time": "...", "version": "1.0" } }
```

### `POST /api/v1/licenses/verify`

Body:
```json
{ "key": "XXXX-XXXX-XXXX-XXXX", "domain": "store.com", "product": "rudraspirit-engine" }
```

`data` on success:
```json
{
  "valid": true,
  "status": "active",
  "product": "rudraspirit-engine",
  "domain": "store.com",
  "expires_at": null,
  "activation_limit": 1,
  "activations_used": 1,
  "addons": [ { "identifier": "affiliate_system", "label": "Affiliate System", "expires_at": null } ],
  "message": "License is valid."
}
```

`valid` is `false` with a `status` of `not_found`, `suspended`, `revoked`,
`expired`, `activation_limit_reached`, or `invalid_domain` otherwise.

Domain matching is normalized: `https://www.store.com/` is treated the same as
`store.com`. The first time a key is seen on a new domain (and it is under its
activation limit) that domain is recorded automatically.

### `POST /api/v1/licenses/deactivate`

Body: `{ "key": "...", "domain": "store.com" }` — releases a domain's activation
slot so it can be reused elsewhere.

---

## Data model

- `licenses` — key, product, customer, status, expiry, activation_limit, notes
- `license_activations` — one row per (license, domain); the domain-lock ledger
- `license_addons` — per-license addon entitlements with optional expiry

---

## Security notes

- Responses are signed; clients reject any response whose signature does not
  match (prevents a proxy returning a fake "valid").
- The verify endpoint is rate-limited (`LICENSE_VERIFY_RATE_LIMIT`/min/IP).
- Keep `APP_DEBUG=false` and `APP_ENV=production` in production.
- Rotate `LICENSE_SIGNING_SECRET` only in a coordinated push to server + clients.
