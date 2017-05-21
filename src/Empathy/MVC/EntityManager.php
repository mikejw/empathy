<?php

namespace Empathy\MVC;

use Nelmio\Alice\PersisterInterface;

/**
 * Empathy EntityManager
 * @package         Empathy
 * @file            Empathy/MVC/EntityManager.php
 * @description     Simple "ORM style" model objects for Empathy.
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * Do things with entities in a doctrine style ObjectManager fashion
 * for use with fixture generation with Alice
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class EntityManager implements PersisterInterface
{
    
    
    public function persist(array $objects)
    {
        foreach ($objects as $object) {
            $object->init();
            foreach ($object->getProperties() as $property) {
                if (is_object($object->$property)) {
                    $object->$property = $object->$property->id;
                }
            }

            Model::connectModel($object);
            $object->id = $object->insert($object::TABLE, true, array(''), Entity::SANITIZE_NO_POST);
        }
    }


    public function find($class, $id)
    {
        if (!class_exists($class)) {
            throw new \Exception('Entity class does not exist.');
        }
        $object = new $class;
        $object->init();
        Model::connectModel($object);

        $object->id = $id;
        $object->load();
        return $object;
    }


    // probably not needed
    public function flush()
    {
        throw new \Exception('not yet implemented.');
    }
}
