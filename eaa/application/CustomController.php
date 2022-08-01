<?php

namespace Empathy\MVC\Controller;
use Empathy\MVC\Controller as Controller;

/**
 * Empathy Custom Controller
 * @file			eaa/application/CustomController.php
 * @description		Site-wide controller customisation goes in this file.
 * @author			Mike Whiting
 * @license			See LICENCE
 *
 * (c) copyright Mike Whiting
 */
class CustomController extends Controller
{

    /**
     * Calls to custom routines can go in here.
     *
     * @return void
     */
    public function __construct($boot, $useSession = true)
    {
        parent::__construct($boot, $useSession);
    }

}
