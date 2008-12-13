<?php

class Validate
{  
  public $errors = array();
  

  public function addError($message)
  {
    array_push($this->errors, $message);
  }

  public function hasErrors()
  {
    return (sizeof($this->errors) > 0);
  }

}
?>