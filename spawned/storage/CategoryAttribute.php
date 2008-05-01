<?php

class CategoryAttribute extends EntityDBMS
{
  public $id;
  public $category_id;
  public $attribute_id;
  
  public static $table = "category_attribute";

  public function loadAll()
  {
    $sql = "SELECT * FROM ".CategoryAttribute::$table;
    $error = "Could not load product attributes.";
    $result = $this->query($sql, $error);
    
  }

  public function loadForCategory($cat_id)
  {
    $sql = "SELECT attribute_id FROM ".CategoryAttribute::$table
      ." WHERE category_id = $cat_id";
    $error = "Could not find default category attributes.";
    $result = $this->query($sql, $error);

    $i = 0;
    $attr = array();
    while($row = mysql_fetch_array($result))
      {
	$attr[$i] = $row['attribute_id'];
	$i++;
      }

    return $attr;
  }

  public function updateForCategory($category_id, $attribute)
  {
    $sql = "DELETE FROM ".CategoryAttribute::$table. " WHERE category_id = $category_id";
    $error = "Could not delete default category attributes.";
    $this->query($sql, $error);

    foreach($attribute as $attribute_id)
      {
	$sql = "INSERT INTO ".CategoryAttribute::$table." VALUES(NULL, $category_id, $attribute_id)";
	$error = "Could not insert product attribute.";
	$this->query($sql, $error);
      }
  }

  

}
?>