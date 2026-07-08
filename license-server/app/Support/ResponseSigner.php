<?php

namespace App\Support;

/**
 * Shared HMAC signing/verification for license responses.
 *
 * The signature is HMAC-SHA256 over the exact JSON body using the shared
 * secret. Both the server (when responding) and the client (when validating a
 * response) use this, so keep them byte-identical.
 */
class ResponseSigner
{
    public static function sign(string $body, string $secret): string
    {
        return hash_hmac('sha256', $body, $secret);
    }

    public static function verify(string $body, string $signature, string $secret): bool
    {
        if ($secret === '' || $signature === '') {
            return false;
        }

        return hash_equals(self::sign($body, $secret), $signature);
    }
}
