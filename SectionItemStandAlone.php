<?php

namespace Empathy;

class SectionItemStandAlone
{
  public $id;
  public $module;
  public $type;
  public $parent_id;
  public $position;
  public $label;
  public $friendly_url;
  public $template;
  public $hidden;
  public $owns_inline;
  public $link;
  private $result;

  public static $table = "section_item";

  public function __construct()
  {
    $this->dbConnect();
  }

  private function dbConnect()
  {
    try
      {
	$this->dbh = new \PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME,
			   DB_USER, DB_PASS);
      }
    catch (PDOException $e)
      {
	print "Error!: " . $e->getMessage() . "<br/>";
	die();
      }
  }

  private function query($sql, $error)
  {
    $result = NULL;
    
    if(is_object($this->result))
      {
	$this->result->closeCursor();
      }
    if(($result = $this->dbh->query($sql)) == false)  
      {
	$errors = $this->dbh->errorInfo();
	$this->controller->error("[".htmlentities($sql)
				 ."]<br /><strong>MySQL</strong>: ($error): "
				 .htmlentities($errors[2]), 0);       
      }
    else
      {
	$this->result = $result;
      }      
    return $result;
  }

  public function getURIData()
  {
    $sql = "SELECT id, label, friendly_url FROM ".SectionItemStandAlone::$table;
    $error = "Could not get URI data.";
    $result = $this->query($sql, $error);
    $i = 0;
    foreach($result as $row)
    {
      $uri_data[$i] = $row;
      $i++;
    }
    return $uri_data;
  }

  public function getItem($id)
  {
    $sql = "SELECT * FROM ".SectionItemStandAlone::$table." WHERE id = $id";
    $error = "Could not load record.";
    $result = $this->query($sql, $error);
    if(1 == $result->rowCount())
    {
      $row = $result->fetch();
      foreach($row as $index => $value)
      {
	$this->$index = $value;
      }
      $this->url_name = str_replace(" ", "", $this->label);
      $this->url_name = strtolower($this->url_name);
    }
    else
    {
      //echo "Whoops!";
    }
  }

  
  
}
?>