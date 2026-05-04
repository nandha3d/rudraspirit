<?php

namespace App\Http\Controllers\Api\V3;

use Illuminate\Http\JsonResponse;

class SettingsController extends Controller
{
    /**
     * GET /api/v3/settings
     * Returns public store settings safe for frontend consumption.
     */
    public function index(): JsonResponse
    {
        return $this->successResponse([
            'store_name'           => get_setting('website_name'),
            'store_logo'           => uploaded_asset(get_setting('header_logo')),
            'store_favicon'        => uploaded_asset(get_setting('fav_icon')),
            'store_motto'          => get_setting('site_motto'),
            'currency_code'        => \App\Models\Currency::find(get_setting('system_default_currency'))->code ?? 'USD',
            'currency_symbol'      => currency_symbol(),
            'language'             => app()->getLocale(),
            'cash_on_delivery'     => get_setting('cash_on_delivery') == 1,
            'wallet_system'        => get_setting('wallet_system') == 1,
            'guest_checkout'       => get_setting('guest_checkout_activation') == 1,
            'min_order_amount'     => (float) (get_setting('minimum_order_amount') ?? 0),
            'min_order_check'      => get_setting('minimum_order_amount_check') == 1,
            'conversation_system'  => get_setting('conversation_system') == 1,
        ]);
    }

    /**
     * GET /api/v3/currencies
     */
    public function currencies(): JsonResponse
    {
        $currencies = \App\Models\Currency::where('status', 1)->get()->map(function ($c) {
            return [
                'id'     => $c->id,
                'name'   => $c->name,
                'code'   => $c->code,
                'symbol' => $c->symbol,
                'rate'   => (float) $c->exchange_rate,
            ];
        });

        return $this->successResponse($currencies);
    }

    /**
     * GET /api/v3/languages
     */
    public function languages(): JsonResponse
    {
        $languages = \App\Models\Language::where('status', 1)->get()->map(function ($l) {
            return [
                'id'     => $l->id,
                'name'   => $l->name,
                'code'   => $l->code,
                'rtl'    => (bool) $l->rtl,
            ];
        });

        return $this->successResponse($languages);
    }
}
