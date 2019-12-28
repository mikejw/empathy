<?php

namespace Empathy\MVC;

use Empathy\MVC\Config;

/**
 * Empathy model class
 * @file            Empathy/MVC/Model.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 */
class Model
{
    protected static $db_handle = null;

    public static function connectModel($model, $host = null)
    {
        // cached handle is not NULL
        // and new host is NULL
        // use cached
        if (self::$db_handle !== null && $host === null) {
            $model->setDBH(self::$db_handle);
        } elseif ($host !== null) {
            // use a new host
            $dbh = DBPool::getConnection($host);
            $model->setDBH($dbh);
        } elseif (self::$db_handle == null && $host == null) {
            // db_handle is NULL and host is NULL
            // (initiate default)
            $handle = DBPool::getDefCX();
            $model->setDBH($handle);
            self::$db_handle = $handle;
        }
    }

    public static function load($model, $id = null, $params = array(), $host = null)
    {
        if (class_exists($model)) {
            $storage_object = new $model($params);
        } else {
            $class = '\Empathy\\MVC\\Model\\'.$model;
            // manually add entity class
            // (for cases when not in 'system-mode' and this code lies outside
            // the reach of the composer autoload
            $file = $model.'.php';
            require_once(Config::get('DOC_ROOT').'/storage/'.$file);

            $reflect  = new \ReflectionClass($class);
            $storage_object = $reflect->newInstanceArgs($params);
        }

        // todo: if id is numeric load record!

        if (in_array('Empathy\MVC\Entity', class_parents($storage_object))) {
            self::connectModel($storage_object, $host);
            $storage_object->init();
        }

        return $storage_object;
    }


    public static function disconnect(array $models)
    {
        foreach ($models as $m) {
            $m->dbDisconnect();
        }
    }

    public static function getTable($model)
    {
        $class = '\\Empathy\\MVC\\Model\\'.$model;
        return $class::TABLE;
    }
}
