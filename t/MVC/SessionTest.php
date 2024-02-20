<?php

namespace ESuite\MVC;

use ESuite\ESuiteTest;
use Empathy\MVC\Session;
use ESuite\Util\Config;

class SessionTest extends ESuiteTest
{

    protected function setUp(): void
    {
        //
    }

    
    public function testLoadUIVars()
    {
        // set bar to dummy value
        $_GET['bar'] = 'baz';
        Session::up();
        Session::loadUIVars('foo', ['bar']);

        // session doesn't work because of test mode
        $this->assertEquals(false, Session::getUISetting('foo', 'bar'));
    }
}
