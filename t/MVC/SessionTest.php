<?php

namespace ESuite\MVC;

use ESuite\ESuiteTest;
use Empathy\MVC\Session;


class SessionTest extends ESuiteTest
{

    protected function setUp()
    {
        //
    }

    
    public function testLoadUIVars()
    {
        // set bar to dummy value
        $_GET['bar'] = 'baz';
        Session::loadUIVars('foo', array('bar'));
    }


}
