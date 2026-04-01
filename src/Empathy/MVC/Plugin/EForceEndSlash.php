<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin;

/**
 * Empathy EForceEndSlash Plugin
 * @file            Empathy/MVC/Plugin/EForceEndSlash.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class EForceEndSlash extends Plugin implements PreDispatch
{
    public function onPreDispatch(): void
    {
        // check if target looks life a file first
        $uri_arr = explode('/', (string) $_SERVER['REQUEST_URI']);
        if (!strpos($uri_arr[count($uri_arr) - 1], '.') && !preg_match('/\/$/', (string) $_SERVER['REQUEST_URI'])) {
            $location = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/';
            header('Location: '.$location);
            exit();
        }
    }
}
