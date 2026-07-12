<?php
/**
 * License domain logic: create, verify, activate, and manage purchase codes.
 */
class Licenses
{
    private PDO $db;
    private array $config;

    /** Plans, low rank -> high. A license carries one plan key. */
    public const PLANS = [
        'basic'        => ['name' => 'Basic',        'rank' => 1],
        'business'     => ['name' => 'Business',     'rank' => 2],
        'professional' => ['name' => 'Professional', 'rank' => 3],
        'enterprise'   => ['name' => 'Enterprise',   'rank' => 4],
    ];

    /**
     * Full feature catalog. Every gateable capability in the shop.
     *   min_tier  : minimum plan rank at which the feature is FULLY licensed
     *   essential : true => when below min_tier it runs a locked "basic" fallback
     *               instead of being switched off
     *   fallback  : short description of the locked basic behavior
     *   group     : UI grouping
     * key => [label, group, min_tier, essential, fallback]
     */
    public const FEATURE_CATALOG = [
        // --- Core (always on, even Basic) ---
        'order_system'        => ['Order System',        'Core', 1, false, null],
        'product_catalog'     => ['Product Catalog',     'Core', 1, false, null],
        'cart_checkout'       => ['Cart & Checkout',     'Core', 1, false, null],
        'category_browsing'   => ['Categories & Search', 'Core', 1, false, null],

        // --- Essentials with a locked Basic fallback ---
        'payment_gateways'    => ['Payment Gateways',    'Essentials', 2, true, 'COD / offline only'],
        'shipping_methods'    => ['Shipping Methods',    'Essentials', 2, true, 'Flat / free only'],
        'product_variations'  => ['Product Variations',  'Essentials', 2, true, 'Simple products only'],
        'seo_tools'           => ['SEO Tools',           'Essentials', 2, true, 'Auto meta only'],

        // --- Business ---
        'wishlist'            => ['Wishlist',            'Storefront', 2, false, null],
        'compare'             => ['Compare',             'Storefront', 2, false, null],
        'coupons'             => ['Coupons',             'Marketing',  2, false, null],
        'flash_deals'         => ['Flash Deals',         'Marketing',  2, false, null],
        'product_reviews'     => ['Product Reviews',     'Storefront', 2, false, null],
        'product_query'       => ['Product Q&A',         'Storefront', 2, false, null],
        'color_filter'        => ['Color Filter',        'Storefront', 2, false, null],
        'newsletter'          => ['Newsletter',          'Marketing',  2, false, null],
        'last_viewed'         => ['Last Viewed',         'Storefront', 2, false, null],
        'guest_checkout'      => ['Guest Checkout',      'Storefront', 2, false, null],

        // --- Professional ---
        'gst_system'          => ['GST System',          'Tax',        3, false, null],
        'wallet'              => ['Wallet',              'Payments',   3, false, null],
        'club_point'          => ['Club Point',          'Marketing',  3, false, null],
        'refund_request'      => ['Refund System',       'Support',    3, false, null],
        'conversation'        => ['Conversations',       'Support',    3, false, null],
        'ai_studio'           => ['AI Studio',           'AI',         3, false, null],
        'otp_system'          => ['OTP Login',           'Support',    3, false, null],
        'classified_products' => ['Classified Products', 'Selling',    3, false, null],
        'preorder'            => ['Preorder',            'Selling',    3, false, null],
        'affiliate_system'    => ['Affiliate System',    'Marketing',  3, false, null],
        'delivery_boy'        => ['Delivery Boy',        'Logistics',  3, false, null],

        // --- Enterprise ---
        'pos_system'          => ['Point of Sale (POS)', 'Selling',    4, false, null],
        'auction'             => ['Auction',             'Selling',    4, false, null],
        'wholesale'           => ['Wholesale',           'Selling',    4, false, null],
        'seller_subscription' => ['Seller Subscription', 'Multivendor',4, false, null],
        'multivendor'         => ['Multivendor System',  'Multivendor',4, false, null],
        'dispute_refund'      => ['Dispute Refund',      'Support',    4, false, null],
        'cybersource'         => ['Cybersource Gateway', 'Payments',   4, false, null],
    ];

    /** Legacy module list kept for the /api/modules back-compat endpoint. */
    public const MODULE_CATALOG = [
        'pos_system'          => 'Point of Sale (POS)',
        'affiliate_system'    => 'Affiliate System',
        'auction'             => 'Auction',
        'club_point'          => 'Club Point',
        'seller_subscription' => 'Seller Subscription',
        'refund_request'      => 'Refund System',
        'gst_system'          => 'GST System',
        'delivery_boy'        => 'Delivery Boy',
        'wholesale'           => 'Wholesale',
        'otp_system'          => 'OTP System',
        'cybersource'         => 'Cybersource',
    ];

    public static function planRank(?string $plan): int
    {
        return self::PLANS[$plan ?? '']['rank'] ?? 4; // unknown/legacy -> enterprise
    }

    /** Default state for a feature under a plan, ignoring overrides. */
    public static function defaultState(string $plan, string $featureKey): string
    {
        $f = self::FEATURE_CATALOG[$featureKey] ?? null;
        if (!$f) return 'full';
        [, , $minTier, $essential] = $f;
        if (self::planRank($plan) >= $minTier) return 'full';
        return $essential ? 'fallback' : 'off';
    }

    public function __construct(PDO $db, array $config)
    {
        $this->db = $db;
        $this->config = $config;
    }

    // ----- module entitlements -----

    /** Raw enabled-state map for a license: [module => bool]. Missing row = enabled. */
    public function entitlements(int $licenseId): array
    {
        $stmt = $this->db->prepare('SELECT module, enabled FROM entitlements WHERE license_id = ?');
        $stmt->execute([$licenseId]);
        $set = [];
        foreach ($stmt->fetchAll() as $r) {
            $set[$r['module']] = (int) $r['enabled'] === 1;
        }
        $out = [];
        foreach (array_keys(self::MODULE_CATALOG) as $mod) {
            $out[$mod] = array_key_exists($mod, $set) ? $set[$mod] : true;
        }
        // include any custom modules stored but not in the catalog
        foreach ($set as $mod => $on) {
            if (!array_key_exists($mod, $out)) $out[$mod] = $on;
        }
        return $out;
    }

    /** Seed all catalog modules as enabled for a new license. */
    public function seedEntitlements(int $licenseId): void
    {
        $stmt = $this->db->prepare('INSERT OR IGNORE INTO entitlements (license_id, module, enabled, updated_at) VALUES (?, ?, 1, ?)');
        // "INSERT OR IGNORE" is SQLite; emulate for MySQL below.
        $isMysql = ($this->config['db_driver'] ?? 'sqlite') === 'mysql';
        foreach (array_keys(self::MODULE_CATALOG) as $mod) {
            try {
                if ($isMysql) {
                    $s = $this->db->prepare('INSERT IGNORE INTO entitlements (license_id, module, enabled, updated_at) VALUES (?, ?, 1, ?)');
                    $s->execute([$licenseId, $mod, gmdate('c')]);
                } else {
                    $stmt->execute([$licenseId, $mod, gmdate('c')]);
                }
            } catch (Throwable $e) {}
        }
    }

    public function setModule(string $code, string $module, bool $enabled): bool
    {
        $lic = $this->find($code);
        if (!$lic) return false;
        $now = gmdate('c');
        // upsert
        $exists = $this->db->prepare('SELECT id FROM entitlements WHERE license_id = ? AND module = ?');
        $exists->execute([$lic['id'], $module]);
        if ($exists->fetch()) {
            $s = $this->db->prepare('UPDATE entitlements SET enabled = ?, updated_at = ? WHERE license_id = ? AND module = ?');
            return $s->execute([$enabled ? 1 : 0, $now, $lic['id'], $module]);
        }
        $s = $this->db->prepare('INSERT INTO entitlements (license_id, module, enabled, updated_at) VALUES (?, ?, ?, ?)');
        return $s->execute([$lic['id'], $module, $enabled ? 1 : 0, $now]);
    }

    /**
     * Resolve module entitlements for a domain: find the active item license bound
     * to it and return its enabled modules.
     * @return array{managed:bool, modules:array<string,bool>, code:?string}
     */
    public function modulesForDomain(string $domain): array
    {
        $norm = self::normalizeDomain($domain);
        $sql = "SELECT l.* FROM licenses l
                JOIN activations a ON a.license_id = l.id
                WHERE l.status = 'active' AND l.type = 'item' AND a.domain = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$norm]);
        $lic = $stmt->fetch();
        if (!$lic) {
            return ['managed' => false, 'modules' => [], 'code' => null];
        }
        return ['managed' => true, 'modules' => $this->entitlements((int) $lic['id']), 'code' => $lic['code']];
    }

    // ----- plans / features -----

    public function setLicensePlan(string $code, string $plan): bool
    {
        if (!array_key_exists($plan, self::PLANS)) return false;
        $s = $this->db->prepare('UPDATE licenses SET plan = ?, updated_at = ? WHERE code = ?');
        return $s->execute([$plan, gmdate('c'), trim($code)]);
    }

    /** plan_features overrides for a plan: [feature => bool]. */
    public function planOverrides(string $plan): array
    {
        $s = $this->db->prepare('SELECT feature, enabled FROM plan_features WHERE plan = ?');
        $s->execute([$plan]);
        $out = [];
        foreach ($s->fetchAll() as $r) $out[$r['feature']] = (int) $r['enabled'] === 1;
        return $out;
    }

    public function setPlanFeature(string $plan, string $feature, bool $enabled): bool
    {
        if (!array_key_exists($plan, self::PLANS)) return false;
        $now = gmdate('c');
        $ex = $this->db->prepare('SELECT id FROM plan_features WHERE plan = ? AND feature = ?');
        $ex->execute([$plan, $feature]);
        if ($ex->fetch()) {
            $s = $this->db->prepare('UPDATE plan_features SET enabled = ?, updated_at = ? WHERE plan = ? AND feature = ?');
            return $s->execute([$enabled ? 1 : 0, $now, $plan, $feature]);
        }
        $s = $this->db->prepare('INSERT INTO plan_features (plan, feature, enabled, updated_at) VALUES (?, ?, ?, ?)');
        return $s->execute([$plan, $feature, $enabled ? 1 : 0, $now]);
    }

    /** Effective state of one feature for a plan (override beats catalog default). */
    public function planFeatureState(string $plan, string $feature, ?array $overrides = null): string
    {
        $overrides = $overrides ?? $this->planOverrides($plan);
        $f = self::FEATURE_CATALOG[$feature] ?? null;
        if (array_key_exists($feature, $overrides)) {
            if ($overrides[$feature]) return 'full';
            return ($f && $f[3]) ? 'fallback' : 'off';
        }
        return self::defaultState($plan, $feature);
    }

    /** The full plan x feature matrix for the admin editor. */
    public function planMatrix(): array
    {
        $matrix = [];
        foreach (array_keys(self::PLANS) as $plan) {
            $ov = $this->planOverrides($plan);
            foreach (array_keys(self::FEATURE_CATALOG) as $feat) {
                $matrix[$plan][$feat] = $this->planFeatureState($plan, $feat, $ov);
            }
        }
        return $matrix;
    }

    /**
     * Resolve effective feature states for a domain.
     * @return array{managed:bool, plan:?string, plan_name:?string, features:array<string,string>}
     */
    public function featuresForDomain(string $domain): array
    {
        $norm = self::normalizeDomain($domain);
        $sql = "SELECT l.* FROM licenses l
                JOIN activations a ON a.license_id = l.id
                WHERE l.status = 'active' AND l.type = 'item' AND a.domain = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$norm]);
        $lic = $stmt->fetch();
        if (!$lic) {
            return ['managed' => false, 'plan' => null, 'plan_name' => null, 'features' => []];
        }

        $plan = $lic['plan'] ?: 'enterprise';
        $overrides = $this->planOverrides($plan);
        $licOverrides = $this->entitlements((int) $lic['id']); // per-license chip toggles

        $features = [];
        foreach (array_keys(self::FEATURE_CATALOG) as $feat) {
            $state = $this->planFeatureState($plan, $feat, $overrides);
            // a per-license disabled chip downgrades the feature
            if (array_key_exists($feat, $licOverrides) && $licOverrides[$feat] === false && $state === 'full') {
                $f = self::FEATURE_CATALOG[$feat];
                $state = $f[3] ? 'fallback' : 'off';
            }
            $features[$feat] = $state;
        }

        return [
            'managed' => true,
            'plan' => $plan,
            'plan_name' => self::PLANS[$plan]['name'] ?? ucfirst($plan),
            'features' => $features,
        ];
    }

    /** Normalize a domain the same way the client does (strip scheme/www, keep registrable pair). */
    public static function normalizeDomain(?string $domain): string
    {
        $domain = strtolower(trim((string) $domain));
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('#^www\.#', '', $domain);
        $domain = explode('/', $domain)[0];      // drop any path
        $domain = explode(':', $domain)[0];      // drop any port
        $parts = explode('.', $domain);
        $count = count($parts);
        if ($count > 2) {
            $domain = $parts[$count - 2] . '.' . $parts[$count - 1];
        }
        return $domain;
    }

    public function generateCode(): string
    {
        $prefix = $this->config['code_prefix'] ?? 'RS';
        do {
            $blocks = [];
            for ($i = 0; $i < 4; $i++) {
                $blocks[] = strtoupper(bin2hex(random_bytes(2)));
            }
            $code = $prefix . '-' . implode('-', $blocks);
            $exists = $this->find($code) !== null;
        } while ($exists);
        return $code;
    }

    public function find(string $code): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM licenses WHERE code = ? LIMIT 1');
        $stmt->execute([trim($code)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(int $limit = 500): array
    {
        $stmt = $this->db->query('SELECT * FROM licenses ORDER BY id DESC LIMIT ' . (int) $limit);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['domains'] = $this->domains($r['id']);
            $r['modules'] = $this->entitlements((int) $r['id']);
        }
        return $rows;
    }

    public function domains(int $licenseId): array
    {
        $stmt = $this->db->prepare('SELECT domain FROM activations WHERE license_id = ? ORDER BY id ASC');
        $stmt->execute([$licenseId]);
        return array_map(fn($r) => $r['domain'], $stmt->fetchAll());
    }

    public function create(array $data): array
    {
        $code = $this->generateCode();
        $plan = array_key_exists(($data['plan'] ?? ''), self::PLANS) ? $data['plan'] : 'basic';
        $stmt = $this->db->prepare('INSERT INTO licenses
            (code, type, addon_identifier, buyer_name, buyer_email, status, max_domains, note, plan, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $code,
            in_array(($data['type'] ?? 'item'), ['item', 'addon'], true) ? $data['type'] : 'item',
            $data['addon_identifier'] ?? null,
            $data['buyer_name'] ?? null,
            $data['buyer_email'] ?? null,
            'active',
            (int) ($data['max_domains'] ?? $this->config['default_max_domains'] ?? 1),
            $data['note'] ?? null,
            $plan,
            gmdate('c'),
        ]);
        $lic = $this->find($code);
        // new licenses start with every module unlocked; admin toggles off what
        // wasn't purchased.
        if ($lic) {
            $this->seedEntitlements((int) $lic['id']);
        }
        return $lic;
    }

    public function revoke(string $code): bool
    {
        $stmt = $this->db->prepare('UPDATE licenses SET status = ?, updated_at = ? WHERE code = ?');
        return $stmt->execute(['revoked', gmdate('c'), trim($code)]) && $stmt->rowCount() >= 0;
    }

    public function activateStatus(string $code): bool
    {
        $stmt = $this->db->prepare('UPDATE licenses SET status = ?, updated_at = ? WHERE code = ?');
        return $stmt->execute(['active', gmdate('c'), trim($code)]);
    }

    public function resetDomains(string $code): bool
    {
        $lic = $this->find($code);
        if (!$lic) return false;
        $stmt = $this->db->prepare('DELETE FROM activations WHERE license_id = ?');
        return $stmt->execute([$lic['id']]);
    }

    /**
     * Verify a code (optionally for a domain / addon). Does NOT bind.
     * @return array{valid:bool, activated:bool, domains:array, message:string, license:?array}
     */
    public function verify(string $code, ?string $domain = null, ?string $addonIdentifier = null): array
    {
        $lic = $this->find($code);
        if (!$lic) {
            return ['valid' => false, 'activated' => false, 'domains' => [], 'message' => 'Invalid purchase code', 'license' => null];
        }
        if ($lic['status'] !== 'active') {
            return ['valid' => false, 'activated' => false, 'domains' => [], 'message' => 'License is ' . $lic['status'], 'license' => $lic];
        }
        if ($addonIdentifier !== null && $lic['type'] === 'addon'
            && !empty($lic['addon_identifier'])
            && strcasecmp($lic['addon_identifier'], $addonIdentifier) !== 0) {
            return ['valid' => false, 'activated' => false, 'domains' => [], 'message' => 'Code does not match this addon', 'license' => $lic];
        }

        $domains = $this->domains($lic['id']);
        $activated = count($domains) > 0;

        if ($domain !== null) {
            $norm = self::normalizeDomain($domain);
            $bound = in_array($norm, array_map([self::class, 'normalizeDomain'], $domains), true);
            return [
                'valid' => true,
                'activated' => $bound,
                'domains' => $domains,
                'message' => $bound ? 'Active on this domain' : 'Not yet activated on this domain',
                'license' => $lic,
            ];
        }

        return ['valid' => true, 'activated' => $activated, 'domains' => $domains, 'message' => 'Valid code', 'license' => $lic];
    }

    /**
     * Bind a code to a domain (activation). Idempotent per domain, capped at max_domains.
     */
    public function activate(string $code, string $domain, ?string $addonIdentifier = null, ?string $ip = null): array
    {
        $lic = $this->find($code);
        if (!$lic) {
            return ['success' => false, 'message' => 'Invalid purchase code'];
        }
        if ($lic['status'] !== 'active') {
            return ['success' => false, 'message' => 'License is ' . $lic['status']];
        }
        if ($addonIdentifier !== null && $lic['type'] === 'addon'
            && !empty($lic['addon_identifier'])
            && strcasecmp($lic['addon_identifier'], $addonIdentifier) !== 0) {
            return ['success' => false, 'message' => 'Code does not match this addon'];
        }

        $norm = self::normalizeDomain($domain);
        if ($norm === '') {
            return ['success' => false, 'message' => 'Missing domain'];
        }

        $existing = $this->domains($lic['id']);
        $existingNorm = array_map([self::class, 'normalizeDomain'], $existing);
        if (in_array($norm, $existingNorm, true)) {
            return ['success' => true, 'message' => 'Already activated on this domain', 'domain' => $norm];
        }
        if (count($existing) >= (int) $lic['max_domains']) {
            return ['success' => false, 'message' => 'Activation limit reached for this code'];
        }

        $stmt = $this->db->prepare('INSERT INTO activations (license_id, domain, ip, created_at) VALUES (?, ?, ?, ?)');
        $stmt->execute([$lic['id'], $norm, $ip, gmdate('c')]);

        return ['success' => true, 'message' => 'Activated', 'domain' => $norm];
    }
}
