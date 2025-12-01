<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Active Frontend Theme
    |--------------------------------------------------------------------------
    |
    | This value determines the default frontend theme used by the application.
    | You can change this by updating the 'active_theme' in settings table or
    | change the default here.
    |
    */
    'active' => env('FRONTEND_THEME', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Frontend Themes Path
    |--------------------------------------------------------------------------
    |
    | Path where frontend themes are stored
    |
    */
    'themes_path' => 'frontend',

    /*
    |--------------------------------------------------------------------------
    | Available Themes
    |--------------------------------------------------------------------------
    |
    | List of available themes in the system
    |
    */
    'available_themes' => [
        'default',
    ],
];
