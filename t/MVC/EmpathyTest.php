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
        //$this->expectOutputRegex('/<h1>Empathy<\/h1>/');
        $mvc = $this->createMVC();
    }


    public function testErrors()
    {
        //$this->markTestSkipped();
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

    public function testExceptions()
    {
        //$this->markTestSkipped();
        $mvc = $this->createMVC(true);
        $this->expectOutputRegex('/die: Safe exception: some error/');
        $mvc->exceptionHandler(new \Empathy\MVC\SafeException('some error'));

        $mvc = $this->createMVC(true);
        $this->expectOutputRegex('/<h2>some error<\/h2>/');
        $mvc->exceptionHandler(new \Exception('some error'));

        // code = 0 => 404
        $mvc = $this->createMVC(true);
        $this->expectOutputRegex('/Not found/');    
        $mvc->exceptionHandler(new \Empathy\MVC\RequestException('some error'));

        // code = 1 => 500
        $mvc = $this->createMVC(true);
        $this->expectOutputRegex('/Bad request/');    
        $mvc->exceptionHandler(new \Empathy\MVC\RequestException('some error', 1)); 

    }

}
