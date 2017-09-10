<?php

namespace Phuocnt\LaravelException;

use Exception;
use Phuocnt\LaravelException\Exceptions\CustomException;
use ReflectionClass;
use InvalidArgumentException;
use Illuminate\Foundation\Exceptions\Handler as LaravelExceptionHandler;
use Illuminate\Contracts\Container\Container;

class ExceptionHandler extends LaravelExceptionHandler
{
    protected $config;

    /**
     * ExceptionHandler constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->config = $container['config']->get('exception');
    }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $exception
     *
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception                $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $e)
    {
        if (!$request->expectsJson() || method_exists($e, 'render')) {
            return parent::render($request, $e);
        }

        $exceptionsMap = $this->config['map'];

        foreach ($exceptionsMap as $coreException => $customException) {
            if (!($e instanceof $coreException)) {
                continue;
            }
            if (!class_exists($customException) || !(new ReflectionClass($customException))->isSubclassOf(
                    new ReflectionClass(CustomException::class)
                )
            ) {
                throw new InvalidArgumentException(
                    sprintf(
                        "%s is not a valid exception class.",
                        $customException
                    )
                );
            }

            $customExceptionInstance = new $customException($e);
            return parent::render($request, $customExceptionInstance);
        }
        return parent::render($request, $e);
    }
}
