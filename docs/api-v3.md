# Animazon Shopping V3 Headless API Reference

## Architecture Overview

The V3 Headless Engine converts the Animazon Shopping monolith into a strict API-first backend. It decouples business logic from view presentation, ensuring the platform can serve any frontend stack (Next.js, Vue, Flutter, iOS/Android) seamlessly.

### Core Principles Used:
1. **Strict JSON Envelopes**: All responses, including errors and 404s, use a unified JSON structure (`success`, `data`, `meta`, `errors`).
2. **Stateless Services**: Heavy business logic (cart rules, price calculations, order placement) was extracted from Controllers into `app/Services/`.
3. **Resource Transformers**: Eloquent models are converted into consistent `snake_case` payloads via `app/Http/Resources/V3/` preventing database leakages.
4. **Adapter Pattern for Payments**: 20+ legacy gateways are unified behind the `PaymentOrchestrator` and `PaymentGatewayAdapter` interface.

---

## Endpoint Definitions

**Base URL**: `/api/v3`

### 1. Storefront API (Public)

*   `GET /health` - API status and versioning.
*   `GET /products` - Search, filter, and paginate catalog.
*   `GET /products/{slug}` - Detailed product info.
*   `POST /products/{slug}/variant-price` - Calculate dynamic pricing for variations.
*   `GET /categories` - Hierarchical category tree.
*   `GET /categories/{slug}` - Category info + products within.
*   `GET /brands` - Brand list.
*   `GET /settings` - Public store config (currency, min order, language flags).

### 2. Storefront API (Customer Auth Required)

*Auth*: Bearer token (Sanctum)

*   `GET /cart` - Retrieve current cart & total summaries.
*   `POST /cart/items` - Add item/variation to cart.
*   `PATCH /cart/items/{id}` - Adjust quantities.
*   `DELETE /cart/items/{id}` - Remove item.
*   `GET /orders` - Customer order history.
*   `GET /orders/{code}` - Order details.
*   `GET /user/profile` - Customer profile.
*   `GET /user/addresses` - Saved shipping addresses.

### 3. Admin API (Admin Auth Required)

**Base URL**: `/api/v3/admin`

*   `GET /products` - Admin product listing.
*   `POST /products` - Create new products (fires `ProductCreated` webhook).
*   `PATCH /orders/{id}/delivery-status` - Update delivery state (fires `OrderUpdated` webhook).
*   `GET /customers` - List all customers.
*   `PATCH /customers/{id}/ban` - Ban/unban users.

### 4. Webhooks (Event Driven)

**Base URL**: `/api/v3/admin/webhooks`

The headless engine emits real-time HTTP callbacks to integrated systems (like a custom fulfillment provider or CRM) when events happen.

**Subscribable Events:**
*   `App\Events\Commerce\ProductCreated`
*   `App\Events\Commerce\ProductUpdated`
*   `App\Events\Commerce\ProductDeleted`
*   `App\Events\Commerce\OrderCreated`
*   `App\Events\Commerce\OrderUpdated`
*   `App\Events\Commerce\CustomerCreated`

Endpoints are managed via the API. The engine cryptographically signs payloads (`X-Commerce-Signature`) using HMAC SHA-256 and the endpoint's configured secret to ensure security.
