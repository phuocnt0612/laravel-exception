<?php
/**
 * Created by PhpStorm.
 * User: phuoc
 * Date: 10/09/2017
 * Time: 19:39
 */

namespace Phuocnt\LaravelException\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ValidationException extends CustomException
{
    protected $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

    public function __construct(\Exception $e)
    {
        parent::__construct($e);
    }

    public function render($request, $data = null)
    {
        $data = [
          'errors' => $this->exceptionInstance->validator->errors()->getMessages()
        ];
        return response()->json()->setStatusCode($this->statusCode)->setData($data);
    }
}