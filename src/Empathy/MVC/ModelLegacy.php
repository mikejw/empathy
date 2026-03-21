<?php

declare(strict_types=1);

namespace Empathy\MVC;

class ModelLegacy extends Model
{
    public static function bootstrapEntity(Entity $model): void
    {
        self::connectModel($model);
        $model->init();
    }
}
