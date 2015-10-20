<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

/**
 * Empathy EDefault Plugin
 * @file            Empathy/MVC/Plugin/EDefault.php
 * @description     
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class EDefault extends Plugin implements PreDispatch
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
