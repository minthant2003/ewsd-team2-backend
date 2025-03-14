<?php

return [
    'paths' => ['api/*', 'storage/*'], // Allow storage access
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'], // Allow all origins (or specify Next.js domain)
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];