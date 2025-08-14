<?php

return [
    'driver' => env('LOCATION_DRIVER', 'null'),
    'drivers' => [
        'ipapi' => [
            'token' => env('IPAPI_TOKEN'),
            'secure' => true,
        ],
    ],
    'fallbacks' => [
        'ipapi' => 'null',
    ],
];