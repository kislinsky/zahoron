<?php

return [
    'driver' => env('LOCATION_DRIVER', 'null'), // Используем 'null' драйвер для локального окружения

    'drivers' => [
        'null' => [
            // Пустая конфигурация для тестов
        ],
        
        'ipapi' => [
            'token' => env('IPAPI_TOKEN'),
            'secure' => true,
        ],
    ],

    'fallbacks' => [
        'ipapi' => 'null',
    ],
];