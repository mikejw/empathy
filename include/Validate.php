<?php

class Validate
{  
  public $error = array();
  
  public function addError($message, $field)
  {
    if($field != '')
      {
	if(isset($this->error['field']))
	  {
	    die('Attempt to overwrite error field.');
	  }
	else
	  {
	    $this->error[$field] = $message;
	  }	  
      }
    else
      {
	array_push($this->error, $message);
      }
  }

  public function hasErrors()
  {
    return (sizeof($this->error) > 0);
  }

  public function getErrors()
  {
    return $this->error;
  }
}
?>