<?php

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

    const TABLE = 'fake';


}
