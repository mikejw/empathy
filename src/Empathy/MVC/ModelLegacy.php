<?php

namespace Empathy\MVC;

/**
 * Empathy model class
 * @file            Empathy/MVC/Model.php
 * @description
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting

 * with this source code in the file licence.txt
 *
 * Ensure legacy model classes (that are instantiated directly)
 * are still initialised properly and connected to database as if they
 * have been loaded through the Model class.
 *
 * Usage: Entity requires the following constructor:
 * public function __construct()
 * {
 *     ModelLegacy::load($this);
 * }
 *
 */
class ModelLegacy extends Model
{
    public static function load($model, $id = null, $params = array(), $host = null)
    {
        $storage_object = $model;
        self::connectModel($storage_object, $host);
        $storage_object->init();
    }
}
