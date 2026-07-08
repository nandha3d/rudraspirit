<?php

namespace App\Support;

/**
 * Client-side counterpart to the license server's ResponseSigner.
 * Verifies the HMAC-SHA256 signature on a license-server response body.
 * Keep this byte-identical to the server implementation.
 */
class LicenseSignature
{
    public static function verify(string $body, ?string $signature, string $secret): bool
    {
        if ($secret === '' || ! $signature) {
            return false;
        }

        return hash_equals(hash_hmac('sha256', $body, $secret), $signature);
    }
}
