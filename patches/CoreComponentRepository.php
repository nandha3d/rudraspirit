<?php

namespace MehediIitdu\CoreComponentRepository;

/**
 * Neutralized CoreComponentRepository.
 *
 * The original vendor class phoned home to an external activation server and, on
 * a "bad" response, redirected the whole app to the vendor purchase-code wizard
 * — which fired on login/admin pages. All methods are
 * now safe no-ops with the original signatures, so every caller keeps working
 * without contacting the vendor. deploy.sh copies this over the vendor file after
 * each composer install (composer would otherwise restore the original).
 */
class CoreComponentRepository
{
    public static function instantiateShopRepository()
    {
        // no-op
    }

    protected static function serializeObjectResponse($zn, $request_data_json)
    {
        return '';
    }

    protected static function finalizeRepository($rn)
    {
        // no-op
    }

    public static function initializeCache()
    {
        // no-op
    }

    public static function finalizeCache($addon)
    {
        // no-op
    }
}
