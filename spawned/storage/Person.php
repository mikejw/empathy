<?php

class Person extends EntityDBMS
{
  public $id;
  public $user_id;
  public $first_name;
  public $last_name;
  public $address1;
  public $address2;
  public $city;
  public $county;
  public $post_code;
  
  public static $table = "person";

  /*
  public function load($id)
  {
    $sql = "SELECT * FROM ".Person::$table
      ." WHERE id = $id";
    $error = "Could not load person.";
    $result = Object::query($sql, $error);
    if(1 == mysql_num_rows($result))
    {
      $row = mysql_fetch_array($result);
      {
	foreach($row as $index => $value)
	{
	  $this->$index = $value;	 
	}
      }
    }
  }
  */

}
?>