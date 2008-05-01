<?php

class CategoryItem extends EntityDBMS
{
  public $id;
  public $name;
  
  public static $table = "category";

  public function loadIndexed()
  {
    $sql = "SELECT * FROM ".CategoryItem::$table;
    $error = "Could not fetch categories.";
    $result = $this->query($sql, $error);
    $category = array();
    while($row = mysql_fetch_array($result))
    {
      $id = $row['id'];
      $category[$id] = $row['name'];
    }
      
    return $category;
  }
  

}
?>