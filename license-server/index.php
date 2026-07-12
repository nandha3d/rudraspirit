<?php
/**
 * Animazon License Server — single front controller.
 *
 * Routes:
 *   Public JSON API (called by client sites):
 *     POST /api/verify      {purchase_code, domain, item_type?, addon_identifier?}
 *     POST /api/activate    {purchase_code, domain, item_type?, addon_identifier?}
 *
 *   Back-compat (same shape the ecommerce client already speaks):
 *     POST /activation/verify-purchase-code/{code}
 *     GET  /item_info/{code}
 *     GET  /registered-addon-info/{code}
 *     GET  /registered-addon-list/{code}
 *
 *   Admin (token via /admin login form, or X-Admin-Token header):
 *     GET  /admin
 *     POST /admin/login, /admin/logout
 *     GET  /admin/api/licenses
 *     POST /admin/api/licenses            (create)
 *     POST /admin/api/licenses/revoke     {code}
 *     POST /admin/api/licenses/activate   {code}
 *     POST /admin/api/licenses/reset      {code}
 */

declare(strict_types=1);
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
session_start();

$config = require __DIR__ . '/config.php';
require __DIR__ . '/src/Db.php';
require __DIR__ . '/src/Licenses.php';

$pdo = Db::conn($config);
$licenses = new Licenses($pdo, $config);

// ---- request helpers ----
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
$uri = '/' . trim(rawurldecode($uri), '/');
if ($uri === '/') $uri = '/';

function body_json(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '', true);
    if (is_array($data)) return $data;
    return $_POST ?: [];
}
function json_out($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($data, JSON_UNESCAPED_SLASHES);
    exit;
}
function client_ip(): ?string {
    return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
}
function seg_after(string $uri, string $prefix): ?string {
    if (strpos($uri, $prefix) === 0) {
        return trim(substr($uri, strlen($prefix)), '/');
    }
    return null;
}
function admin_ok(array $config): bool {
    $hdr = $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? '';
    if ($hdr !== '' && hash_equals((string) $config['admin_token'], $hdr)) return true;
    return !empty($_SESSION['license_admin']);
}

// preflight
if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, X-Admin-Token');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    exit;
}

// =======================================================================
// PUBLIC JSON API
// =======================================================================
if ($uri === '/api/verify' && $method === 'POST') {
    $b = body_json();
    $r = $licenses->verify($b['purchase_code'] ?? '', $b['domain'] ?? null, $b['addon_identifier'] ?? null);
    json_out([
        'valid' => $r['valid'],
        'activated' => $r['activated'],
        'bound_domains' => $r['domains'],
        'message' => $r['message'],
    ]);
}

if ($uri === '/api/activate' && $method === 'POST') {
    $b = body_json();
    $r = $licenses->activate($b['purchase_code'] ?? '', $b['domain'] ?? '', $b['addon_identifier'] ?? null, client_ip());
    json_out($r, $r['success'] ? 200 : 422);
}

// effective feature states for a domain (plan-based). Primary endpoint the shop uses.
if ($uri === '/api/features' && $method === 'POST') {
    $b = body_json();
    $r = $licenses->featuresForDomain($b['domain'] ?? '');
    json_out([
        'managed'   => $r['managed'],    // false => fail open on the client
        'plan'      => $r['plan'],
        'plan_name' => $r['plan_name'],
        'domain'    => Licenses::normalizeDomain($b['domain'] ?? ''),
        'features'  => (object) $r['features'],  // key => 'full'|'fallback'|'off'
    ]);
}

// module entitlements for a domain (the shop calls this, cached client-side)
if ($uri === '/api/modules' && $method === 'POST') {
    $b = body_json();
    $r = $licenses->modulesForDomain($b['domain'] ?? '');
    json_out([
        'managed' => $r['managed'],   // false => domain has no active item license (shop should fail-open)
        'domain'  => Licenses::normalizeDomain($b['domain'] ?? ''),
        'modules' => (object) $r['modules'],
    ]);
}

// single-module check for a domain
if ($uri === '/api/module-check' && $method === 'POST') {
    $b = body_json();
    $module = $b['module'] ?? '';
    $r = $licenses->modulesForDomain($b['domain'] ?? '');
    $enabled = !$r['managed'] ? true : (bool) ($r['modules'][$module] ?? true);
    json_out(['managed' => $r['managed'], 'module' => $module, 'enabled' => $enabled]);
}

// =======================================================================
// BACK-COMPAT ENDPOINTS (activeitzone-shaped) so existing clients work
// =======================================================================
if (($code = seg_after($uri, '/activation/verify-purchase-code/')) !== null) {
    // client sends the code in the path (and body). Valid + active => truthy.
    $r = $licenses->verify($code);
    json_out(['result' => $r['valid'], 'verified' => $r['valid'], 'message' => $r['message']]);
}
if (($code = seg_after($uri, '/item_info/')) !== null) {
    $r = $licenses->verify($code, null, null);
    // truthy body only when a valid item license; EMPTY body otherwise so the
    // client's `$res ? true : false` check reads invalid as falsy.
    if ($r['valid']) {
        json_out(['item' => true, 'buyer' => $r['license']['buyer_name'] ?? null, 'domains' => $r['domains']]);
    }
    http_response_code(404);
    exit;
}
if (($code = seg_after($uri, '/registered-addon-info/')) !== null) {
    $r = $licenses->verify($code);
    if ($r['valid']) {
        json_out(['addon' => true, 'identifier' => $r['license']['addon_identifier'] ?? null]);
    }
    http_response_code(404);
    exit;
}
if (($code = seg_after($uri, '/registered-addon-list/')) !== null) {
    // client reads element [0] as the bound domain
    $r = $licenses->verify($code);
    json_out($r['valid'] ? array_values($r['domains']) : []);
}

// =======================================================================
// ADMIN
// =======================================================================
if ($uri === '/admin/login' && $method === 'POST') {
    $token = $_POST['token'] ?? '';
    if (hash_equals((string) $config['admin_token'], (string) $token)) {
        $_SESSION['license_admin'] = true;
        header('Location: /admin');
        exit;
    }
    $_SESSION['login_error'] = 'Invalid token';
    header('Location: /admin');
    exit;
}
if ($uri === '/admin/logout') {
    unset($_SESSION['license_admin']);
    header('Location: /admin');
    exit;
}

if (strpos($uri, '/admin/api/') === 0) {
    if (!admin_ok($config)) json_out(['error' => 'unauthorized'], 401);

    if ($uri === '/admin/api/licenses' && $method === 'GET') {
        json_out(['licenses' => $licenses->all()]);
    }
    if ($uri === '/admin/api/licenses' && $method === 'POST') {
        $b = body_json();
        $lic = $licenses->create($b);
        json_out(['created' => true, 'license' => $lic]);
    }
    if ($uri === '/admin/api/licenses/revoke' && $method === 'POST') {
        $licenses->revoke(body_json()['code'] ?? '');
        json_out(['ok' => true]);
    }
    if ($uri === '/admin/api/licenses/activate' && $method === 'POST') {
        $licenses->activateStatus(body_json()['code'] ?? '');
        json_out(['ok' => true]);
    }
    if ($uri === '/admin/api/licenses/reset' && $method === 'POST') {
        $licenses->resetDomains(body_json()['code'] ?? '');
        json_out(['ok' => true]);
    }
    if ($uri === '/admin/api/entitlements/toggle' && $method === 'POST') {
        $b = body_json();
        $ok = $licenses->setModule($b['code'] ?? '', $b['module'] ?? '', (bool) ($b['enabled'] ?? false));
        json_out(['ok' => $ok]);
    }
    if ($uri === '/admin/api/licenses/plan' && $method === 'POST') {
        $b = body_json();
        $ok = $licenses->setLicensePlan($b['code'] ?? '', $b['plan'] ?? '');
        json_out(['ok' => $ok]);
    }
    if ($uri === '/admin/api/plan-matrix' && $method === 'GET') {
        json_out([
            'plans'   => Licenses::PLANS,
            'catalog' => Licenses::FEATURE_CATALOG,
            'matrix'  => $licenses->planMatrix(),
        ]);
    }
    if ($uri === '/admin/api/plan-feature' && $method === 'POST') {
        $b = body_json();
        $ok = $licenses->setPlanFeature($b['plan'] ?? '', $b['feature'] ?? '', (bool) ($b['enabled'] ?? false));
        json_out(['ok' => $ok]);
    }
    json_out(['error' => 'not found'], 404);
}

if ($uri === '/admin') {
    if (!admin_ok($config)) {
        require __DIR__ . '/views/login.php';
        exit;
    }
    $rows = $licenses->all();
    $planMatrix = $licenses->planMatrix();
    require __DIR__ . '/views/admin.php';
    exit;
}

// =======================================================================
// ROOT / health
// =======================================================================
if ($uri === '/' || $uri === '/health') {
    json_out(['service' => $config['brand'], 'status' => 'ok', 'time' => gmdate('c')]);
}

json_out(['error' => 'Not found', 'path' => $uri], 404);
