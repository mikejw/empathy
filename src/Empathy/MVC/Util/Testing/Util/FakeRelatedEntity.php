<?php

declare(strict_types=1);

namespace Empathy\MVC\Util\Testing\Util;

use Empathy\MVC\Entity;

class FakeRelatedEntity extends Entity
{
    public $id;
    public $fake_id;
    public $name;

    public const TABLE = 'related';


}
