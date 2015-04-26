<?php

namespace Empathy\MVC;

class RequestException extends Exception
{
    const NOT_FOUND = 0;
    const BAD_REQUEST = 1;

    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
