<?php

class ProductRange extends EntityDBMS
{
  public $id;
  public $product_id;
  public $range_id;


  public static $table = "product_range";



  public function updateForProduct($product_id, $range)
  {
    $sql = "DELETE FROM ".ProductRange::$table. " WHERE product_id = $product_id";
    $error = "Could not delete product ranges.";
    $this->query($sql, $error);

    foreach($range as $range_id)
      {
	$sql = "INSERT INTO ".ProductRange::$table." VALUES(NULL, $product_id, $range_id)";
	$error = "Could not insert product range.";
	$this->query($sql, $error);
      }
  }




  public function makeUnavailable($product_id)
  {
    $sql = "INSERT INTO ".ProductRange::$table." VALUES("
      ."NULL, $product_id, 1)";
    $error = "Could not insert produt into 'unavilable' range.";
    $this->query($sql, $error);

  }

  public function loadForProduct($product_id)
  {
    $sql = "SELECT * FROM ".ProductRange::$table
      ." WHERE product_id = $product_id";     
    $error = "Could not load product ranges.";
    $result = $this->query($sql, $error);
    
    $range = array();
    $i = 0;
    while($row = mysql_fetch_array($result))
      {
	$range[$i] = $row['range_id'];
	$i++;
      }
    return $range;
  }
  
}
?>