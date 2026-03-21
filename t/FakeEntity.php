<?php

declare(strict_types=1);

namespace ESuite;

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
