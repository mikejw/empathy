<?php

declare(strict_types=1);

namespace Empathy\MVC;

use PDO;

class Model
{
    private static ?PDO $dbHandle = null;

    public static function connectModel(Entity $model, ?string $host = null): void
    {
        if (self::$dbHandle instanceof \PDO && $host === null) {

            $model->setDBH(self::$dbHandle);

        } elseif ($host !== null) {

            $dbh = DBPool::getConnection($host);
            $model->setDBH($dbh);

        } else {

            $handle = DBPool::getDefCX();
            $model->setDBH($handle);
            self::$dbHandle = $handle;
        }
    }

    /**
     * @template T of Entity
     *
     * @param class-string<T> $model
     * @param list<mixed>     $params
     *
     * @return T
     */
    public static function load(string $model, mixed $id = null, array $params = [], ?string $host = null): Entity
    {
        $reflect = new \ReflectionClass($model);
        $entity = $reflect->newInstanceArgs($params);

        if (!in_array(\Empathy\MVC\Entity::class, class_parents($entity), true)) {
            throw new \Exception('Class is not Entity model: ' . $model);
        }

        self::connectModel($entity, $host);
        $entity->init();
        $entity->load($id);

        return $entity;
    }

    /**
     * @param list<Entity> $models
     */
    public static function disconnect(array $models): void
    {
        foreach ($models as $m) {
            $m->dbDisconnect();
        }
    }

    /**
     * @param class-string<Entity> $model
     */
    public static function getTable(string $model): string
    {
        $entity = new $model();
        return $entity->getTable();
    }
}
