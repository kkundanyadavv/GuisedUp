<?php

return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1,::1')),
    'guard' => ['web'],
    'expiration' => null,
    'token_prefix' => '',
    'middleware' => ['verify_csrf_token' => Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class],
];
