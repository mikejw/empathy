<?php

declare(strict_types=1);

namespace Tests;

use Empathy\MVC\Bootstrap;
use Empathy\MVC\Controller;
use Empathy\MVC\DBC;
use Empathy\MVC\Util\Testing\EmpathyApp;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Project test case: wires Empathy helpers for Pest (and optional PHPUnit tests in ./tests).
 *
 * Properties are public so assignments in Pest beforeEach are obvious to static analysis
 * when combined with universalObjectCratesClasses for PHPUnit\Framework\TestCase.
 */
abstract class TestCase extends BaseTestCase
{
    public EmpathyApp $empathy;

    public ?Bootstrap $bootstrap = null;

    public ?Controller $controller = null;

    public ?DBC $dbc = null;

    /** @var Bootstrap|null Used by URITest */
    public ?Bootstrap $boot = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->empathy = new EmpathyApp();
    }
}
