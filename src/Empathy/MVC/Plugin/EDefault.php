<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Testable;
use Empathy\MVC\Plugin as Plugin;

/**
 * Empathy EDefault Plugin
 * @file            Empathy/MVC/Plugin/EDefault.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class EDefault extends Plugin implements PreDispatch
{

    public function onPreDispatch()
    {
        date_default_timezone_set('Europe/London');
        Testable::header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        Testable::header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    }
}
