<?php

declare(strict_types=1);

namespace Empathy\MVC;

/**
 * Boot options and plugin definitions derived from YAML for {@see Empathy}.
 *
 * @author Mike Whiting
 */
final readonly class BootSnapshot
{
    /**
     * @param array<string, mixed>       $bootOptions
     * @param list<array<string, mixed>> $plugins
     */
    public function __construct(
        public array $bootOptions,
        public array $plugins,
    ) {
    }
}
