<?php

return [

    /**
     * Route for Admin Dashboard
     */
    'admin_prefix' => 'admin',

    /**
     * slug untuk rute frontend
     * Jika menggunakan halaman baru, tambahkan disini
     */
    'excluded_slugs' => [
        'login',
        'register',
        'password',
        'email',
        'logout',
        'contact-us',
        'homepage-builder',
        'suryacms',
        'api',
        '_debugbar',
        'horizon',
        'telescope',
        'category',
        'media'
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup Tables
    |--------------------------------------------------------------------------
    |
    | Define the list of tables that belong to SuryaCMS. Only these tables
    | will be exported during the backup process, and only these tables
    | will be overwritten during the restore process.
    |
    */
    'tables' => [
        'categories',
        'contacts',
        'galleries',
        'languages',
        'menus',
        'pages',
        'posts',
        'settings',
        'youtube_videos',
        'users',
        'custom_blocks',
        //'announcements',
        //'reports',
        //'stocks',
    ],

    'monitor_token' => env('SURYACMS_MONITOR_TOKEN', null),
    'version'       => '2.1.18',
];
