<?php

namespace Empathy\MVC\Plugin;
use Empathy\MVC\Plugin as Plugin;

class GoogleAPI extends Plugin
{

    public function __construct()
    {
        require('google-api-php-client/src/Google_Client.php');
        require('google-api-php-client/src/contrib/Google_PlusService.php');
    }

    

}
