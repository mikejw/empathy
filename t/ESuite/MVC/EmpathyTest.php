<?php

declare(strict_types=1);

namespace ESuite\MVC;

use Empathy\MVC\Config;
use Empathy\MVC\Util\Testing\ESuiteTestCase;

// @totdo: figure out proper use of expectOutput
// can we set it multiple times.. or match over set of output
// is it supposed to default to one test case?

class EmpathyTest extends ESuiteTestCase
{
    private mixed $mvc = null;

    protected function setUp(): void
    {
        //
    }

    private function createMVC(): void
    {
        $bootstrap = $this->makeFakeBootstrap();
        $this->mvc = $bootstrap->getMVC();
    }


    private function changeEnv(string $env): void
    {
        $boot_options = Config::get('BOOT_OPTIONS');
        $boot_options['environment'] = $env;
        Config::store('BOOT_OPTIONS', $boot_options);
        $this->mvc->reloadBootOptions();
    }

    private function changeDebug(bool $debug): void
    {
        $boot_options = Config::get('BOOT_OPTIONS');
        $boot_options['debug_mode'] = $debug;
        Config::store('BOOT_OPTIONS', $boot_options);
        $this->mvc->reloadBootOptions();
    }

    public function testErrors(): void
    {
        $this->createMVC();
        $errors = $this->mvc->getErrors();

        $this->assertEmpty($errors);
        $this->assertFalse($this->mvc->hasErrors());
        $this->assertEmpty($this->mvc->errorsToString());

        $this->expectOutputRegex('/Fatal error/');
        $this->mvc->errorHandler(E_ERROR, 'dummy error', 'someFile.php', 1);

        $this->mvc->errorHandler(E_USER_WARNING, 'dummy error', 'someFile.php', 1);
        $this->assertMatchesRegularExpression('/Warning/', $this->mvc->errorsToString());

        $this->mvc->errorHandler(E_NOTICE, 'dummy error', 'someFile.php', 1);
        $this->assertMatchesRegularExpression('/Notice/', $this->mvc->errorsToString());

        $this->mvc->errorHandler(0, 'dummy error', 'someFile.php', 1);
        $this->assertMatchesRegularExpression('/Error/', $this->mvc->errorsToString());
    }

    public function testExceptions(): void
    {
        // what happens when there is just an error
        $this->createMVC();
        $this->changeDebug(true);
        $this->mvc->errorHandler(E_NOTICE, 'dummy error', 'someFile.php', 1);
        $this->expectOutputRegex('/dummy error/');
        $this->mvc->exceptionHandler(new \Exception(''));

        $this->createMVC();
        $this->expectOutputRegex('/Bad request/');
        $this->mvc->exceptionHandler(new \Empathy\MVC\SafeException('some error'));

        $this->createMVC();
        $this->changeEnv('dev');
        $this->expectOutputRegex('/die: Safe exception: some error/');
        $this->mvc->exceptionHandler(new \Empathy\MVC\SafeException('some error'));


        $this->createMVC();
        $this->expectOutputRegex('/<h2>some error<\/h2>/');
        $this->mvc->exceptionHandler(new \Exception('some error'));

        // code = 0 => 404
        $this->createMVC();
        $this->expectOutputRegex('/Not found/');
        $this->mvc->exceptionHandler(new \Empathy\MVC\RequestException('some error'));

        // code = 1 => 500
        $this->createMVC();
        $this->expectOutputRegex('/Bad request/');
        $this->mvc->exceptionHandler(new \Empathy\MVC\RequestException('some error', 1));
    }
}
