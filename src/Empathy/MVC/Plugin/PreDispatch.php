<?php

namespace Empathy\MVC\Plugin;

/**
 * Empathy PreDispatch interface
 * @file            Empathy/MVC/Plugin/PreDispatch.php
 * @description     
 * @author          Mike Whiting
 * @license         LGPLv3
 *
 * (c) copyright Mike Whiting
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
interface PreDispatch
{
    public function onPreDispatch();
}
