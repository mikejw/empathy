<?php

class Attribute extends EntityDBMS
{
  public $id;
  public $name;
  
  public static $table = "attribute";

  public function loadIndexed()
  {
    $attr = array();
    //    $attr[0] = "None";
    $sql = "SELECT * FROM ".Attribute::$table;  
    $error = "Could not fetch attributes.";
    $result = $this->query($sql, $error);
    while($row = mysql_fetch_array($result))
    {
      $id = $row['id'];
      $attr[$id] = $row['name'];
    }
      
    return $attr;
  }

}
?>