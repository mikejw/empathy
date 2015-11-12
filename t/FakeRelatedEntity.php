<?php

namespace ESuite;

use Empathy\MVC\Entity;

class FakeRelatedEntity extends Entity
{
    public $id;
    public $fake_id;
    public $name;

    const TABLE = 'related';


}
