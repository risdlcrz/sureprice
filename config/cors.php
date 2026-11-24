<?php

// Cross-origin requests: the app is a same-origin web app with no public API, so only its own origin is allowed.
$url = parse_url((string) env('APP_URL', 'http://localhost'));
$origin = isset($url['scheme'], $url['host'])
    ? $url['scheme'] . '://' . $url['host'] . (isset($url['port']) ? ':' . $url['port'] : '')
    : null;

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],

    'allowed_origins' => array_filter([$origin]),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'X-Requested-With', 'X-CSRF-TOKEN', 'X-XSRF-TOKEN', 'Accept'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
