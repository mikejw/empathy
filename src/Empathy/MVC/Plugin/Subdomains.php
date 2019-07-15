<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Testable;
use Empathy\MVC\Plugin as Plugin;
use Empathy\MVC\Config;

/**
 * Empathy EDefault Plugin
 * @file            Empathy/MVC/Plugin/Subdomains.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class Subdomains extends Plugin implements PreDispatch
{

    // regex from http://stackoverflow.com/a/10526727/6108127
    public function onPreDispatch()
    {
        Config::store('WEB_ROOT_DEFAULT', Config::get('WEB_ROOT'));
        if (isset($_SERVER['HTTP_HOST'])) {
            $matches = [];
            if (preg_match(
               '/(?:http[s]*\:\/\/)*(.*?)\.(?=[^\/]*\..{2,5})/i',
               $_SERVER['HTTP_HOST'],
               $matches)) {
                 Config::store('SUBDOMAIN', $matches[1]);
                 Config::store('WEB_ROOT', $matches[0].Config::get('WEB_ROOT'));
            }
        }
    }
}
