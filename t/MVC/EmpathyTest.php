<?php

namespace ESuite\MVC;

use Empathy\MVC\DBC;
use ESuite\ESuiteTest;


class EmpathyTest extends ESuiteTest
{
    private $config_dir;

    protected function setUp()
    {
        $this->config_dir = realpath(dirname(realpath(__FILE__)).'/../../eaa/');
    }

    private function createMVC($persistent=false)
    {   
        $this->expectOutputRegex('/(Setting header)/');     
        return new \Empathy\MVC\Empathy($this->config_dir, $persistent);
    }
    
    public function testNew()
    {
        $this->setExpectedException(
            'Empathy\MVC\Exception', 'Dispatch error 1 : Missing class file'
        );    
        $mvc = $this->createMVC();
    }


    public function testErrors()
    {
        $mvc = $this->createMVC(true);
        $errors = $mvc->getErrors();
        $this->assertEmpty($errors);
        $this->assertFalse($mvc->hasErrors());
        $this->assertEmpty($mvc->errorsToString());

        $this->expectOutputRegex('/Fatal error/');
        $mvc->errorHandler(E_ERROR, 'dummy error', 'someFile.php', 1);

        $mvc->errorHandler(E_USER_WARNING, 'dummy error', 'someFile.php', 1);
        $this->assertRegExp('/Warning/', $mvc->errorsToString());

        $mvc->errorHandler(E_NOTICE, 'dummy error', 'someFile.php', 1);
        $this->assertRegExp('/Notice/', $mvc->errorsToString());

        $mvc->errorHandler(E_STRICT, 'dummy error', 'someFile.php', 1);
        $this->assertRegExp('/Strict/', $mvc->errorsToString());

        $mvc->errorHandler(0, 'dummy error', 'someFile.php', 1);
        $this->assertRegExp('/Unknown/', $mvc->errorsToString());
    }




}
