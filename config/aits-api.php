<?php

return [
    /**
     * Configuration to set proxy to your API requests
     * You can replace the logic according to your needs.
     * Ex. with_proxy => true will always set proxy in all environments.
     */
    'with_proxy' => env('APP_ENV') === 'local',

    /**
     * BASE URL for AITS endpoint
     */
    'base_url' => env('AITS_BASE_URL', null),

    /**
     * Proxy Server configuration.
     */
    'proxy' => [
        'scheme' => env('AITS_PROXY_SCHEME', 'http://'),
        'host' => env('AITS_PROXY_HOST', null),
        'port' => env('AITS_PROXY_PORT', null),
        'username' => env('AITS_PROXY_USERNAME', null),
        'password' => env('AITS_PROXY_PASSWORD', null),
    ],
];