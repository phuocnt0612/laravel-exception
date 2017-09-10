<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Phuocnt\LaravelException\Exceptions as CustomException;

return [
    // Has to be in prioritized order, e.g. highest priority first.
    'map' => [
        AuthenticationException::class => CustomException\AuthenticationException::class,
        AuthorizationException::class => CustomException\AuthorizationException::class,
        ValidationException::class => CustomException\ValidationException::class,
        Exception::class => CustomException\Exception::class,
    ],

    'server_error_production' => 'An error occurred.',
    'include_exception' => true
];
