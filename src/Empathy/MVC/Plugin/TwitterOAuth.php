<?php


namespace Empathy\MVC\Plugin;

use Empathy\MVC\Plugin as Plugin;

/**
 * Empathy TwitterOAuth Plugin
 * @file            Empathy/MVC/Plugin/TwitterOAuth.php
 * @description     uses abraham-twitteroauth - https://github.com/abraham/twitteroauth
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class TwitterOAuth
{

    public function __construct()
    {
        require 'twitteroauth/twitteroauth.php';
    }
}
