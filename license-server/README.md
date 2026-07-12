# Animazon License Server

Standalone PHP license/activation server for the RudraSpirit / Active eCommerce
platform. Issues purchase codes, binds them to domains, and answers the
activation checks the shop's addon installer makes.

No framework, no Composer, no build step — plain PHP + SQLite. Drop it on shared
hosting (Hostinger hPanel) and it runs.

---

## What it does

- Generates purchase codes (`RS-XXXX-XXXX-XXXX-XXXX`) for the main item and for addons.
- Binds a code to a domain on first activation (configurable max domains per code).
- Answers both a clean JSON API **and** the legacy endpoint shape the shop already speaks,
  so the client only needs a base-URL change.
- Token-protected admin UI to issue / revoke / reset licenses.

## Files

```
license-server/
  index.php        front controller (all routes)
  config.php       settings (admin token, storage, prefix)
  src/             Db.php, Licenses.php
  views/           admin.php, login.php
  data/            SQLite DB lives here (auto-created, web-blocked)
  .htaccess        routes everything to index.php + blocks data/src/views/config
```

---

## Deploy to Hostinger (subdomain `license.animazon.in`)

1. In hPanel create/confirm the subdomain `license.animazon.in`. Note its document
   root (e.g. `public_html/license`).
2. Upload the **contents** of this `license-server/` folder into that document root
   (so `index.php` and `.htaccess` sit at the web root of the subdomain).
3. Set a strong admin token. Either edit `config.php` (`admin_token`) or, better,
   add an environment variable in hPanel → **Advanced → PHP Configuration**:
   ```
   LICENSE_ADMIN_TOKEN = <long-random-string>
   ```
4. Make sure the `data/` directory is writable (755/775). The SQLite file is created
   automatically on first request.
5. Visit `https://license.animazon.in/` → should return
   `{"service":"Animazon License Server","status":"ok",...}`.
6. Visit `https://license.animazon.in/admin`, log in with the token, and issue a code.

> PHP 7.4+ required (8.x fine). SQLite PDO is enabled by default on Hostinger.
> To use MySQL instead, set `LICENSE_DB_DRIVER=mysql` plus `LICENSE_DB_*` vars.

---

## Connecting the shop (already wired)

The shop reads the server URL from `config/license.php` (env `LICENSE_SERVER_URL`).
It's already set to `https://license.animazon.in`. To disable the check for a
self-hosted install set `LICENSE_CHECK_ENABLED=false` in the shop's `.env`.

The shop calls these (all served by this app):

| Endpoint | Purpose |
|---|---|
| `POST /activation/verify-purchase-code/{code}` | code is valid |
| `GET  /item_info/{code}` | main-item code active (empty body if not) |
| `GET  /registered-addon-info/{code}` | addon code active (empty body if not) |
| `GET  /registered-addon-list/{code}` | returns `["boundDomain"]` |

## Clean JSON API (for new integrations)

```
POST /api/verify
  { "purchase_code": "...", "domain": "shop.com", "addon_identifier": "pos_system" }
  -> { "valid": true, "activated": false, "bound_domains": [], "message": "..." }

POST /api/activate
  { "purchase_code": "...", "domain": "shop.com", "addon_identifier": "pos_system" }
  -> { "success": true, "message": "Activated", "domain": "shop.com" }
```

Domains are normalized (scheme, `www.`, path and port stripped; registrable pair kept)
so `https://www.shop.com/x` and `shop.com` are the same activation.

## Admin API (header `X-Admin-Token: <token>`)

```
GET  /admin/api/licenses
POST /admin/api/licenses            { type, addon_identifier?, buyer_name?, buyer_email?, max_domains?, note? }
POST /admin/api/licenses/revoke     { code }
POST /admin/api/licenses/activate   { code }
POST /admin/api/licenses/reset      { code }   # unbind all domains
```

## Local test

```
cd license-server
LICENSE_ADMIN_TOKEN=dev php -S 127.0.0.1:8801 index.php
# then: curl http://127.0.0.1:8801/
```

## Security notes

- `config.php`, `src/`, `views/`, and `data/` are blocked from direct web access by
  `.htaccess`. Keep that file (Apache/LiteSpeed on Hostinger honor it).
- Rotate `LICENSE_ADMIN_TOKEN` if it ever leaks; it's the only admin gate.
- The activation API is intentionally public (client sites call it unauthenticated),
  but it only ever confirms/binds codes you issued — it cannot mint new ones.
