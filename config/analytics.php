<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Visitor analytics
    |--------------------------------------------------------------------------
    |
    | Storefront page views are recorded for the admin "Visitor Analytics" page.
    |
    */

    'enabled' => (bool) env('ANALYTICS_ENABLED', true),

    // Don't count the shop owner's own browsing while logged in as an admin.
    'exclude_admins' => (bool) env('ANALYTICS_EXCLUDE_ADMINS', true),

    // A visit (session) ends after this many minutes without a page view.
    'visit_timeout_minutes' => 30,

    // A visitor counts as "online now" if they viewed a page within this many minutes.
    'online_minutes' => 5,

    // Anonymous visitor cookie.
    'cookie' => 'odin_vid',
    'cookie_days' => 365,

    /*
    |--------------------------------------------------------------------------
    | Country lookup
    |--------------------------------------------------------------------------
    |
    | If the site runs behind Cloudflare, the free "CF-IPCountry" header is used.
    | Otherwise new IP addresses are looked up in batches (only when the admin opens
    | the analytics page) using the service below. The default, ip-api.com, is free
    | for non-commercial use only — for the live shop use Cloudflare or a paid plan,
    | or set ANALYTICS_GEOIP=false to turn lookups off.
    |
    */

    'geoip' => [
        'enabled' => (bool) env('ANALYTICS_GEOIP', true),
        'batch_url' => env('ANALYTICS_GEOIP_URL', 'http://ip-api.com/batch?fields=status,countryCode,country,query'),
        'timeout' => 4,
    ],

    // Page-view data older than this is removed by "php artisan analytics:prune".
    'keep_days' => (int) env('ANALYTICS_KEEP_DAYS', 395),

];
