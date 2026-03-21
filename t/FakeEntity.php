<?php

declare(strict_types=1);

namespace ESuite;

use Empathy\MVC\Entity;

class FakeEntity extends Entity
{
    public $id;
    public $name;
    public $age;
    public $stamp;
    public $nonempty;
    public $foo;

    public const TABLE = 'fake';


}
