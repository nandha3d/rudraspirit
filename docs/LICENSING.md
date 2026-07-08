# Licensing (client integration)

This storefront can validate itself against our own **License Server** (the
`license-server/` project in this repo). It replaces the third-party activation
service the base engine used to phone home to — with no backdoor: a failed check
never logs anyone in and never changes auth. The storefront is **never** taken
down by licensing; only the admin panel / addon installs can be gated.

## Configure

Set these in the storefront `.env` (see `.env.example`, "Licensing (client)"):

| Var | Meaning |
|-----|---------|
| `LICENSE_ENFORCE` | `off` (default), `warn`, `addons`, or `admin` — see below |
| `LICENSE_SERVER_URL` | Base URL of the license server (default `https://license.animazon.in`) |
| `LICENSE_PRODUCT` | Product slug (default `animazon-engine`) |
| `LICENSE_KEY` | This deployment's license key |
| `LICENSE_DOMAIN` | Domain to license under (blank = host from `APP_URL`) |
| `LICENSE_SIGNING_SECRET` | **Must equal** the license server's `LICENSE_SIGNING_SECRET` |
| `LICENSE_CACHE_TTL` | Minutes to cache a result (default 720 = 12h) |
| `LICENSE_FAIL_OPEN` | On unreachable server + no cache: `true` = stay up, `false` = treat unlicensed |

## Enforcement modes

- **off** — licensing disabled; no network calls. An unconfigured install is
  never accidentally bricked. (Default.)
- **warn** — everything works; admin sees a warning banner when unlicensed.
- **addons** — as `warn`, plus **module gating**: `addon_is_activated()` only
  returns true for modules the deployment's plan/license entitles (local addon
  toggle AND license entitlement), and new addon installs are blocked when
  unlicensed.
- **admin** — as `addons`, plus the **admin panel is locked** when unlicensed
  (a clear notice page; the storefront stays fully up so customers are unaffected).

### Plan-driven modules

Plans are managed on the license server (Admin → Plans) and sold at
`https://license.animazon.in/pricing` (JSON: `GET /api/v1/plans` for embedding
on the main website). A plan lists the module identifiers it includes
(`affiliate_system`, `club_point`, `auction`, `refund_request`, `otp_system`,
`offline_payment`, `seller_subscription`, `wholesale`, `pos_system`, …).
The verify response returns the union of plan modules + any per-license extras;
in `addons`/`admin` mode the client enforces it at runtime, so upgrading a
customer's plan on the server flips their modules on at the next license
re-check (within `LICENSE_CACHE_TTL`, default 12 h — or immediately after
`php artisan cache:clear` on the client).

Safety: if the license server is unreachable (fail-open) or licensing is off,
module gating entitles everything — no data is never treated as "not entitled",
so a server outage cannot switch off modules on a live store. Unreachable
results are cached for at most 15 minutes so entitlements resync quickly.

A definitive "invalid" answer from the server is always honored. `LICENSE_FAIL_OPEN`
only applies when the server is unreachable and there is no cached result.

## How it works

- `App\Services\License\LicenseClient` calls `POST /api/v1/licenses/verify`,
  verifies the response's `X-License-Signature` (HMAC-SHA256 with the shared
  secret), caches the result for `LICENSE_CACHE_TTL` minutes, and exposes
  `isValid()`, `entitledAddons()`, `isAddonEntitled($id)`.
- `App\Http\Middleware\EnsureLicensed` (`licensed` alias) enforces the `admin`
  mode on admin routes.
- `AddonController::check_activation()` gates addon installation in `addons`/`admin`
  modes.
- `resources/views/backend/inc/license_banner.blade.php` shows the admin warning.

## Notes

- Clear the cached result after changing config: `php artisan cache:clear`.
- Addon *entitlements* from the license are available via `LicenseClient::isAddonEntitled()`
  if you want to gate specific addons; by default addons remain controlled by the
  local Admin → Addons toggle and the install gate above.
