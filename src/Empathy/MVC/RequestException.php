<?php

declare(strict_types=1);

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
    public const NOT_FOUND = 0;
    public const BAD_REQUEST = 1;
    public const INTERNAL_ERROR = 2;
    public const NOT_AUTHORIZED = 3;
    public const NOT_AUTHENTICATED = 4;
    public const METHOD_NOT_ALLOWED = 5;

    public function __construct($message, $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
