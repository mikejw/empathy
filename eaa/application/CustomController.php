<?php

namespace Empathy\Controller;
use Empathy\Controller as Controller;

/**
 * Empathy Custom Controller
 * @file			eaa/application/CustomController.php
 * @description		Site-wide controller customisation goes in this file.
 * @author			Mike Whiting
 * @license			LGPLv3
 *
 * (c) copyright Mike Whiting 
 * This source file is subject to the LGPLv3 License that is bundled
 * with this source code in the file licence.txt
 */
class CustomController extends Controller
{	

  /**
   * Calls to custom routines can go in here.
   *
   * @return void
   */
  public function __construct($boot)
  {
    parent::__construct($boot);  
  }
  

}
?>