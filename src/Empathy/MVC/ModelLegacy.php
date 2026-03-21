<?php

declare(strict_types=1);

namespace Empathy\MVC;

class ModelLegacy extends Model
{
    public static function load($model, $id = null, $params = [], $host = null): void
    {
        self::connectModel($model);
        $model->init();
    }
}
