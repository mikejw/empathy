<?php

declare(strict_types=1);

namespace Empathy\MVC\Util\Testing\Util;

use Empathy\MVC\Entity;

class FakeEntity extends Entity
{
    public int $id;

    public string $name;
    public int $age;
    public string $stamp;
    public int $nonempty;

    public string $foo;

    public const TABLE = 'fake';


}
