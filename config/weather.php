<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Weather provider
    |--------------------------------------------------------------------------
    | Default: Open-Meteo (no API key required). Optional key is kept for
    | future providers or authenticated Open-Meteo commercial plans.
    |
    */

    'provider' => env('WEATHER_PROVIDER', 'open-meteo'),

    'api_key' => env('WEATHER_API_KEY'),

    'forecast_url' => env('WEATHER_FORECAST_URL', 'https://api.open-meteo.com/v1/forecast'),

    'geocode_url' => env('WEATHER_GEOCODE_URL', 'https://geocoding-api.open-meteo.com/v1/search'),

    'cache_hours' => (int) env('WEATHER_CACHE_HOURS', 2),

    'timeout' => (int) env('WEATHER_TIMEOUT', 10),

];
