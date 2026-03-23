<?php

declare(strict_types=1);

namespace ESuite\MVC;

use Empathy\MVC\Session;
use Empathy\MVC\Util\Testing\ESuiteTestCase;

class SessionTest extends ESuiteTestCase
{
    protected function setUp(): void
    {
        //
    }


    public function testLoadUIVars(): void
    {
        // set bar to dummy value
        $_GET['bar'] = 'baz';
        Session::up();
        Session::loadUIVars('foo', ['bar']);

        // session doesn't work because of test mode
        $this->assertEquals(false, Session::getUISetting('foo', 'bar'));
    }
}
