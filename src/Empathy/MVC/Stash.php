<?php

declare(strict_types=1);

namespace Empathy\MVC;

/**
 * Empathy Stash class
 * @file            Empathy/MVC/Stash.php
 * @description     Global key/value store.
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class Stash
{
    /** @var array<string, mixed> */
    private array $items = [];

    public function __construct()
    {
        $this->items = [];
    }

    public function get(string $key): mixed
    {
        if (!isset($this->items[$key])) {
            return null;
        }

        return $this->items[$key];
    }

    public function store(string $key, mixed $data): void
    {
        $this->items[$key] = $data;
    }
}
