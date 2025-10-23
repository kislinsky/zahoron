<?php

return [
    'driver' => env('SCOUT_DRIVER', 'tntsearch'),

    'tntsearch' => [
        'storage' => storage_path('app'),
        'driver' => 'mysql', // Используем MySQL вместо SQLite
        'database' => env('DB_DATABASE'),
        'username' => env('DB_USERNAME'),
        'password' => env('DB_PASSWORD'),
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'fuzziness' => true,
        'fuzzy' => [
            'prefix_length' => 2,
            'max_expansions' => 50,
            'distance' => 2
        ],
    ],
];