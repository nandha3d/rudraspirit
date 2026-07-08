<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LicenseVerifier;
use App\Support\ResponseSigner;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Public licensing API consumed by client deployments.
 *
 * Every response body is HMAC-signed (X-License-Signature header) with the
 * shared secret so clients can reject spoofed answers.
 */
class LicenseController extends Controller
{
    public function __construct(private LicenseVerifier $verifier)
    {
    }

    /**
     * POST /api/v1/licenses/verify
     * Body: { key, domain, product? }
     */
    public function verify(Request $request): Response
    {
        $data = $request->validate([
            'key'     => ['required', 'string', 'max:64'],
            'domain'  => ['required', 'string', 'max:255'],
            'product' => ['nullable', 'string', 'max:100'],
        ]);

        $payload = $this->verifier->verify(
            $data['key'],
            $data['domain'],
            $data['product'] ?? null,
            $request->ip(),
        );

        return $this->signed($payload);
    }

    /**
     * POST /api/v1/licenses/deactivate
     * Body: { key, domain }  — releases a domain's activation slot.
     */
    public function deactivate(Request $request): Response
    {
        $data = $request->validate([
            'key'    => ['required', 'string', 'max:64'],
            'domain' => ['required', 'string', 'max:255'],
        ]);

        $payload = $this->verifier->deactivate($data['key'], $data['domain']);

        return $this->signed($payload);
    }

    /**
     * Wrap a payload in the standard envelope and attach the HMAC signature.
     */
    private function signed(array $payload): Response
    {
        $envelope = [
            'data' => $payload,
            'meta' => [
                'server_time' => now()->toIso8601String(),
                'version'     => '1.0',
            ],
        ];

        // Sign the exact serialized body the client will receive.
        $body = json_encode($envelope, JSON_UNESCAPED_SLASHES);
        $signature = ResponseSigner::sign($body, (string) config('license.signing_secret'));

        return response($body, 200)
            ->header('Content-Type', 'application/json')
            ->header('X-License-Signature', $signature);
    }
}
