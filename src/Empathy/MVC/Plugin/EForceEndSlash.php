<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

/**
 * Empathy EForceEndSlash Plugin
 * @file            Empathy/MVC/Plugin/EForceEndSlash.php
 * @description     
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class EForceEndSlash extends Plugin implements PreDispatch
{
   
    public function onPreDispatch()
    {   
        // check if target looks life a file first
        $uri_arr = explode('/', $_SERVER['REQUEST_URI']);
        if (!strpos($uri_arr[sizeof($uri_arr)-1], '.')) {
            if (!preg_match('/\/$/', $_SERVER['REQUEST_URI'])) {
                $location = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'/';
                header('Location: '.$location);
                exit();
            }
        }
    }
}
