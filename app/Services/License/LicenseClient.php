<?php

namespace App\Services\License;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client-side license verifier.
 *
 * Talks to the license server (config/license.php), verifies the response
 * signature, caches the result, and exposes simple predicates the app uses to
 * decide what to gate. It NEVER touches authentication and never grants access
 * to anything on its own — callers decide what to do with the answer.
 */
class LicenseClient
{
    private const CACHE_KEY = 'license.verification';

    /**
     * Full verification payload (cached). Shape mirrors the server's `data`:
     * ['valid'=>bool,'status'=>string,'addons'=>[...], ...] plus a local
     * 'checked_at' and 'source' (server|cache|fail_open|fail_closed|disabled).
     */
    public function check(bool $fresh = false): array
    {
        if (! $this->enabled()) {
            return ['valid' => true, 'status' => 'disabled', 'addons' => [], 'source' => 'disabled'];
        }

        if ($fresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(
            self::CACHE_KEY,
            now()->addMinutes((int) config('license.cache_ttl', 720)),
            fn () => $this->fetch(),
        );
    }

    public function isValid(): bool
    {
        return (bool) ($this->check()['valid'] ?? false);
    }

    /**
     * @return array<int,string> Entitled addon identifiers.
     */
    public function entitledAddons(): array
    {
        $addons = $this->check()['addons'] ?? [];

        return array_values(array_filter(array_map(
            fn ($a) => is_array($a) ? ($a['identifier'] ?? null) : $a,
            $addons,
        )));
    }

    public function isAddonEntitled(string $identifier): bool
    {
        // If the license itself is invalid, nothing is entitled.
        if (! $this->isValid()) {
            return false;
        }

        return in_array($identifier, $this->entitledAddons(), true);
    }

    public function enforceMode(): string
    {
        return (string) config('license.enforce', 'off');
    }

    public function enabled(): bool
    {
        return $this->enforceMode() !== 'off'
            && config('license.server_url')
            && config('license.key');
    }

    public function domain(): string
    {
        $domain = (string) config('license.domain');
        if ($domain === '') {
            $domain = parse_url((string) config('app.url'), PHP_URL_HOST) ?: (string) config('app.url');
        }

        return \App\Services\License\DomainNormalizer::normalize($domain);
    }

    /**
     * Contact the server and validate the signed response.
     */
    private function fetch(): array
    {
        $url = rtrim((string) config('license.server_url'), '/') . '/api/v1/licenses/verify';
        $secret = (string) config('license.signing_secret');

        try {
            $response = Http::timeout((int) config('license.timeout', 8))
                ->acceptJson()
                ->asJson()
                ->post($url, [
                    'key'     => config('license.key'),
                    'domain'  => $this->domain(),
                    'product' => config('license.product'),
                ]);
        } catch (\Throwable $e) {
            Log::warning('License server unreachable: ' . $e->getMessage());

            return $this->unreachable();
        }

        if (! $response->successful()) {
            Log::warning('License server returned HTTP ' . $response->status());

            return $this->unreachable();
        }

        // Verify the signature over the EXACT body we received.
        $body = $response->body();
        $signature = $response->header('X-License-Signature');

        if ($secret !== '' && ! \App\Support\LicenseSignature::verify($body, $signature, $secret)) {
            Log::error('License response signature mismatch — refusing to trust it.');
            // A signature mismatch means a tampered/spoofed response: fail closed.
            return ['valid' => false, 'status' => 'signature_mismatch', 'addons' => [], 'source' => 'fail_closed', 'checked_at' => now()->toIso8601String()];
        }

        $data = ($response->json()['data'] ?? []);
        $data['source'] = 'server';
        $data['checked_at'] = now()->toIso8601String();

        return $data;
    }

    /**
     * No definitive answer from the server: apply fail-open / fail-closed policy.
     */
    private function unreachable(): array
    {
        $failOpen = (bool) config('license.fail_open', true);

        return [
            'valid'      => $failOpen,
            'status'     => $failOpen ? 'unreachable_fail_open' : 'unreachable_fail_closed',
            'addons'     => [], // never grant addon entitlements without a real answer
            'source'     => $failOpen ? 'fail_open' : 'fail_closed',
            'checked_at' => now()->toIso8601String(),
        ];
    }
}
