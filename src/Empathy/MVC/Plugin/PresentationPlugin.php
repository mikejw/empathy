<?php

declare(strict_types=1);

namespace Empathy\MVC\Plugin;

use Empathy\MVC\Bootstrap;
use Empathy\MVC\Plugin as Plugin;
use Empathy\MVC\PluginManager;

class PresentationPlugin extends Plugin
{
    private bool $global;

    public function __construct(PluginManager $manager, Bootstrap $bootstrap, mixed $config = null, bool $global = true)
    {
        parent::__construct($manager, $bootstrap, $config);
        $this->global = $global;
    }

    public function getGlobal(): bool
    {
        return $this->global;
    }
}
