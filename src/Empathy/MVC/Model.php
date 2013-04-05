<?php

namespace Empathy\MVC;

class Model
{
    protected static $db_handle = null;

    protected static function connectModel($model, $host)
    {
        // cached handle is not null
        // and new host is null
        // use cached
        if (self::$db_handle !== null && $host === null) {
            $model->setDBH(self::$db_handle);
        } elseif ($host !== null) {
            // use a new host
            $dbh = DBPool::getConnection($host);
            $model->setDBH($dbh);
        } elseif (self::$db_handle == null && $host == null) {
            // db_handle is null and host is null
            // (initiate default)
            $handle = DBPool::getDefCX();
            $model->setDBH($handle);
            self::$db_handle = $handle;
        }

    }

    public static function load($model, $host = null)
    {
        $class = '\Empathy\\MVC\\Model\\'.$model;

        // manually add entity class 
        // (for cases when not in 'system-mode' and this code lies outside
        // the reach of the composer autoload 
        $file = $model.'.php';
        require_once(DOC_ROOT.'/storage/'.$file);

        // prevent auto-connecting
        $storage_object = new $class(false);

        self::connectModel($storage_object, $host);

        return $storage_object;
    }


    public static function disconnect(array $models)
    {
        foreach($models as $m) {
            $m->dbDisconnect();
        }

    }

    public static function getTable($model)
    {
        $class = '\\Empathy\\MVC\\Model\\'.$model;

        return $class::TABLE;
    }
}
