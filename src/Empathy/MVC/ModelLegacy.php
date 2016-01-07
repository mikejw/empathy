<?php

namespace Empathy\MVC;


/**
 * Empathy model class
 * @file            Empathy/MVC/Model.php
 * @description     
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
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
    public static function load($model, $id = NULL, $params = array(), $host = NULL)
    {
        $storage_object = $model;
        self::connectModel($storage_object, $host);
        $storage_object->init();        
    }
}
