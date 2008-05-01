<?php

class ProductAttribute extends EntityDBMS
{
  public $id;
  public $product_id;
  public $attribute_id;
  
  public static $table = "product_attribute";


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


  public function loadForProductIndexed($product_id)
  {
    require(DOC_ROOT."/storage/Attribute.php");
    $selected_attr = array();
    $sql = "SELECT attribute_id, name FROM ".ProductAttribute::$table." p, ".Attribute::$table." a WHERE product_id = $product_id"
      ." AND p.attribute_id = a.id ORDER BY a.id";
    $error = "Could not get selected attributes.";
    $result = $this->query($sql, $error);

    $i = 0;
    while($row = mysql_fetch_array($result))
      {
	$attribute_id = $row['attribute_id'];
	$selected_attr[$attribute_id] = $row['name'];
	$i++;
      }
    return $selected_attr;
  }


  public function insertForProduct($product_id, $attribute)
  {
    foreach($attribute as $attribute_id)
      {
	$sql = "INSERT INTO ".ProductAttribute::$table." VALUES(NULL, $product_id, $attribute_id)";
	$error = "Could not insert product attribute.";
	$this->query($sql, $error);
      }  
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
  

}
?>