<?php

namespace Empathy\MVC;

/**
 * Empathy RequestException class
 * @file            Empathy/MVC/RequestException.php
 * @description
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class RequestException extends Exception
{
    const NOT_FOUND = 0;
    const BAD_REQUEST = 1;
    const INTERNAL_ERROR = 2;

    public function __construct($message, $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
