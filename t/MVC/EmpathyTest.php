<?php

namespace ESuite\MVC;

use Empathy\MVC\Config;
use Empathy\MVC\DBC;
use ESuite\ESuiteTest;


// @totdo: figure out proper use of expectOutput
// can we set it multiple times.. or match over set of output
// is it supposed to default to one test case?

class EmpathyTest extends ESuiteTest
{
    private $config_dir;
    private $mvc;

    protected function setUp()
    {
        $this->config_dir = realpath(dirname(realpath(__FILE__)).'/../../eaa/');
    }

    private function createMVC($persistent=false)
    {   
        $this->expectOutputRegex('/(Setting header)/');

        $container = \Empathy\MVC\DI::init($this->config_dir, $persistent);
        $empathy = $container->get('Empathy');
        $empathy->init();
        $this->mvc = $empathy;
    }


    private function changeEnv($env)
    {
        $boot_options = Config::get('BOOT_OPTIONS');
        $boot_options['environment'] = $env;
        Config::store('BOOT_OPTIONS', $boot_options);
        $this->mvc->reloadBootOptions();
    }
    
    private function changeDebug($debug)
    {
        $boot_options = Config::get('BOOT_OPTIONS');
        $boot_options['debug_mode'] = $debug;
        Config::store('BOOT_OPTIONS', $boot_options);
        $this->mvc->reloadBootOptions();   
    }
    

    public function testNew()
    {    
        $this->setExpectedException(
            'Empathy\MVC\RequestException', 'Not found'
        );
        $this->mvc = $this->createMVC();
    }


    public function testErrors()
    {
        //$this->markTestSkipped();

        $this->createMVC(true);
        $errors = $this->mvc->getErrors();
        $this->assertEmpty($errors);
        $this->assertFalse($this->mvc->hasErrors());
        $this->assertEmpty($this->mvc->errorsToString());

        $this->expectOutputRegex('/Fatal error/');
        $this->mvc->errorHandler(E_ERROR, 'dummy error', 'someFile.php', 1);

        $this->mvc->errorHandler(E_USER_WARNING, 'dummy error', 'someFile.php', 1);
        $this->assertRegExp('/Warning/', $this->mvc->errorsToString());

        $this->mvc->errorHandler(E_NOTICE, 'dummy error', 'someFile.php', 1);
        $this->assertRegExp('/Notice/', $this->mvc->errorsToString());

        $this->mvc->errorHandler(E_STRICT, 'dummy error', 'someFile.php', 1);
        $this->assertRegExp('/Strict/', $this->mvc->errorsToString());

        $this->mvc->errorHandler(0, 'dummy error', 'someFile.php', 1);
        $this->assertRegExp('/Unknown/', $this->mvc->errorsToString());
    }

    public function testExceptions()
    {
        //$this->markTestSkipped();

        // what happens when there is just an error
        $this->createMVC(true);
        $this->changeDebug(true);
        $this->mvc->errorHandler(E_NOTICE, 'dummy error', 'someFile.php', 1);
        $this->expectOutputRegex('/dummy error/');
        $this->mvc->exceptionHandler(new \Exception(''));


        $this->createMVC(true);
        $this->expectOutputRegex('/Bad request/');
        $this->mvc->exceptionHandler(new \Empathy\MVC\SafeException('some error'));



        $this->createMVC(true);
        $this->changeEnv('dev');
        $this->expectOutputRegex('/die: Safe exception: some error/');
        $this->mvc->exceptionHandler(new \Empathy\MVC\SafeException('some error'));


        $this->createMVC(true);
        $this->expectOutputRegex('/<h2>some error<\/h2>/');
        $this->mvc->exceptionHandler(new \Exception('some error'));

        // code = 0 => 404
        $this->createMVC(true);
        $this->expectOutputRegex('/Not found/');    
        $this->mvc->exceptionHandler(new \Empathy\MVC\RequestException('some error'));

        // code = 1 => 500
        $this->createMVC(true);
        $this->expectOutputRegex('/Bad request/');    
        $this->mvc->exceptionHandler(new \Empathy\MVC\RequestException('some error', 1)); 

    }

}
