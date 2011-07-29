<?php

namespace Empathy;

class Validate
{  
  const TEXT =  1;
  const ALNUM = 2;
  const NUM = 3;
  const EMAIL = 4;
  const TEL = 5;
  const USERNAME = 6;
  const URL = 7;
 
  public $error = array();
  private $email_pattern;
  private $allowed_pattern_1;
  private $unix_username_pattern;
  private $twitter_style_username;


  public function __construct()
  {
    $this->email_pattern = '/^[^@\s<&>]+@([-a-z0-9]+\.)+[a-z]{2,}$/i';
    $this->allowed_pattern_1 = '/["\/-\s:,\']/';
    $this->unix_username_pattern = '/^[a-z][_a-zA-Z0-9-]{3,7}$/';
    $this->twitter_style_username = '/^\w{1,15}$/';

    // taken from http://bit.ly/AQFAn
    $this->url_pattern = '|https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?|';
  }

  public function valType($type, $field, $data, $optional)
  {
    $valid = true;
    if(!$optional || $data != '')
      {
	switch($type)
	  {
	  case self::TEXT:
	    if(!ctype_alnum(preg_replace($this->allowed_pattern_1, '', $data)))
	      {
		$this->addError('Invalid '.$field, $field);
		$valid = false;
	      }	
	    break;
	  case self::ALNUM:
	    if(!ctype_alnum($data))
	      {
		$this->addError('Invalid '.$field, $field);
		$valid = false;
	      }	
	    break;
	  case self::NUM:
	    if(!is_numeric($data)) // consider ctype_digit instead?
	      {
		$this->addError('Invalid '.$field, $field);		
		$valid = false;
	      }	    
	    break;
	  case self::EMAIL:
	    if(!preg_match($this->email_pattern, $data))
	      {
		$this->addError('Invalid '.$field, $field);
		$valid = false;
	      }
	    break;
	  case self::TEL:
	    if(!ctype_digit(preg_replace('/\s/', '', $data)))
	      {
		$this->addError('Invalid '.$field, $field);
		$valid = false;
	      }
	    break;
	  case self::USERNAME:
	    //if(!preg_match($this->unix_username_pattern, $data))
	    if(!preg_match($this->twitter_style_username, $data))
	      {
		$this->addError('Invalid '.$field, $field);
		$valid = false;
	      }
	    break;
	  case self::URL:
	    if(!preg_match($this->url_pattern, $data))
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