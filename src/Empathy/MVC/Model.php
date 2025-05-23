<?php

namespace Empathy\MVC;

use Empathy\MVC\Config;

class Model
{
    private static $dbHandle = null;

    public static function connectModel($model, $host = null)
    {
        if (self::$dbHandle !== null && $host === null) {

            $model->setDBH(self::$dbHandle);

        } elseif ($host !== null) {

            $dbh = DBPool::getConnection($host);
            $model->setDBH($dbh);

        } elseif (self::$dbHandle == null && $host == null) {

            $handle = DBPool::getDefCX();
            $model->setDBH($handle);
            self::$dbHandle = $handle;
        }
    }

    public static function load($model, $id = null, $params = [], $host = null)
    {
        $reflect = new \ReflectionClass($model);
        $modelObject = $reflect->newInstanceArgs($params);

        if (!in_array('Empathy\MVC\Entity', class_parents($modelObject))) {
            throw new \Exception('Class is not Entity model: ' . $model);
        } else {
            $entity = new $model($params);
            self::connectModel($entity, $host);
            $entity->init();
            $entity->load($id);
            return $entity;
        }
    }

    public static function disconnect(array $models)
    {
        foreach ($models as $m) {
            $m->dbDisconnect();
        }
    }

    public static function getTable($model)
    {
        $entity = new $model();
        return $entity->getTable();
    }
}
