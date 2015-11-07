<?php


namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

/**
 * Empathy TwitterOAuth Plugin
 * @file            Empathy/MVC/Plugin/TwitterOAuth.php
 * @description     uses abraham-twitteroauth - https://github.com/abraham/twitteroauth
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class TwitterOAuth
{

    public function __construct()
    {
        require 'twitteroauth/twitteroauth.php';
    }
}
