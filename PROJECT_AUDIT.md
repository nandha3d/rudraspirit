# RudraSpirit — Project Audit

**Date:** 2026-06-20
**Stack:** the base commerce CMS (v10.8) · Laravel 10 · custom theme `rudraspirit`
**Auditor:** Claude Code

## Scope & method

The codebase is a commercial off-the-shelf **Laravel commerce CMS** (thousands of stock files)
with a custom storefront theme layered on top (`resources/views/frontend/rudraspirit/*`,
custom routes, `SearchController::mukhi_info`, `public/assets/{css,js}/rudraspirit.*`).

A literal file-by-file audit of the entire CMS is not useful — the stock vendor code is
known/maintained upstream. This audit **thoroughly covers the custom layer** (where all
the project risk and value live) and **samples** the stock core for integration breaks.
Severity: 🔴 critical · 🟠 high · 🟡 medium · 🔵 low/enhancement.

---

## 1. 🔴 Security & repository hygiene (fix before any public push/deploy)

| # | Finding | Evidence | Fix |
|---|---------|----------|-----|
| 1.1 | **`.env` is committed to git** and `.env` is **not in `.gitignore`**. `APP_KEY` is exposed, `APP_DEBUG=true`, `APP_ENV=local`. | `git ls-files --error-unmatch .env` → tracked | `git rm --cached .env`; add `.env` to `.gitignore`; **rotate `APP_KEY`** and any real secrets; set `APP_DEBUG=false`, `APP_ENV=production` on the server. |
| 1.2 | **Full DB dumps committed / present in web root.** `database_backup.sql` & `shop.sql` are git-tracked; `live_database_backup.sql` (live data) and 6 other `*.sql` sit in the public project root. | root `*.sql`, `git ls-files \| grep sql` | Remove from repo + history (`git rm --cached`, consider `git filter-repo`); never store DB dumps in web root; move backups off-repo. |
| 1.3 | **~25 accidental junk files in root** from botched shell commands. | `0])`, `1])`, `get())`, `pluck('name')`, `toArray())`, `choice_options)`, `choice_options))`, `response.html`, `response_browser.html` | Delete. (See §8 cleanup command.) |
| 1.4 | **~17 scratch/one-off scripts in web root**, several DB-touching. Each is a publicly reachable PHP file unless blocked. | `import.php`, `fix_*.php`, `scratch_*.php`, `test_error.php`, `export_db.php`, `extract_*.py`, `rewrite_categories.py` | Delete or move to a non-public `scripts/` dir; ensure none are routable. |
| 1.5 | `APP_URL=http://commerce.test` (dev default) while the project deploys via webhook. | `.env` | Set real production `APP_URL` in server env. |
| 1.6 | the vendor CMS documentation PDF (29 MB) + `_ide_helper.php` shipped in repo/web root. | root listing | Remove from repo; `_ide_helper.php` is dev-only. |

> These are the highest-priority items. Everything else is quality.

---

## 2. 🟠 Hardcoded values (audit point #2)

### Logic hardcoding
- **Fake countdown timer.** `index.blade.php:130` calls `rsStartCountdownBoxes(..., 1000*60*60*76)` — a fixed **76-hour** window computed as `Date.now()+duration` on every page load, so it **resets on every refresh** and is **not tied to any real flash deal**. Misleading "Limited Time Deal". → Drive from an actual `FlashDeal` end date, or a configurable setting.
- **Mukhi spiritual dataset is hardcoded in the view.** `mukhi_info.blade.php` embeds a 14-entry PHP array (`$mukhiData[1..14]`: deity, planet, mantra, chakra, significance, 3× benefits, wearing day). It is **mapped to a product by regex-parsing the product name** (`SearchController::mukhi_info`, `preg_match('/(\d+)\s*Mukhi/i', ...)`). If a product name lacks "N Mukhi", `$mukhiNumber` is `null` and no data renders. **Not editable from admin.** → Move to a DB table / product custom-fields so admins can manage it. (Also see §3.)
- **Hardcoded category slug `rudraksha-beads`** in `index.blade.php:5` and `header.blade.php:2`; the whole storefront breaks/empties if that slug changes. → Make it a setting (`get_setting('rs_root_category')`).
- **"Ruling Deity" derived from `explode(',', $product->tags)[0]`** (`product_details.blade.php:25`) — repurposes the first tag as deity; fragile and implicit.
- **Magic DB-id branching.** `app.blade.php` switches custom-alert behaviour on `id == 1 / 200 / 300` and popups on `id == 100 / 1`. Hardcoded primary keys as business logic → brittle across environments. → Use a `type`/`key` column.
- **Hardcoded `bd` (Bangladesh) dial-code override** in `app.blade.php:1246` (`country.dialCode = '88'`) and `01xxxxxxxxx` placeholder — leftover from the vendor's locale, irrelevant to a Nepal/India rudraksha brand.

### UI hardcoding
- Fallback image filenames hardcoded throughout the theme (`Gemini_Generated_Image_*.webp`, `Logo_02-scaled.webp`). Acceptable as last-resort fallbacks, but the **hero slides** (`index.blade.php:13-43`) are a hardcoded array of 3 slides — not manageable from the admin slider. → Wire to the existing slider/banner manager.
- Feature row ("Free Shipping / 14-Day Returns / Premium Support") and all marketing copy are hardcoded translate() strings — fine for i18n, but not editable as content blocks.

### Config coupling
- **217 view files call `env(...)` directly.** With `php artisan config:cache` (required for production performance) `env()` outside `config/*` returns `null`, silently breaking Google Analytics, Facebook Pixel/CAPI, WhatsApp, tracking IDs, etc. → Route all through `config()` + a config file. (Mostly inherited base-CMS debt, but it blocks the standard prod optimisation.)

---

## 3. 🟠 Front UI ↔ backend wiring (audit point #3)

**Wired correctly (DB-driven, editable in admin product edit):**
name, description, price/unit_price, discount, tags, stock/quantity, variant choice_options,
taxes, min_qty, num_of_sale, rating, thumbnail. ✓

**Not wired / gaps:**
- 🟠 **Mukhi spiritual content is not in the backend** (see §2) — the richest part of each product page (deity, mantra, benefits) cannot be edited from the product edit screen. Highest-value gap for this niche.
- 🟠 **rudraspirit `product_details.blade.php` dropped all SEO meta.** Compared to the stock `frontend/product_details.blade.php` (which sets `meta_title`, `meta_description`, `meta_keywords`, Open Graph, Twitter cards, product schema), the custom page emits **none**. Product pages lose social/SEO rich data even though those fields exist and are editable in admin. → Port the `@section('meta*')` blocks into the rudraspirit page.
- 🟡 No **product image gallery** in the custom PDP — only `thumbnail` is shown; `photos`/variant images that admins upload aren't displayed (the stock theme has a slider; the JS in `app.blade.php` even references `.thumb-slider`/`.main-slider` that the rudraspirit PDP never renders).

---

## 4. 🟠 Account section, deals, flash sales (audit point #4)

- 🟠 **Account section is not themed.** `dashboard()` returns `frontend.user.customer.dashboard` — the **stock base-CMS** dashboard/sidebar, not a rudraspirit-styled one. Customers jump from the gold/serif storefront into the old default UI. Functionally linked (orders, wishlist, addresses, profile work) but visually inconsistent. → Reskin or wrap the customer panel in the rudraspirit shell.
- 🟠 **Flash sales are not wired into the storefront.** No `FlashDeal`/`flash_deal` reference anywhere under `resources/views/frontend/rudraspirit/`. The home "Limited Time Deal" is **decorative only** (fake timer, links to a category). Real flash deals configured in admin never surface on the custom home/PDP. → Render actual active flash deals (price + countdown to real end date).
- 🟡 Reviews surface only through **purchase history / order details** (account), not the product page (see §5).
- 🔵 Wishlist, cart drawer, currency switcher in the header are correctly wired to their routes. ✓ (Language switcher intentionally disabled.)

---

## 5. 🟠 Review form attached to product? (audit point #5) — **No**

- The rudraspirit PDP **displays** reviews (`product_details.blade.php:171-183`) but contains **no submission form**. Your screenshot ("Customer Reviews — No reviews yet… ") has no "Write a review" control.
- `HomeController::product()` already passes `$review_status` (1 if the user received this product) and `$order_id` to the view — **the custom template ignores both.**
- The stock theme implements the full flow (`openReviewOffcanvas` → `product_review_modal` → `reviews.store`, gated by `$review_status==1`). The backend (routes `reviews.store`, `product_review_modal`; `ReviewController`; admin moderation; club-point-for-review) is fully present.
- **Net:** customers can only review from purchase history, not from the product page. → Add the rating + comment form to the rudraspirit PDP, gated on `$review_status == 1`, posting to `route('reviews.store')`.

---

## 6. 🟠 Enterprise-grade assessment (audit point #6) — **Not yet**

Good foundations (Laravel 10, addon architecture, payment/i18n/multi-currency, queues available),
but the following block an "enterprise-grade" label:

| Area | Status | Note |
|------|--------|------|
| Secrets management | 🔴 | `.env`+`APP_KEY` in git, `APP_DEBUG=true`, DB dumps in repo (§1). |
| Automated tests | 🔴 | Only `tests/{Feature,Unit}/ExampleTest.php` (stock stubs). No coverage of custom logic. |
| Separation of concerns | 🟠 | Business logic & DB queries live **in Blade views** (`index.blade.php:5-11`, `header.blade.php:2-5` run Category/Product/Blog/TopBanner queries on every render). Belongs in controllers/view-composers/services. |
| Performance | 🟠 | Repeated `Category::where('slug','rudraksha-beads')` + product queries per page (header + index). No eager-load/cache. `config:cache` blocked by `env()` in views (§2). Large unoptimised `*.jpeg` (~0.5 MB each) used as bead assets. |
| Caching | 🟡 | No view/route/config cache strategy evident; no Redis/object cache for catalog. |
| CI/CD | 🟡 | `deploy.sh` + webhook only; no test gate, no pipeline (`.github/` present — verify it runs anything). |
| Observability | 🟡 | `storage/logs/laravel-*.log` committed/untracked; no centralized logging/error tracking (Sentry etc.). |
| Code hygiene | 🟠 | Scratch scripts & junk in web root (§1); dead/commented routes (commit `8e6a1c0` added dummy controllers + commented routes to mask a 500). |
| Accessibility/SEO | 🟡 | Custom PDP missing meta/schema (§3); icon-only buttons mostly have `title`/`aria-label` (good), but verify forms/labels. |

---

## 7. 🔵 Recommended additions & enhancements

**Make the niche shine (highest ROACI for a rudraksha store):**
1. Move mukhi metadata (deity/planet/mantra/chakra/benefits/wearing-day) into a DB table or product custom-fields → admin-editable, per-product, not regex-guessed.
2. Add the **review form** to the PDP + photo reviews + verified-buyer badge.
3. Add a **product image gallery** + zoom to the custom PDP (assets already uploaded).
4. **Lab-certificate** display (certificate image/PDF per product), origin/size/weight spec table — core trust signals for certified beads.

**Storefront / conversion:**
5. Wire **real flash deals** with genuine countdowns; drive the home "deal" from admin.
6. Reskin the **customer account** area to match the theme.
7. Restore **SEO**: per-product meta/OG/Twitter + JSON-LD `Product` schema with `aggregateRating` (you already compute ratings).
8. Manage **hero slides / banners / feature row / marquee** from admin instead of hardcoded arrays.

**Engineering / enterprise:**
9. Secrets hygiene (§1) + `config:cache`-safe config (§2).
10. Move queries out of Blade into controllers/view-composers; add catalog caching.
11. Add a real test suite (smoke tests for cart→checkout→order, reviews, auth) + CI gate.
12. Optimise images (WebP/AVIF, responsive `srcset`, compress the 0.5 MB JPEGs).
13. Add error tracking + log rotation; remove logs from repo.

---

## 8. Cleanup helper (junk files only — review before running)

```bash
# from repo root — removes the accidental shell-output files (NOT the .sql/scripts)
rm -f -- '0])' '1])' 'get())' "pluck('name')" 'toArray())' \
        'choice_options)' 'choice_options))' response.html response_browser.html
# stop tracking secrets/dumps (keeps local copies):
git rm --cached .env database_backup.sql shop.sql
echo ".env" >> .gitignore
```

> Done in this session: added a related bead image to the home "Limited Time Deal"
> right panel (`index.blade.php` `.rs-deal-visual` + `rudraspirit.css`). The fake
> countdown there is documented in §2 and left unchanged pending your decision on
> wiring it to a real deal.

---

# Audit #2 — post-deploy (2026-06-20)

The app was deployed to the Hostinger server (`rudraspirit.com`) during this round.
The path from 403 → 500 → working storefront surfaced several real issues; this
section records them and what was fixed.

## 9. 🔴 Vendor license backdoors & phone-home (FIXED this round)

The base CMS contained hidden calls to an external vendor activation server. Three of them were **remote admin-login
backdoors**: on a `"bad"` response they ran `auth()->login(<first admin user>)`
and redirected to the admin dashboard — i.e. the vendor (or anyone able to spoof
that response, or simply an unactivated install) could be handed an admin session.

| Location | Behaviour | Fix |
|---|---|---|
| `Payment/StripeController@checkout_payment_detail` | phone-home → **admin login** on "bad" | no-op |
| `Utility/NgeniusUtility@initPayment` | phone-home → **admin login** on "bad" | returns home, no call |
| `LanguageController@get_translation` | phone-home → **admin login** on "bad" | returns home, no call |
| `Payment/IyzicoController@initPayment` | phone-home, echoes "not Activated" | no-op |
| `CompareController@details` | phone-home + hardcoded `$rn="bad"` (always bailed) | keeps benign behavior, no call |
| `Utility/CategoryUtility@create_initial_category` | phone-home, disables on "no" | fail-open (`true`) |
| `Utility/NagadUtility`, `Utility/PayhereUtility` | phone-home for flutter wallet | fail-open |

Left in place (admin-initiated, not a backdoor): `AddonController` addon
verify/list, and a commented demo-import URL in `BusinessSettingsController`.

## 10. 🟠 Activation wizard removed
- Admin sidebar **"Features activation"** link removed; `activation.index` now
  redirects to the dashboard (the purchase-code / vendor activation wizard is gone).
- Install wizard was already disabled (`mapInstallRoutes()` commented out).
- Storefront has no vendor branding (footer uses `website_name`); the only vendor-name string was a code comment, now reworded.

## 11. 🟠 Deployment / hosting findings
- **Web root was misconfigured** → 403. Document root is the project root, with no
  front controller. Fixed: root `.htaccess` forwards into `public/`; added
  `public/index.php` + `public/.htaccess` (tracked). See commit `c517d3b`.
- **`public/assets` (50 MB) and `public/uploads` (13 MB) are gitignored** → not
  deployed by `git pull`, so vendor CSS/JS, fonts and images 404'd (broken layout +
  images). Shipped as `assets.zip` / `uploads.zip` for manual upload. *Recommendation:*
  pick a deploy story for assets (rsync/CI artifact, or un-ignore built assets).
- **Server DB was empty** — `php artisan migrate` created only the ~15 migration
  tables; the bulk of the schema lives in the install SQL. Imported a clean
  `mysqldump` (`rudra_server_import.sql`) after the original `database_backup.sql`
  failed to import (unescaped apostrophes from a hand-rolled backup script).
- **80 duplicate route names** → `route:cache` cannot run (dynamic routing only).
  Fixed 1 (`api.razorpay.payment`); 79 remain. Pre-existing.
- **`public/export_db.php` + `public/import_database.php`** were web-reachable DB
  export/import endpoints — **removed** (commit `c517d3b`).
- Server `.env` still needs `APP_ENV=production`, `APP_DEBUG=false`, real `APP_URL`,
  and `storage:link`.

## 12. Still open (carried forward)
- `.env` + `APP_KEY` tracked in git; DB dumps in repo (Audit #1 §1).
- Review form missing on the storefront PDP (Audit #1 §5).
- Account section not reskinned; flash deals not wired (Audit #1 §4).
- No automated tests; queries in Blade; 79 duplicate route names.
- Asset deployment story (see §11).

---

# Round-3 fixes (2026-06-20)

Fixed:
- 🔴 **Repo hygiene** — untracked `.env`, `_ide_helper.php`, `database_backup.sql`,
  `shop.sql`; gitignore now blocks `/.env`, root `*.sql`, `*.zip`, scratch scripts.
  Deleted ~25 junk + scratch files. `deploy.sh` now **backs up & restores the
  production `.env`** around every pull so untracking it can't wipe live config.
  *(Still TODO: rotate `APP_KEY`, and purge the secrets from git history.)*
- 🟠 **Review form on the PDP** — rating (stars) + comment + photo upload, gated on
  `$review_status == 1`, posting to `reviews.store`. Reviews now show stars + photos.
- 🟠 **PDP SEO** — `meta_title`/`description`/`keywords`, Open Graph, Twitter card,
  and JSON-LD `Product` schema (with `aggregateRating` when reviews exist).
- 🟡 **PDP image gallery** — shows `product.photos` with clickable thumbnails
  (falls back to thumbnail).
- 🟠 **Flash deals wired** — the home "Limited Time Deal" uses a real active
  `FlashDeal` (countdown to its `end_date`, link to the deal) when one exists;
  otherwise the configurable rolling countdown. Query guarded.
- ✅ **First test** — `tests/Unit/MukhiNumberTest.php` covers the mukhi-number parser.

Deliberately deferred (would risk "nothing broken" or is large):
- **79 duplicate route names** → `route:cache`. Two kinds: resource + explicit
  `/edit/{id}`,`/destroy/{id}` overrides (fix: `Route::resource(...)->except([...])`
  per resource), and web-vs-API name collisions (`brands.index` etc.; fix: prefix
  API names with `api.`). Mechanical but needs per-resource regression of admin
  CRUD URLs — do as a focused pass, not a bulk rename.
- **Account section reskin** — ~20 `frontend/user/*` views to re-theme; large.
- **Move catalog queries out of Blade** into view-composers/controllers + caching.
- **Asset deployment** — `public/assets`/`public/uploads` still shipped via zip;
  pick an rsync/CI-artifact story.

---

# Audit #4 — Pre-launch full review (2026-07-07)

**Scope:** engine architecture (in & out), mobile-app compatibility, VPS compatibility,
module enable/disable, hard-coded values, enterprise readiness. Launch target: ~1 week.

## 13. Engine overview

Three parallel surfaces over one Laravel 10 / PHP 8.2 / MySQL core (a commercial base commerce CMS + custom `rudraspirit` theme):

| Surface | Location | State |
|---|---|---|
| Blade storefront + admin | `resources/views/*` | Live, themed, SEO'd |
| V2 REST API (Flutter apps) | `routes/api.php`, `Api/V2/*` (296 routes) | Frozen, intact |
| V3 headless API | `routes/api_v3*.php`, `Api/V3/*`, `app/Services/*` | Built through Phase 5; tests/docs pending |

V3 is well-architected: service layer, response envelope, Sanctum auth,
`auth:sanctum`+`admin` on admin routes, per-tier rate limiters, CORS middleware,
HMAC-signed webhooks. Gaps: Phase 6 (factories/feature tests) not done;
`config('headless.enabled')` exists but is **never enforced** (dead kill switch);
`API_V3_*`/`API_CORS_ORIGINS`/`WEBHOOK_*` vars missing from `.env.example`.

## 14. 🔴 Launch blockers

| # | Finding | Impact | Fix |
|---|---|---|---|
| 14.1 | **`deploy.sh` runs `php artisan config:cache`, but payment gateways read `env()` directly at runtime** (`RazorpayController: env('RAZOR_KEY')`, `StripeController: env('STRIPE_SECRET')`, all OTP/SMS services, FCM key — 325 `env()` call sites in `app/`, 216 view files). With config cached, Dotenv never loads, so `env()` returns **null** → gateways get empty credentials and **payments/OTP/SMS fail silently**. | Revenue-critical | Short-term: remove `config:cache` from `deploy.sh` (keep `view:cache`). Long-term: move all gateway creds into `config/*` and switch call sites to `config()`. |
| 14.2 | **Mobile push notifications cannot work.** `app/Utility/NotificationUtility.php:99` posts to the FCM **HTTP v1** endpoint with a **placeholder project ID `myproject-b5ae1`**, legacy `'to'=>` payload shape, legacy `Authorization: key=` header (v1 requires OAuth2 service-account token), and `CURLOPT_SSL_VERIFYPEER=false`. Legacy FCM server keys were shut down by Google in 2024. | All app push dead | Rewrite `sendFirebaseNotification()` for HTTP v1: real project ID, service-account JSON → bearer token, `{"message":{...}}` payload, SSL verification on. |
| 14.3 | **Admin "Features activation" page is unreachable.** `BusinessSettingsController::activation()` redirects to the dashboard (collateral of removing the vendor purchase-code wizard). The page it should render (`backend/setup_configurations/activation.blade.php`) holds ~30 business toggles: vendor system, guest checkout, coupon system, pickup point, conversation, maintenance mode, HTTPS, social logins, email/customer verification, wallet, classified products… The POST route (`business_settings.update.activation`) still works — only the UI is gone. | Can't manage features | Restore `return view('backend.setup_configurations.activation');` — the view contains no purchase-code fields; it's safe. |
| 14.4 | **Secret rotation still pending** (carried from Audit #1). Old `.env` + `APP_KEY` remain in git history; `.env.example` ships a real-looking `APP_KEY` and `SYSTEM_KEY="12345"`. If production still uses that key, sessions/cookies are forgeable by anyone with repo access. | Auth integrity | Rotate `APP_KEY` on the server (invalidates sessions — do before launch, off-peak), set a random `SYSTEM_KEY`, strip the key from `.env.example`, optionally purge history with `git filter-repo`. |

## 15. 📱 Mobile app compatibility — **Yes, with conditions**

- The repo ships the stock Flutter apps (`Mobile_App/`): customer v5.70, seller v3.10,
  delivery-boy v3.90 — all built against the **V2 API**, which is present and frozen
  (296 routes, Sanctum auth, `app_language` middleware, OTP addon routes, business-settings
  config endpoint). The engine side is compatible.
- Required before the apps work: set the base URL + package id inside each Flutter
  project, add Firebase config (`google-services.json` / `GoogleService-Info.plist`),
  rebuild and publish. **Verify vendor's app↔CMS version pairing** (CMS v10.8 vs app v5.7).
- **Push is broken server-side until 14.2 is fixed.**
- OTP login works only after an SMS provider (Twilio etc.) is configured — and those
  creds are read via `env()`, so 14.1 applies.
- A future custom app can target the cleaner V3 API instead; for browser-based
  frontends set `API_CORS_ORIGINS` (defaults to `*`).

## 16. 🖥 VPS compatibility — **Yes**

Standard Laravel 10 stack; nothing Hostinger-specific in the app itself. VPS checklist:

- PHP 8.2+ (`ext-gd`, `zip`, `mbstring`, `bcmath`, `intl`, `curl`), MySQL 5.7+/8, Composer 2.
- Set the web-server document root to `public/` and **drop the root `.htaccess`
  forwarding hack** (that exists only because Hostinger's docroot is the project root).
- `php artisan storage:link`; `APP_ENV=production`, `APP_DEBUG=false`, real `APP_URL`.
- Cron: `* * * * * php artisan schedule:run` (note: `Console/Kernel::schedule()` is
  currently **empty** — nothing scheduled; add queue-retry/backup jobs here).
- Move `QUEUE_CONNECTION` from `sync` → `database`/`redis` + a supervised
  `queue:work` worker so mail/notifications stop blocking requests.
- Redis optional but supported (`predis` installed) for cache/session/queue.
- `deploy.sh` is webhook/Hostinger-oriented but portable; keep the
  `patches/CoreComponentRepository.php` re-apply step (neutralizes the vendor
  activation gate after every `composer install`).
- `route:cache` still impossible: **84 duplicate route names** (deploy falls back
  gracefully). `config:cache` must stay OFF until 14.1 is resolved.

## 17. 🧩 Module enable/disable — **Mostly yes, one break**

| Layer | Mechanism | Works? |
|---|---|---|
| 11 addons (affiliate, auction, club point, OTP, POS, refund, seller subscription, wholesale, offline payment, Paytm, Cybersource) | Admin → Addons → toggle (`AddonController@activation` sets `addons.activated`, clears the 24 h `addons` cache; code gates via `addon_is_activated()`) | ✅ |
| ~30 business feature toggles | Features-activation page | ❌ page redirects — see 14.3 |
| Payment/shipping method activation | separate pages (`payment.activation`, `shipping.activation`) | ✅ |
| V3 API kill switch | `headless.enabled` | ⚠️ defined but never checked — enforce or remove |

## 18. 🟠 Hard-coded values — status

**Fixed since Audit #1:** tracking/pixel/WhatsApp via `config/rudraspirit.php`; magic
alert/popup IDs centralized; hero slides DB-driven (with fallback); deal countdown from
`get_setting('rudraspirit_deal_end')`; root category from
`get_setting('rudraspirit_root_category')` (default `rudraksha-beads`); mukhi data moved
to a `mukhi_infos` table with full admin CRUD. ✅

**Remaining:**
- 🔴 FCM placeholder project ID (14.2).
- 🟠 `.env.example` real `APP_KEY`, `SYSTEM_KEY="12345"` (14.4).
- 🟠 **14 code sites disable SSL verification** (`CURLOPT_SSL_VERIFYPEER=false`) in
  payment/notification code — inherited base-CMS debt, MITM-exposed; enable verification.
- 🟡 `Gemini_Generated_Image_*.webp` fallback filenames across the theme (cosmetic;
  breaks quietly if assets are renamed).
- 🟡 `https://rudraspirit.com/sitemap.xml` hardcoded in `public/robots.txt` (wrong on staging).
- 🟡 Root leftovers to delete: `scratch_import.php`, `test_v3_endpoints.php`, empty
  `sitemap.xml` (shadowed by the route but junk in the docroot).
- 🔵 Payment endpoints (bKash/Khalti/Paymob/Tap) hardcode vendor URLs with env-based
  sandbox/live switches — normal.

## 19. 🟠 Enterprise-grade verdict — **functional-grade, not yet enterprise**

Solid: layered V3 architecture, addon modularity, license backdoors neutralized,
secrets untracked, SEO/structured data, hardened deploy script.

Still missing for "enterprise": test suite (3 test files, no CI gate — the GitHub
workflow only fires the deploy webhook), error tracking (no Sentry/log shipping),
queue+scheduler unused, no automated DB backups, 84 duplicate route names, SSL-verify
bypasses, secret rotation pending, no staging environment in evidence.

## 20. One-week launch order

1. **Day 1:** 14.1 (drop `config:cache` from deploy) + verify a live test payment; 14.3 (restore features page — one line); delete root leftovers.
2. **Day 2:** 14.4 rotate `APP_KEY`/`SYSTEM_KEY` off-peak; re-test login + payments.
3. **Day 3–4:** 14.2 FCM v1 rewrite if apps launch with the site; queue → database + worker; enable SSL verification in payment code.
4. **Day 5:** smoke-test checklist: register/login, OTP, cart→checkout on every enabled gateway, order mails, refund flow, admin CRUD, mobile app against prod API.
5. **Post-launch:** dedupe route names → `route:cache`; feature tests + CI gate; Sentry; scheduled backups.
