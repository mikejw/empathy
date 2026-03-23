<?php

declare(strict_types=1);

namespace Empathy\MVC;

use Nelmio\Alice\PersisterInterface;

/**
 * Empathy EntityManager
 * @package         Empathy
 * @file            Empathy/MVC/EntityManager.php
 * @description     Simple "ORM style" model objects for Empathy.
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * Do things with entities in a doctrine style ObjectManager fashion
 * for use with fixture generation with Alice
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class EntityManager implements PersisterInterface
{
    /**
     * @param list<object> $objects
     */
    public function persist(array $objects): void
    {
        foreach ($objects as $object) {
            if (!$object instanceof Entity) {
                throw new \Exception('persist() only accepts Entity instances');
            }
            $object->init();
            foreach ($object->getProperties() as $property) {
                $value = $object->$property;
                if ($value instanceof Entity) {
                    $object->$property = $value->id;
                }
            }

            Model::connectModel($object);
            $object->setPrimaryKeyAfterInsert($object->insert());
        }
    }


    public function find(mixed $class, mixed $id): ?object
    {
        if (!class_exists($class)) {
            throw new \Exception('Entity class does not exist.');
        }
        $object = new $class();
        if (!$object instanceof Entity) {
            throw new \Exception('Class is not an Entity: '.$class);
        }
        $object->init();
        Model::connectModel($object);
        $object->load($id);
        return $object;
    }


    // probably not needed
    public function flush(): void
    {
        throw new \Exception('not yet implemented.');
    }
}
