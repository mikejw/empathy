<?php

declare(strict_types=1);

namespace Empathy\MVC;

/**
 * Empathy Plugin class
 * @file            Empathy/MVC/Plugin.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class Plugin
{
    protected Bootstrap $bootstrap;
    protected mixed $config = null;
    protected PluginManager $manager;

    public function __construct(PluginManager $manager, Bootstrap $bootstrap, mixed $config = null)
    {
        $this->bootstrap = $bootstrap;
        $this->manager = $manager;
        if ($config !== null) {
            $this->assignConfig($config);
        }
    }

    public function assignConfig(mixed $config): void
    {
        $this->config = is_string($config) ? json_decode($config, true) : $config;
    }

    public function getConfig(): mixed
    {
        return $this->config;
    }
}
