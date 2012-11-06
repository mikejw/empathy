<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

class EDefault-1.0 extends Plugin implements PreDispatch
{
    public function __construct()
    {
        //
    }

    public function onPreDispatch()
    {
        date_default_timezone_set('Europe/London');
        header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    }
}
