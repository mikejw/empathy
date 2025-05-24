<?php

namespace Empathy\MVC;

/**
 * Empathy RequestException class
 * @file            Empathy/MVC/RequestException.php
 * @description
 * @author          Michael J. Whiting
 * @license         See LICENCE
 *
 * (c) copyright Michael J. Whiting

 * with this source code in the file LICENSE
 */
class RequestException extends Exception
{
    const NOT_FOUND = 0;
    const BAD_REQUEST = 1;
    const INTERNAL_ERROR = 2;
    const NOT_AUTHORIZED = 3;
    const NOT_AUTHENTICATED = 4;
    const METHOD_NOT_ALLOWED = 5;

    public function __construct($message, $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
