<?php

namespace App\Services\License;

/**
 * Normalizes a domain to the same canonical form the license server uses, so a
 * key activated as "https://www.shop.com/" matches "shop.com". Keep this in
 * sync with the server's LicenseVerifier::normalizeDomain().
 */
class DomainNormalizer
{
    public static function normalize(string $domain): string
    {
        $domain = trim(strtolower($domain));
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('#^www\.#', '', $domain);
        $domain = explode('/', $domain)[0];
        $domain = explode(':', $domain)[0];

        return trim($domain);
    }
}
