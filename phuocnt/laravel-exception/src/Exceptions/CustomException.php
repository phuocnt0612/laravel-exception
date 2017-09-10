<?php

namespace Phuocnt\LaravelException\Exceptions;

use Symfony\Component\HttpFoundation\Response;

abstract class CustomException extends \Exception
{
    const DEFAULT_STATUS_CODE = Response::HTTP_INTERNAL_SERVER_ERROR;

    /**
     * @var \Exception
     */
    protected $exceptionInstance;

    protected $debug;

    protected $config;

    protected $statusCode;

    public function __construct(\Exception $e)
    {
        $this->exceptionInstance = $e;
        $this->debug = \Config::get('app.debug');
        $this->config = \Config::get('exception');
        $this->statusCode = $this->prepareStatusCode();
    }

    public function render($request, $data = null)
    {
        $data = $data ?: $this->getDefaultData();
        return response()->json()->setStatusCode($this->statusCode)->setData($data);
    }

    protected function getDefaultData()
    {
        if (!$this->debug) {
            return ['message' => $this->config['server_error_production']];
        }
        $data = [
            'message' => $this->exceptionInstance->getMessage(),
            'line'    => $this->exceptionInstance->getLine(),
            'file'    => $this->exceptionInstance->getFile(),
            'code'    => $this->exceptionInstance->getCode(),
        ];
        return $this->config['include_exception'] ? array_merge(
            $data,
            ['exception' => (string)$this->exceptionInstance]
        ) : $data;
    }

    private function prepareStatusCode()
    {
        return $this->findInExceptionInstance()
            ?: $this->statusCode
                ?: $this->findInLaravelException()
                    ?: self::DEFAULT_STATUS_CODE;
    }

    private function findInExceptionInstance()
    {
        return property_exists($this->exceptionInstance, 'statusCode') ? $this->exceptionInstance->statusCode : null;
    }

    private function findInLaravelException()
    {
        return method_exists($this->exceptionInstance, 'getStatusCode') ? $this->exceptionInstance->getStatusCode(
        ) : null;
    }
}