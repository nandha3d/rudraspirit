<?php

/*
 * RudraSpirit theme — runtime config.
 * Reading these via config() (instead of env() in Blade) keeps them working
 * after `php artisan config:cache`, where env() returns null in views.
 */
return [
    'tracking_id'       => env('TRACKING_ID'),
    'facebook_pixel_id' => env('FACEBOOK_PIXEL_ID'),
    'whatsapp_number'   => env('WHATSAPP_NUMBER'),
    'demo_mode'         => env('DEMO_MODE', 'Off'),

    /*
     * Reserved record IDs that the layout treats specially (custom alerts /
     * dynamic popups). Centralised here so the values are not bare "magic
     * numbers" scattered across Blade. Values match the existing seeded rows.
     */
    'special_alerts' => [
        'cookie'            => 1,
        'club_point_review' => 200,
        'otp'               => 300,
    ],
    'special_popups' => [
        'subscribe'  => 1,
        'unreviewed' => 100,
    ],
];
