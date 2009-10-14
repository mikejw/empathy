<?php
define('VAL_TYPE_TEXT', 1);
define('VAL_TYPE_ALNUM', 2);
define('VAL_TYPE_NUM', 3);
define('VAL_TYPE_EMAIL', 4);
define('VAL_TYPE_TEL', 5);

class Validate
{  
  public $error = array();
  private $email_pattern;
  private $allowed_pattern_1;
  private $unix_username_pattern;


  public function __construct()
  {
    $this->email_pattern = '/^[^@\s<&>]+@([-a-z0-9]+\.)+[a-z]{2,}$/i';
    $this->allowed_pattern_1 = '/[\/-\s]/';
    $this->unix_username_pattern = '/^[a-z][_a-zA-Z0-9-]{3,7}$/';
  }

  public function valType($type, $field, $data, $optional)
  {
    $valid = true;
    if(!$optional || $data != '')
      {
	switch($type)
	  {
	  case VAL_TYPE_TEXT:
	    if(!ctype_alnum(preg_replace($this->allowed_pattern_1, '', $data)))
	      {
		$this->addError('Invalid '.$field, $field);
		$valid = false;
	      }	
	    break;
	  case VAL_TYPE_ALNUM:
	    if(!ctype_alnum($data))
	      {
		$this->addError('Invalid '.$field, $field);
		$valid = false;
	      }	
	    break;
	  case VAL_TYPE_NUM:
	    if(!is_numeric($data))
	      {
		$this->addError('Invalid '.$field, $field);		
		$valid = false;
	      }	    
	    break;
	  case VAL_TYPE_EMAIL:
	    if(!preg_match($this->email_pattern, $data))
	      {
		$this->addError('Inalid '.$field, $field);
		$valid = false;
	      }
	    break;
	  case VAL_TYPE_TEL:
	    if(!ctype_digit(preg_replace('/\s/', '', $data)))
	      {
		$this->addError('Invalid '.$field, $field);
		$valid = false;
	      }
	    break;
	  default:
	    die('No valid validation type specified.');
	    break;
	  }
      }
    return $valid;
  }
  
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