<?php

declare(strict_types=1);

namespace ESuite;

use Empathy\MVC\Entity;

class FakeRelatedEntity extends Entity
{
    public $id;
    public $fake_id;
    public $name;

    public const TABLE = 'related';


}
