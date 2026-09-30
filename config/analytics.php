<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google Analytics 4 (optional)
    |--------------------------------------------------------------------------
    |
    | Set GA4_MEASUREMENT_ID in .env (e.g. G-XXXXXXXXXX). When present, the
    | measurement snippet is injected from resources/views/app.blade.php.
    |
    */

    'ga4_measurement_id' => env('GA4_MEASUREMENT_ID'),

    'gtm_container_id' => env('GTM_CONTAINER_ID'),

    'meta_pixel_id' => env('META_PIXEL_ID'),

];
