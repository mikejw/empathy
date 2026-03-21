<?php

declare(strict_types=1);

namespace Empathy\MVC;

class ModelLegacy extends Model
{
    public static function load($model)
    {
        $storageObject = $model;
        self::connectModel($storageObject);
        $storageObject->init();
    }
}
