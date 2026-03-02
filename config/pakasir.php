<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pakasir Project Slug
    |--------------------------------------------------------------------------
    | The slug of your Pakasir project, found in the project detail page.
    */
    'project' => env('PAKASIR_PROJECT', ''),

    /*
    |--------------------------------------------------------------------------
    | Pakasir API Key
    |--------------------------------------------------------------------------
    | The API key of your Pakasir project.
    */
    'api_key' => env('PAKASIR_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Sandbox Mode
    |--------------------------------------------------------------------------
    | When true, payment simulation endpoints are enabled.
    */
    'sandbox' => env('PAKASIR_SANDBOX', true),
];
