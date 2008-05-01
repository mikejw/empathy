<?php

class Colour extends EntityDBMS
{
  public $id;
  public $colour;
  
  public static $table = "colour";

  public function loadIndexed()
  {
    $colour = array();
    $colour[0] = "None";
    $sql = "SELECT * FROM ".Colour::$table;  
    $error = "Could not fetch colours.";
    $result = $this->query($sql, $error);
    $category = array();
    while($row = mysql_fetch_array($result))
    {
      $id = $row['id'];
      $colour[$id] = $row['colour'];
    }
      
    return $colour;
  }
  

}
?>