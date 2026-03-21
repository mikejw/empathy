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
    private $items;

    public function __construct()
    {
        $this->items = [];
    }

    public function get($key)
    {
        if (!isset($this->items[$key])) {
            return null;
        } else {
            return $this->items[$key];
        }
    }

    public function store($key, $data)
    {
        $this->items[$key] = $data;
    }
}
