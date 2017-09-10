<?php
/**
 * Created by PhpStorm.
 * User: phuoc
 * Date: 09/09/2017
 * Time: 17:33
 */

namespace Phuocnt\LaravelException\Exceptions;


class Exception extends CustomException
{
    public function __construct(\Exception $e)
    {
        parent::__construct($e);
    }

}