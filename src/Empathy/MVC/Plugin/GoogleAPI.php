<?php

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

/**
 * Empathy GoogleAPI Plugin
 * @file            Empathy/MVC/Plugin/GoogleAPI.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class GoogleAPI extends Plugin
{

    public function __construct()
    {
        require('google-api-php-client/src/Google_Client.php');
        require('google-api-php-client/src/contrib/Google_PlusService.php');
    }
}
