# Laravel 5.5 custom exception

[![Latest Version](https://img.shields.io/github/release/phuocnt0612/laravel-exception.svg?style=flat-square)](https://github.com/phuocnt0612/laravel-exception/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

## Introduction

This is a Laravel exception handler build specifically for APIs.

Get idea from [Heimdal](https://github.com/esbenp/heimdal)

### Why is it needed?

When building APIs there are specific formatting do's and dont's on how to send errors back to the user. Frameworks like Laravel are not
build specifically for API builders. This small library just bridges that gap. For instance, specifications like [JSON API](https://jsonapi.org)
have [guidelines for how errors should be formatted](http://jsonapi.org/format/#error-objects).

## Installation

```bash
composer require phuoc/laravel-exception
```

Add the service provider to `config/app.php` if you've disabled `package auto-discovery` feature

```
// other providers...
Phuocnt\LaravelException\Providers\LaravelServiceProvider::class,
```

Publish the configuration.

```
php artisan vendor:publish --provider="Phuocnt\LaravelException\Providers\LaravelServiceProvider"
```

Change `App\Exceptions\Handler`'s extends class

```
namespace App\Exceptions;

use Exception;
use Phuocnt\LaravelException\ExceptionHandler;

class Handler extends ExceptionHandler {
    ...
}
```

Clear cache if it need to

```
php artisan cache:clear
```

Autoload

```
composer dump-autoload
```


## Configuration

### Exceptions

This package already comes with sensible custom exceptions out of the box. In `config/exception.php` is a section where
the exception priority is defined.

```php
    // Has to be in prioritized order, e.g. highest priority first.
    'map' => [
        AuthenticationException::class => CustomException\AuthenticationException::class,
        AuthorizationException::class => CustomException\AuthorizationException::class,
        ValidationException::class => CustomException\ValidationException::class,
        Exception::class => CustomException\Exception::class,
    ],
```

The higher the entry, the higher the priority. In this example, a `AuthenticationException` will be formatted used the
`CustomException\AuthenticationException` because it is the first entry. However, an `NotFoundHttpException` will not match
`AuthenticationException` but will match `Exception` (since it is a child class hereof) and will therefore
be formatted using the `Exception`.

### Write your custom exception easily

Write your custom exception class extend `Phuocnt\LaravelException\Exceptions\CustomException` class

You may want to define default `$statusCode` or `render` method which helps to define your custom response
 
```php
<?php

namespace App\Exceptions\YourExceptions;

use Phuocnt\LaravelException\Exceptions\CustomException as BaseCustomException;
use Symfony\Component\HttpFoundation\Response;

class UnprocessableEntityHttpException extends BaseCustomException
{
    protected $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    public function __construct(\Exception $e)
    {
        parent::__construct($e);
    }

    pubic function render($request, $data = null) {
        $data = [
            'firstLine' => 'firstLine',    
            'secondLine' => 'secondLine',    
        ];
        return response()->json()->setStatusCode($this->statusCode)->setData($data);
    }
}
```

Now we simply add it to `config/exception.php`

```php
    // Has to be in prioritized order, e.g. highest priority first.
    'map' => [
        \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException::class 
            => App\Exceptions\YourExceptions\UnprocessableEntityHttpException::class,
        AuthenticationException::class => CustomException\AuthenticationException::class,
        AuthorizationException::class => CustomException\AuthorizationException::class,
        ValidationException::class => CustomException\ValidationException::class,
        Exception::class => CustomException\Exception::class,
    ],
```

Now all `UnprocessableEntityHttpException`s will be formatted using our custom exception.


## Usage

Write your exception as usual

Override `$statusCode` if needed else it will be get in order: `this exception class` > `your custom exception format` > `default laravel status code for this exception` > `500` 

You can also override `render` method here, too

```php
<?php

namespace App\Exceptions;

class SomeException extends \Exception
{
    public $statusCode = 400;

    public function __construct($message = "Oops, exception's thrown", $code = 99999)
    {
        parent::__construct(
            $message,
            $code
        );
    }
}
```
 
## Default exception's format

### Production

In production, you may want to turn `APP_DEBUG=false` in `.env`.

Almost common errors will have response below with appropriate status code

```json
    {
        "message": "An error occurred."
    }
```

### Dev

#### Validation error

Status code: 422
```json
{
    "errors": {
        "id": [
            "The id field is required."
        ],
        "name": [
            "The name field is required."
        ]
    }
}
```

#### Common errors
 
 ```json
 {
     "message": "This action is unauthorized.",
     "line": 165,
     "file": "/var/www/lar55/vendor/laravel/framework/src/Illuminate/Foundation/Http/FormRequest.php",
     "code": 0,
     "exception": "Illuminate\\Auth\\Access\\AuthorizationException: This action is unauthorized. in /var..."
 }
 ```
## License

The MIT License (MIT). Please see [License File](https://github.com/phuocnt/laravel-exception/blob/master/LICENSE) for more information.