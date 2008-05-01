<?php

class Size extends EntityDBMS
{
  public $id;
  public $size;
  
  public static $table = "size";

  public function loadIndexed()
  {
    $size = array();
    $size[0] = "None";
    $sql = "SELECT * FROM ".Size::$table;  
    $error = "Could not fetch sizes.";
    $result = $this->query($sql, $error);
    $category = array();
    while($row = mysql_fetch_array($result))
    {
      $id = $row['id'];
      $size[$id] = $row['size'];
    }
      
    return $size;
  }
  

}
?>