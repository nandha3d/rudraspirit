# RudraSpirit — Project Audit

**Date:** 2026-06-20
**Stack:** Active eCommerce CMS v10.8 · Laravel 10 · custom theme `rudraspirit`
**Auditor:** Claude Code

## Scope & method

The codebase is the commercial **Active eCommerce CMS** (thousands of stock files)
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
| 1.5 | `APP_URL=http://active-ecommerce.test` (dev default) while the project deploys via webhook. | `.env` | Set real production `APP_URL` in server env. |
| 1.6 | `Active eCommerce CMS Documentation (v-10.8).pdf` (29 MB) + `_ide_helper.php` shipped in repo/web root. | root listing | Remove from repo; `_ide_helper.php` is dev-only. |

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
- **217 view files call `env(...)` directly.** With `php artisan config:cache` (required for production performance) `env()` outside `config/*` returns `null`, silently breaking Google Analytics, Facebook Pixel/CAPI, WhatsApp, tracking IDs, etc. → Route all through `config()` + a config file. (Mostly stock-AE debt, but it blocks the standard prod optimisation.)

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

- 🟠 **Account section is not themed.** `dashboard()` returns `frontend.user.customer.dashboard` — the **stock Active eCommerce** dashboard/sidebar, not a rudraspirit-styled one. Customers jump from the gold/serif storefront into the old default UI. Functionally linked (orders, wishlist, addresses, profile work) but visually inconsistent. → Reskin or wrap the customer panel in the rudraspirit shell.
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
