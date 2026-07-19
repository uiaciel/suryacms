<?php

return [
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
];
