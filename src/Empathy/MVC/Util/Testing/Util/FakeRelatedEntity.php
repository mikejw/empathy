<?php

declare(strict_types=1);

namespace Empathy\MVC\Util\Testing\Util;

use Empathy\MVC\Entity;

class FakeRelatedEntity extends Entity
{
    public int $id;
    public mixed $fake_id;
    public mixed $name;

    public const TABLE = 'related';
}
