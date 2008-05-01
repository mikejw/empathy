<?php
// site wide controller costomisation goes in this file

require('empathy/include/Controller.php');

class CustomController extends Controller
{
  
  public function __construct($error, $internalController, $secondary)
  {
    parent::__construct($error, $internalController, $secondary);   
    // put custom routienes here
  }
  
  
  // custom functions can go here
  
  
  
}
?>