<?php
/**
 * Created by PhpStorm.
 * User: phuoc
 * Date: 09/09/2017
 * Time: 17:33
 */

namespace Phuocnt\LaravelException\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class AuthenticationException extends CustomException
{
    protected $statusCode = Response::HTTP_UNAUTHORIZED;

    public function __construct(\Exception $e)
    {
        parent::__construct($e);
    }

}