<?php

namespace App\Services;

use App\Models\License;
use App\Models\LicenseActivation;
use Illuminate\Support\Carbon;

/**
 * Core licensing domain logic.
 *
 * Given a key + domain, decides whether a deployment is licensed, enforcing:
 *   - license existence and product match
 *   - status (active / suspended / revoked)
 *   - expiry
 *   - domain-locking with an activation limit (auto-activates new domains
 *     while under the limit; rejects once the limit is reached)
 *   - per-addon entitlements (with their own optional expiry)
 *
 * IMPORTANT: this class only answers "is this deployment licensed and what is
 * it entitled to". It never authenticates a user and never grants access to
 * anything on the client — the client decides what to do with the answer.
 */
class LicenseVerifier
{
    /**
     * Verify a key against a domain. Auto-activates the domain when allowed.
     *
     * @return array The response payload (before signing).
     */
    public function verify(string $key, string $domain, ?string $product = null, ?string $ip = null): array
    {
        $domain = self::normalizeDomain($domain);

        $query = License::where('license_key', trim($key));
        if ($product) {
            $query->where('product', $product);
        }
        $license = $query->first();

        if (! $license) {
            return $this->fail('not_found', 'No license matches this key.');
        }

        if ($license->status === 'revoked') {
            return $this->fail('revoked', 'This license has been revoked.');
        }

        if ($license->status === 'suspended') {
            return $this->fail('suspended', 'This license is suspended.');
        }

        if ($license->isExpired()) {
            return $this->fail('expired', 'This license expired on '
                . $license->expires_at->toDateString() . '.', [
                    'expires_at' => $license->expires_at->toIso8601String(),
                ]);
        }

        if ($domain === '') {
            return $this->fail('invalid_domain', 'A domain is required to verify this license.');
        }

        // Domain-lock: reuse an existing activation, or create one if under the limit.
        $activation = $license->activations()->where('domain', $domain)->first();

        if (! $activation) {
            $used = $license->activations()->count();
            if ($used >= $license->activation_limit) {
                return $this->fail('activation_limit_reached',
                    "This license is already active on its maximum of {$license->activation_limit} domain(s).", [
                        'activation_limit' => $license->activation_limit,
                        'activations_used' => $used,
                    ]);
            }

            $activation = $license->activations()->create([
                'domain'        => $domain,
                'ip'            => $ip,
                'activated_at'  => Carbon::now(),
                'last_check_at' => Carbon::now(),
            ]);
        } else {
            $activation->forceFill([
                'ip'            => $ip ?: $activation->ip,
                'last_check_at' => Carbon::now(),
            ])->save();
        }

        return [
            'valid'            => true,
            'status'           => 'active',
            'product'          => $license->product,
            'domain'           => $domain,
            'expires_at'       => optional($license->expires_at)->toIso8601String(),
            'activation_limit' => $license->activation_limit,
            'activations_used' => $license->activations()->count(),
            'plan'             => $license->plan
                ? ['name' => $license->plan->name, 'slug' => $license->plan->slug]
                : null,
            'addons'           => $this->entitledAddons($license),
            'message'          => 'License is valid.',
        ];
    }

    /**
     * Release a domain so the activation slot can be reused elsewhere.
     */
    public function deactivate(string $key, string $domain): array
    {
        $domain = self::normalizeDomain($domain);
        $license = License::where('license_key', trim($key))->first();

        if (! $license) {
            return $this->fail('not_found', 'No license matches this key.');
        }

        $deleted = $license->activations()->where('domain', $domain)->delete();

        return [
            'valid'   => true,
            'status'  => 'deactivated',
            'domain'  => $domain,
            'removed' => (bool) $deleted,
            'message' => $deleted ? 'Domain released.' : 'Domain was not active on this license.',
        ];
    }

    /**
     * The list of addon identifiers this license currently entitles.
     *
     * Union of the license's plan modules (live — upgrading a plan's module
     * list immediately applies to every license on that plan) and any explicit
     * per-license addon rows (for one-off extras).
     *
     * @return array<int, array{identifier:string,label:?string,expires_at:?string}>
     */
    public function entitledAddons(License $license): array
    {
        $entitled = [];

        foreach ($license->plan?->moduleIdentifiers() ?? [] as $identifier) {
            $entitled[$identifier] = [
                'identifier' => $identifier,
                'label'      => null,
                'expires_at' => null, // plan modules live and die with the license
            ];
        }

        foreach ($license->addons()->get() as $addon) {
            if ($addon->isEntitled()) {
                $entitled[$addon->addon_identifier] = [
                    'identifier' => $addon->addon_identifier,
                    'label'      => $addon->label,
                    'expires_at' => optional($addon->expires_at)->toIso8601String(),
                ];
            }
        }

        return array_values($entitled);
    }

    private function fail(string $status, string $message, array $extra = []): array
    {
        return array_merge([
            'valid'   => false,
            'status'  => $status,
            'addons'  => [],
            'message' => $message,
        ], $extra);
    }

    /**
     * Strip scheme, www., path and port; lowercase. Keeps subdomains distinct.
     */
    public static function normalizeDomain(string $domain): string
    {
        $domain = trim(strtolower($domain));
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('#^www\.#', '', $domain);
        $domain = explode('/', $domain)[0];
        $domain = explode(':', $domain)[0];

        return trim($domain);
    }
}
