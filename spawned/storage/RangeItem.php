<?php

class RangeItem extends EntityDBMS
{
  public $id;
  public $name;
  public $date_avail;
  public $date_exp;
  public $description;

  public static $table = "range";



  public function remove()
  {      
    $sql = "DELETE FROM ".RangeItem::$table. " WHERE id = $this->id";    
    $error = "Could not delete range.";
    $this->query($sql, $error);
  }

  /*
  public function load($table)
  {
    $sql = "SELECT *, UNIX_TIMESTAMP(date_avail) AS date_avail,"
      ."UNIX_TIMESTAMP(date_exp) AS date_exp FROM $table WHERE id = $this->id";
    $error = "Could not load record from $table.";
    //    $result = self::query($sql, $error);

    $result = $this->query($sql, $error);
    $row = mysql_fetch_array($result);
    foreach($row as $index => $value)
    {
      $this->$index = $value;
    }
  }
  */



  public function loadAll()
  {
    $range = array();
    $sql = "SELECT * FROM ".RangeItem::$table;
    $error = "Could not load all product ranges.";
    $result = $this->query($sql, $error);

    $i = 0;
    while($row = mysql_fetch_array($result))
      {
	$range[$i] = $row;
	$i++;
      }

    return $range;
  }



  public function loadAllIndexed()
  {
    $range = array();
    $sql = "SELECT * FROM ".RangeItem::$table;
    $error = "Could not load all product ranges.";
    $result = $this->query($sql, $error);

    while($row = mysql_fetch_array($result))
      {
	$id = $row['id'];
	$range[$id] = $row['name'];
      }

    return $range;
  }



  public function create()
  {
    $sql = "INSERT INTO ".RangeItem::$table." VALUES("
      ."NULL, 'Untitled', NULL, NULL, NULL)";
    $error = "Could not create new range.";
    $result = $this->query($sql, $error);
  }
  
}
?>