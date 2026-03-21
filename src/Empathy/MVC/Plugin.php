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
    protected mixed $config = null;

    public function __construct(protected PluginManager $manager, protected Bootstrap $bootstrap, ?string $config = null)
    {
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
