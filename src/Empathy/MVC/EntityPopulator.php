<?php

namespace Empathy\MVC;

use Nelmio\Alice\Fixtures\Fixture;
use Nelmio\Alice\Instances\Populator\Methods\MethodInterface;

/**
 * Empathy EntityPopulator
 * @package         Empathy
 * @file            Empathy/MVC/EntityPopulator.php
 * @description     Populate entities with alice fixtures
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * Do things with entities in a doctrine style ObjectManager fashion
 * for use with fixture generation with Alice
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class EntityPopulator implements MethodInterface
{
    

    public function canSet(Fixture $fixture, $object, $property, $value)
    {
        return true;
    }


    public function set(Fixture $fixture, $object, $property, $value)
    {
        $object->$property = $value;
    }
}
