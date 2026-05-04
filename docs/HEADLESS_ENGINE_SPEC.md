# Animazon Shopping — Headless Engine Specification

> **Version**: 1.0 | **Created**: 2026-05-03 | **Status**: Approved
> **Purpose**: Transform this monolithic Laravel CMS into a headless e-commerce engine.
> Any AI agent or developer MUST read this file before making changes to the API layer.

---

## 1. PROJECT CONTEXT

- **Framework**: Laravel 10, PHP 8.2+
- **Database**: MySQL (database name in `.env`)
- **Auth**: Laravel Sanctum (token-based)
- **Existing APIs**: V2 in `routes/api.php` + `routes/api_seller.php` (for Flutter mobile app)
- **Admin Panel**: Blade-based, in `resources/views/backend/`
- **Frontend**: Blade-based, in `resources/views/frontend/`
- **Models**: 175 Eloquent models in `app/Models/`
- **Helpers**: `app/Http/Helpers.php` (3787 lines, global functions)

---

## 2. DECISIONS (LOCKED)

| Item | Decision |
|------|----------|
| V2 API | KEEP frozen. New work = V3 only |
| Admin Blade Panel | KEEP as-is. Add V3 Admin API alongside |
| Auth Strategy | Sanctum (primary) |
| Frontend | Agnostic (Blade, Next.js, Nuxt, Flutter, React Native) |
| Multi-tenancy | No. Single-tenant. Deploy per client |
| GraphQL | No. REST only |
| Payments | All gateways via unified PaymentOrchestrator |
| Execution | Phased: Foundation → Services → Storefront → Admin → Webhooks → Tests |

---

## 3. STRICT RULES

### Rule 1: File Locations
```
V3 controllers       → app/Http/Controllers/Api/V3/
V3 admin controllers  → app/Http/Controllers/Api/V3/Admin/
V3 resources          → app/Http/Resources/V3/
Services              → app/Services/{Domain}/
V3 routes             → routes/api_v3.php
V3 admin routes       → routes/api_v3_admin.php
V3 tests              → tests/Feature/Api/V3/
Middleware            → app/Http/Middleware/
Events                → app/Events/Commerce/
Config                → config/headless.php
```

### Rule 2: Response Envelope
EVERY V3 endpoint MUST return this exact JSON structure:
```json
{
  "success": true,
  "data": {},
  "meta": {
    "timestamp": "2026-05-03T16:30:00Z",
    "version": "3.0"
  },
  "errors": []
}
```
For paginated responses, `meta` includes:
```json
"pagination": {
  "current_page": 1,
  "last_page": 5,
  "per_page": 20,
  "total": 100
}
```

### Rule 3: Error Format
```json
{
  "success": false,
  "data": null,
  "meta": {"timestamp": "...", "version": "3.0"},
  "errors": [{
    "code": "VALIDATION_FAILED",
    "status": 422,
    "field": "email",
    "message": "The email field is required."
  }]
}
```
Allowed error codes: `VALIDATION_FAILED`, `UNAUTHORIZED`, `FORBIDDEN`,
`NOT_FOUND`, `RATE_LIMITED`, `SERVER_ERROR`, `PAYMENT_FAILED`

### Rule 4: Naming Conventions
- Controllers: PascalCase + `Controller` suffix
- Services: PascalCase + `Service` suffix
- Resources: PascalCase + `Resource` or `Collection` suffix
- Routes: snake_case URIs, plural nouns (`/products`, `/orders`)
- JSON fields: snake_case always (`unit_price`, `created_at`)
- Route names: `api.v3.{domain}.{action}`

### Rule 5: Controller Pattern
Every V3 controller method MUST follow this exact pattern:
```php
public function index(Request $request): JsonResponse
{
    // Step 1: Validate
    $validated = $request->validate([...]);
    // Step 2: Call service
    $result = $this->service->method($validated);
    // Step 3: Return resource
    return $this->paginatedResponse($result, ResourceClass::class);
}
```
- Controllers do NOT touch Eloquent directly
- Controllers do NOT contain business logic
- Max 1 service call per method

### Rule 6: Service Pattern
```php
class ProductCatalogService
{
    // Services are stateless — no request-dependent properties
    // Parameters are typed primitives/arrays, NOT Request objects
    // Return Eloquent models/collections, NOT JSON
    // Throw exceptions on failure, never return false/null for errors
    
    public function getBySlug(string $slug): Product
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        return $product;
    }
}
```

### Rule 7: Resource Pattern
```php
class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->getTranslation('name'),
            'slug' => $this->slug,
            // ... explicit field mapping, no $this->resource
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
```
Every resource MUST include: `id`, `created_at`, `updated_at`

### Rule 8: DO NOT MODIFY THESE FILES
```
routes/api.php                    — V2 mobile routes
routes/api_seller.php             — V2 seller routes  
app/Http/Controllers/Api/V2/*     — V2 controllers
app/Http/Resources/V2/*           — V2 resources
resources/views/**                — Blade templates
app/Http/Helpers.php              — Legacy helpers
Existing database table schemas   — Only ADD new tables/columns
```

### Rule 9: Authentication
- Public endpoints (browse products): NO auth required
- Cart, checkout, orders, profile: `auth:sanctum` required
- Admin endpoints: `auth:sanctum` + `admin` middleware required
- Token: Bearer token in `Authorization` header

### Rule 10: Testing
- Every controller method has ≥1 feature test
- Tests use `RefreshDatabase` trait
- Tests create own data via factories
- Test naming: `test_{action}_{scenario}`

---

## 4. ARCHITECTURE OVERVIEW

```
┌─────────────────────────────────────────────────┐
│                  Frontend Layer                  │
│  (Blade / Next.js / Nuxt / Flutter / React)     │
└──────────────────────┬──────────────────────────┘
                       │ HTTP (JSON)
┌──────────────────────▼──────────────────────────┐
│              API V3 Layer (NEW)                  │
│  routes/api_v3.php → V3 Controllers → Resources │
├─────────────────────────────────────────────────┤
│              Service Layer (NEW)                 │
│  Services/Catalog/ | Cart/ | Checkout/ | Order/  │
│  Services/Auth/    | Payment/ | Admin/ | Webhook │
├─────────────────────────────────────────────────┤
│              Model Layer (EXISTING)              │
│  175 Eloquent Models — DO NOT MODIFY             │
├─────────────────────────────────────────────────┤
│              Database (MySQL)                    │
│  Existing tables + webhook tables (NEW)          │
└─────────────────────────────────────────────────┘

PARALLEL (untouched):
┌─────────────────┐  ┌─────────────────┐
│ V2 Mobile API   │  │ Admin Blade UI  │
│ (routes/api.php)│  │ (views/backend) │
└─────────────────┘  └─────────────────┘
```

---

## 5. PHASE BREAKDOWN

### Phase 1: Foundation (Days 1-3)
Create infrastructure that all V3 endpoints use.

| Step | File to Create/Modify | Purpose |
|------|----------------------|---------|
| 1.1 | `config/headless.php` | CORS, rate limits, pagination config |
| 1.2 | `app/Http/Controllers/Api/V3/Controller.php` | Base controller with response helpers |
| 1.3 | `app/Exceptions/Api/V3/ApiExceptionHandler.php` | JSON error handler |
| 1.4 | `app/Http/Middleware/HeadlessCors.php` | CORS for any frontend origin |
| 1.5 | `RouteServiceProvider.php` (modify) | Add v3 rate limiters |
| 1.6 | `Kernel.php` (modify) | Add `api_v3` middleware group |
| 1.7 | `.env` (modify) | Add V3 config vars |
| 1.8 | `RouteServiceProvider.php` (modify) | Register V3 route files |

### Phase 2: Service Layer (Days 4-7)
Extract business logic from controllers into services.

| Step | File | Extracted From |
|------|------|---------------|
| 2.1 | `Services/Catalog/ProductCatalogService.php` | Api/V2/ProductController + SearchController |
| 2.2 | `Services/Catalog/CategoryService.php` | CategoryController |
| 2.3 | `Services/Catalog/BrandService.php` | BrandController |
| 2.4 | `Services/Cart/CartService.php` | Api/V2/CartController |
| 2.5 | `Services/Checkout/CheckoutService.php` | CheckoutController |
| 2.6 | `Services/Order/OrderQueryService.php` | OrderController |
| 2.7 | `Services/Auth/AuthService.php` | Api/V2/AuthController |
| 2.8 | `Services/Payment/PaymentOrchestrator.php` | 20+ payment controllers |

### Phase 3: Storefront API (Days 8-12)
Customer-facing endpoints for any frontend.

**Resources to create** (12 files in `app/Http/Resources/V3/`):
ProductResource, CategoryResource, BrandResource, CartItemResource,
OrderResource, OrderItemResource, UserResource, AddressResource,
ReviewResource, FlashDealResource, WishlistResource, SearchResultResource

**Controllers to create** (15 files in `app/Http/Controllers/Api/V3/`):
ProductController, CategoryController, BrandController, AuthController,
CartController, CheckoutController, OrderController, ProfileController,
AddressController, WishlistController, ReviewController, FlashDealController,
SettingsController, SliderController, SearchController

**Routes file**: `routes/api_v3.php`

### Phase 4: Admin API (Days 13-19)
Admin operations as JSON endpoints.

**Controllers** (11 files in `app/Http/Controllers/Api/V3/Admin/`):
ProductController, OrderController, CategoryController, BrandController,
CustomerController, SellerController, CouponController, FlashDealController,
SettingsController, DashboardController, UploadController

**Routes file**: `routes/api_v3_admin.php`

### Phase 5: Webhooks (Days 20-22)
Event-driven integrations.

| File | Purpose |
|------|---------|
| Migration: `create_webhook_endpoints_table` | Stores subscriptions |
| Migration: `create_webhook_logs_table` | Delivery audit log |
| `Models/WebhookEndpoint.php` | Endpoint model |
| `Models/WebhookLog.php` | Log model |
| `Events/Commerce/OrderPlaced.php` (+ 7 more) | Domain events |
| `Services/Webhook/WebhookDispatcher.php` | Dispatch + retry logic |
| `Controllers/Api/V3/Admin/WebhookController.php` | CRUD + test |

### Phase 6: Tests & Docs (Days 23-26)
| File | Purpose |
|------|---------|
| `database/factories/` | Factories for all tested models |
| `tests/Feature/Api/V3/*.php` | 1 test file per controller |
| `docs/api-v3.md` | Full API reference |

---

## 6. V3 ROUTE MAP

### Storefront (Public + Auth)
```
GET    /api/v3/products                     → ProductController@index
GET    /api/v3/products/{slug}              → ProductController@show
POST   /api/v3/products/{slug}/variant-price → ProductController@variantPrice
GET    /api/v3/categories                   → CategoryController@index
GET    /api/v3/categories/{slug}            → CategoryController@show
GET    /api/v3/brands                       → BrandController@index
GET    /api/v3/brands/{slug}                → BrandController@show
GET    /api/v3/flash-deals                  → FlashDealController@index
GET    /api/v3/flash-deals/{slug}           → FlashDealController@show
GET    /api/v3/search                       → SearchController@search
GET    /api/v3/search/suggestions           → SearchController@suggestions
GET    /api/v3/sliders                      → SliderController@index
GET    /api/v3/settings                     → SettingsController@index
GET    /api/v3/currencies                   → SettingsController@currencies
GET    /api/v3/languages                    → SettingsController@languages
GET    /api/v3/pages/{slug}                 → PageController@show

POST   /api/v3/auth/register               → AuthController@register
POST   /api/v3/auth/login                  → AuthController@login
POST   /api/v3/auth/social                 → AuthController@socialLogin
POST   /api/v3/auth/forgot-password        → AuthController@forgotPassword
POST   /api/v3/auth/reset-password         → AuthController@resetPassword
DELETE /api/v3/auth/logout                 → AuthController@logout         [auth]

GET    /api/v3/cart                         → CartController@index         [auth]
POST   /api/v3/cart/items                   → CartController@addItem       [auth]
PATCH  /api/v3/cart/items/{id}              → CartController@updateItem    [auth]
DELETE /api/v3/cart/items/{id}              → CartController@removeItem    [auth]
POST   /api/v3/cart/coupon                  → CartController@applyCoupon   [auth]
DELETE /api/v3/cart/coupon                  → CartController@removeCoupon  [auth]

POST   /api/v3/checkout/shipping            → CheckoutController@shipping  [auth]
POST   /api/v3/checkout/place-order         → CheckoutController@placeOrder [auth]

GET    /api/v3/orders                       → OrderController@index        [auth]
GET    /api/v3/orders/{code}                → OrderController@show         [auth]
POST   /api/v3/orders/{code}/cancel         → OrderController@cancel       [auth]

GET    /api/v3/user/profile                 → ProfileController@show       [auth]
PATCH  /api/v3/user/profile                 → ProfileController@update     [auth]
GET    /api/v3/user/addresses               → AddressController@index      [auth]
POST   /api/v3/user/addresses               → AddressController@store      [auth]
PATCH  /api/v3/user/addresses/{id}          → AddressController@update     [auth]
DELETE /api/v3/user/addresses/{id}          → AddressController@destroy    [auth]

GET    /api/v3/user/wishlist                → WishlistController@index     [auth]
POST   /api/v3/user/wishlist/{slug}         → WishlistController@add       [auth]
DELETE /api/v3/user/wishlist/{slug}         → WishlistController@remove    [auth]

GET    /api/v3/products/{slug}/reviews      → ReviewController@index
POST   /api/v3/products/{slug}/reviews      → ReviewController@store       [auth]
```

### Admin (All require [auth] + [admin])
```
GET/POST        /api/v3/admin/products
GET/PATCH/DEL   /api/v3/admin/products/{id}
POST            /api/v3/admin/products/{id}/duplicate
PATCH           /api/v3/admin/products/{id}/status

GET             /api/v3/admin/orders
GET             /api/v3/admin/orders/{id}
PATCH           /api/v3/admin/orders/{id}/delivery-status
PATCH           /api/v3/admin/orders/{id}/payment-status

GET/POST        /api/v3/admin/categories
GET/PATCH/DEL   /api/v3/admin/categories/{id}

GET/POST        /api/v3/admin/brands
GET/PATCH/DEL   /api/v3/admin/brands/{id}

GET             /api/v3/admin/customers
GET/PATCH       /api/v3/admin/customers/{id}
POST            /api/v3/admin/customers/{id}/ban

GET/POST        /api/v3/admin/coupons
GET/PATCH/DEL   /api/v3/admin/coupons/{id}

GET/PATCH       /api/v3/admin/settings

GET             /api/v3/admin/dashboard/stats

POST            /api/v3/admin/uploads

GET/POST        /api/v3/admin/webhooks
DEL             /api/v3/admin/webhooks/{id}
POST            /api/v3/admin/webhooks/{id}/test
```

---

## 7. EXECUTION CHECKLIST

Use this to track progress. Mark `[x]` when done.

### Phase 1: Foundation
- [x] 1.1 Create `config/headless.php`
- [x] 1.2 Create `app/Http/Controllers/Api/V3/Controller.php`
- [x] 1.3 Create `app/Exceptions/Api/V3/ApiExceptionHandler.php`
- [x] 1.4 Create `app/Http/Middleware/HeadlessCors.php`
- [x] 1.5 Add rate limiters to `RouteServiceProvider`
- [x] 1.6 Add `api_v3` middleware group to `Kernel.php`
- [x] 1.7 Add V3 env vars to `.env`
- [x] 1.8 Register V3 route mapping in `RouteServiceProvider`
- [x] 1.9 Create empty `routes/api_v3.php`
- [x] 1.10 Create empty `routes/api_v3_admin.php`

### Phase 2: Service Layer
- [x] 2.1 `app/Services/Catalog/ProductCatalogService.php`
- [x] 2.2 `app/Services/Catalog/CategoryService.php`
- [x] 2.3 `app/Services/Catalog/BrandService.php`
- [x] 2.4 `app/Services/Cart/CartService.php`
- [x] 2.5 `app/Services/Checkout/CheckoutService.php`
- [x] 2.6 `app/Services/Order/OrderQueryService.php`
- [x] 2.7 `app/Services/Auth/AuthService.php`
- [x] 2.8 `app/Services/Payment/PaymentOrchestrator.php`
- [x] 2.9 `app/Contracts/PaymentGatewayAdapter.php`

### Phase 3: Storefront API
- [x] 3.1 Create 12 Resource files in `app/Http/Resources/V3/`
- [x] 3.2 Create 13 Controller files in `app/Http/Controllers/Api/V3/`
- [x] 3.3 Define all storefront routes in `routes/api_v3.php`
- [x] 3.4 Smoke-test all public endpoints

### Phase 4: Admin API
- [x] 4.1 Create 3 admin services in `app/Services/Admin/`
- [x] 4.2 Create 4 admin controllers in `app/Http/Controllers/Api/V3/Admin/`
- [x] 4.3 Define all admin routes in `routes/api_v3_admin.php`
- [ ] 4.4 Smoke-test admin endpoints

### Phase 5: Webhooks
- [x] 5.1 Create 2 database migrations
- [x] 5.2 Create WebhookEndpoint + WebhookLog models
- [x] 5.3 Create 6 event classes in `app/Events/Commerce/`
- [x] 5.4 Create `app/Services/Webhook/WebhookDispatcher.php`
- [x] 5.5 Create `app/Http/Controllers/Api/V3/Admin/WebhookController.php`

### Phase 6: Tests & Docs
- [ ] 6.1 Create model factories
- [ ] 6.2 Create feature tests (1 per controller)
- [ ] 6.3 Create `docs/api-v3.md` reference

---

## 8. HOW TO USE THIS DOCUMENT

**For AI Agents**: Read this entire file before writing any V3 code.
Follow Rules 1-10 exactly. Use the checklist to know what's done vs pending.
Check existing code in `app/Http/Controllers/Api/V2/` for reference logic
to extract into services, but NEVER modify V2 files.

**For Developers**: This is the single source of truth for the headless
engine architecture. All V3 pull requests must conform to these rules.
