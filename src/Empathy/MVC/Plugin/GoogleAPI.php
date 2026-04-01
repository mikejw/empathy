<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin;

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
        if (file_exists('google-api-php-client/src/Google_Client.php')) {
            include __DIR__ . '/google-api-php-client/src/Google_Client.php';
        }

        if (file_exists('google-api-php-client/src/contrib/Google_PlusService.php')) {
            include __DIR__ . '/google-api-php-client/src/contrib/Google_PlusService.php';
        }
    }
}
