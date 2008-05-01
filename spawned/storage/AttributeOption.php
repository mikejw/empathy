<?php

class AttributeOption extends EntityDBMS
{
  public $id;
  public $attribute_id;
  public $option_val;
  
  public static $table = "attribute_option";

  public function build($attributes)
  {
    $attr_opt = array();
    foreach($attributes as $index => $value)
      {
	$option = array();
	$sql = "SELECT a.id, a.option_val FROM ".AttributeOption::$table." a WHERE attribute_id = $index";
	$error = "Could not fetch attribute options.";
	
	$result = $this->query($sql, $error);
	while($row = mysql_fetch_array($result))
	  {
	    $id = $row['id'];
	    $option[$id] = $row['option_val'];
	  }

	$attr_opt[$index]['name'] = $value;
	$attr_opt[$index]['options'] = $option;
      }
    return $attr_opt;
  }



  /*
  public function loadForProduct($product_id)
  {
    $selected_attr = array();
    $sql = "SELECT attribute_id FROM ".ProductAttribute::$table." WHERE product_id = $product_id";
    $error = "Could not get selected attributes.";
    $result = $this->query($sql, $error);

    $i = 0;
    while($row = mysql_fetch_array($result))
      {
	$selected_attr[$i] = $row['attribute_id'];
	$i++;
      }
    return $selected_attr;
  }


  public function updateForProduct($product_id, $attribute)
  {
    $sql = "DELETE FROM ".ProductAttribute::$table. " WHERE product_id = $product_id";
    $error = "Could not delete product attributes.";
    $this->query($sql, $error);

    foreach($attribute as $attribute_id)
      {
	$sql = "INSERT INTO ".ProductAttribute::$table." VALUES(NULL, $product_id, $attribute_id)";
	$error = "Could not insert product attribute.";
	$this->query($sql, $error);
      }
  }
  */

}
?>