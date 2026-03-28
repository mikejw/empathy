<?php

declare(strict_types=1);

namespace Tests;

use Empathy\MVC\Util\Testing\EmpathyApp;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Project test case: wires Empathy helpers for Pest (and optional PHPUnit tests in ./tests).
 *
 * In Pest, use $this->empathy (e.g. $this->empathy->makeFakeBootstrap()) inside tests
 * when this class is bound via tests/Pest.php.
 */
#[\AllowDynamicProperties]
abstract class TestCase extends BaseTestCase
{
    protected EmpathyApp $empathy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->empathy = new EmpathyApp();
    }
}
