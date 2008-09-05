<?php
  // Copyright 2008 Mike Whiting (mail@mikejw.co.uk).
  // This file is part of the Empathy MVC framework.

  // Empathy is free software: you can redistribute it and/or modify
  // it under the terms of the GNU Lesser General Public License as published by
  // the Free Software Foundation, either version 3 of the License, or
  // (at your option) any later version.

  // Empathy is distributed in the hope that it will be useful,
  // but WITHOUT ANY WARRANTY; without even the implied warranty of
  // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  // GNU Lesser General Public License for more details.

  // You should have received a copy of the GNU Lesser General Public License
  // along with Empathy.  If not, see <http://www.gnu.org/licenses/>.

abstract class  Entity
{
  protected $controller;
  
  /* dodgey code */
  
  private $table = "";
  
  public static function appendPrefix($table)
  {
    return TBL_PREFIX.$table;
  }
  /* end dodgey code */
  
  
  public function __construct(&$controller)
  // needs optimisation!?
  {
    $this->controller = $controller;
    if($this->controller->connected == false)
      {
	$this->dbConnect();
      }
  }  
  
  abstract public function dbConnect();
  abstract public function query($sql, $error);
  
  public function load($table)
  {
    $table = $this->appendPrefix($table);
    $sql = "SELECT * FROM $table WHERE id = $this->id";
    $error = "Could not load record from $table.";
    //    $result = self::query($sql, $error);
    
    $result = $this->query($sql, $error);
    if(mysql_num_rows($result) > 0)
      {
	$row = mysql_fetch_array($result);
	foreach($row as $index => $value)
	  {
	    $this->$index = $value;
	  }
      }
  }
  
  public function sanitize()
  {
    $vars = array_keys(get_class_vars(get_class($this)));
    foreach($vars as $property)
      {
	if(isset($_POST[$property]) && !(is_numeric($property))) // && ($this->$property == $_POST[$property])) -> breaking where single quotes and break tags
	  {
	    $this->$property = mysql_escape_string($this->$property);
	  }
      }
  }

  
  public function sanitizeNoPost()
  {
    $vars = array_keys(get_class_vars(get_class($this)));
    foreach($vars as $property)
      {
	if(!(is_numeric($property)))
	  {
	    $this->$property = mysql_escape_string($this->$property);
	  }
      }
  }

  
  public function save($table, $format, $sanitize)
  {
    $table = $this->appendPrefix($table);
    $this->toXHTML($format);
    $this->stripMSWordChars();
    if($sanitize == 1)
      {
	$this->sanitize();
      }
    elseif($sanitize == 2)
      {
	$this->sanitizeNoPost();
      }

    $sql = "UPDATE $table SET ";
    $vars = array_keys(get_class_vars(get_class($this)));
    $i = 0;
    foreach($vars as $property)
      {
	if($property != "id" && $property != "table")
	  {
	    $sql .= "$property = ";
	    if(is_numeric($this->$property))
	      {
		$sql .= $this->$property;
	      }
	    else
	      {
		$sql .= "'".$this->$property."'";
	      }
	    
	    if(($i+2) != sizeof($vars))
	      {
		$sql .= ", ";
	      }
	  }
	$i++;
      }
    $sql .= " WHERE id = $this->id";
    $error = "Could not update table '$table'";
    $this->query($sql, $error);
  }  
  
  public function insert($table, $id, $format, $sanitize)
  {
    $table = $this->appendPrefix($table);
    $this->toXHTML($format);
    $this->stripMSWordChars();
    if($sanitize == 1)
      {
	$this->sanitize();
      }
    elseif($sanitize == 2)
      {
	$this->sanitizeNoPost();
      }

    $sql = 'INSERT INTO '.$table.' VALUES(';
    if($id)
      {
	$sql .= 'NULL, ';
      }
    $vars = array_keys(get_class_vars(get_class($this)));
    $i = 0;
    $id = 0;
    
    foreach($vars as $property)
      {
	if($property != "id" && $property != "table")
	  {
	    if(is_numeric($this->$property))
	      {
		$sql .= $this->$property;
	      }
	    else
	      {
		$sql .= "'".$this->$property."'";
	      }
	    
	    if(($i+2) != sizeof($vars))
	      {
		$sql .= ", ";
	      }
	  }
	$i++;
      }
    $sql .= ")";
    $error = "Could not insert to table '$table'";
    $this->query($sql, $error);
    return mysql_insert_id();
  }
  
  public function getAll($table)
  {
    $all = array();
    $sql = 'SELECT * FROM '.$table;
    $error = 'Could not get all rows from '.$table;
    $result = $this->query($sql, $error);
    
    $i = 0;
    while($row = mysql_fetch_array($result))
      {
	$all[$i] = $row;
	$i++;
      }
    return $all;
  }

  public function getAllCustom($table, $sql_string)
  {
    $table = $this->appendPrefix($table);
    $all = array();
    $sql = 'SELECT * FROM '.$table.' '.$sql_string;
    $error = 'Could not get all rows from '.$table;
    $result = $this->query($sql, $error);
    
    $i = 0;
    while($row = mysql_fetch_array($result))
      {
	$all[$i] = $row;
	$i++;
      }
    return $all;    
  }


  public function getPaginatePages($table, $sql_string, $page, $per_page)
  {
    $nav = array();
    $sql = 'SELECT * FROM '.$table.' '.$sql_string;
    $error = 'Could not get rows from '.$table;
    $result = $this->query($sql, $error);   
    $rows = mysql_num_rows($result);
    $p_rows = $rows;
    $pages = ceil($rows / $per_page);
    $i = 1;
    while($i <= $pages)
      {
	if($i == $page)
	  {
	    $nav[$i] = 1;
	  }
	else
	  {
	    $nav[$i] = 0;
	  }
	$i++;
      }
    return $nav;
  }


  public function addTablePrefix($table)
  {
    return TBL_PREFIX.$table;
  }



  public function getAllCustomPaginate($table, $sql_string, $page, $per_page)
  {   
    $table = $this->addTablePrefix($table);
    $all = array();
    $start = ($page - 1) * $per_page;
    $sql = 'SELECT * FROM '.$table.' '.$sql_string.' LIMIT '.$start.', '.$per_page;
    $error = 'Could not get rows from '.$table;

    $result = $this->query($sql, $error);
    $i = 0;
    while($row = mysql_fetch_array($result))
      {
	$all[$i] = $row;
	$i++;
      }
    return $all;    
  }


  public function assignFromPost($ignore)
  {
    $vars = array_keys(get_class_vars(get_class($this)));   
    foreach($vars as $property)
      {
	if($property != 'id' && $property != 'table'
	   && !in_array($property, $ignore))
	  {
	    $this->$property = $_POST[$property];	  
	  }
      }
  }


  public function prepareOptions($first, $label, $table)
  {
    $option = array();
    $data = $this->getAll($table);
    if($first != '')
      {
	$option[0] = $first;
      }

    foreach($data as $index => $value)
      {
	$id = $value['id'];
	$option[$id]= $value[$label];
      }
    return $option;
  }
 

  public function toXHTML($formatting)
  {
    $vars = array_keys(get_class_vars(get_class($this)));
    foreach($vars as $property)
      {
	if(!(is_numeric($property)))
	  {
	    //$this->$property = ereg_replace(38, "&amp;", $this->$property);

	    if(in_array($property, $formatting))	    
	      {		
		$this->$property = str_replace("\r\n", '<br />', $this->$property);
	      }
	    else
	      {
		$this->$property = str_replace("\r\n", ' ', $this->$property);
	      }
	  }
      }
  }    



  public function buildUnionString($ids)
  {
    $str = '(0,';
    $i = 0;
    foreach($ids as $id)
      {
	$str .= $id;
	if(($i + 1) != sizeof($ids))
	  {
	    $str .= ',';
	  }
	$i++;
      }
    $str .= ')';
    
    return $str;  
  }

  
  public function stripMSWordChars()
  {   
    $vars = array_keys(get_class_vars(get_class($this)));
    foreach($vars as $property)
      {
	if(!(is_numeric($property)))
	  {
	    /*
	    $this->$property = ereg_replace(133, "&#133;", $this->$property); // ellipses
	    $this->$property = ereg_replace(8226, "&#8243;", $this->$property); // double prime
	    $this->$property = ereg_replace(8216, "&#039;", $this->$property); // left single quote
	    $this->$property = ereg_replace(145, "&#039;", $this->$property); // left single quote
	    $this->$property = ereg_replace(8217, "&#039;", $this->$property); // right single quote
	    $this->$property = ereg_replace(146, "&#039;", $this->$property); // right single quote
	    $this->$property = ereg_replace(8220, "&#034;", $this->$property); // left double quote
	    $this->$property = ereg_replace(147, "&#034;", $this->$property); // left double quote
	    $this->$property = ereg_replace(8221, "&#034;", $this->$property); // right double quote
	    $this->$property = ereg_replace(148, "&#034;", $this->$property); // right double quote
	    $this->$property = ereg_replace(8226, "&#149;", $this->$property); // bullet
	    $this->$property = ereg_replace(149, "&#149;", $this->$property); // bullet
	    $this->$property = ereg_replace(8211, "&#150;", $this->$property); // en dash
	    $this->$property = ereg_replace(150, "&#150;", $this->$property); // en dash
	    $this->$property = ereg_replace(8212, "&#151;", $this->$property); // em dash
	    $this->$property = ereg_replace(151, "&#151;", $this->$property); // em dash
	    $this->$property = ereg_replace(8482, "&#153;", $this->$property); // trademark
	    $this->$property = ereg_replace(153, "&#153;", $this->$property); // trademark
	    $this->$property = ereg_replace(169, "&copy;", $this->$property); // copyright mark
	    $this->$property = ereg_replace(174, "&reg;", $this->$property); // registration mark	
	    */

	    $this->$property = ereg_replace(133, "", $this->$property); // ellipses
	    $this->$property = ereg_replace(8226, "", $this->$property); // double prime
	    $this->$property = ereg_replace(8216, "", $this->$property); // left single quote
	    $this->$property = ereg_replace(145, "", $this->$property); // left single quote
	    $this->$property = ereg_replace(8217, "", $this->$property); // right single quote
	    $this->$property = ereg_replace(146, "", $this->$property); // right single quote
	    $this->$property = ereg_replace(8220, "", $this->$property); // left double quote
	    $this->$property = ereg_replace(147, "", $this->$property); // left double quote
	    $this->$property = ereg_replace(8221, "", $this->$property); // right double quote
	    $this->$property = ereg_replace(148, "", $this->$property); // right double quote
	    $this->$property = ereg_replace(8226, "", $this->$property); // bullet
	    $this->$property = ereg_replace(149, "", $this->$property); // bullet
	    $this->$property = ereg_replace(8211, "", $this->$property); // en dash
	    $this->$property = ereg_replace(150, "", $this->$property); // en dash
	    $this->$property = ereg_replace(8212, "", $this->$property); // em dash
	    $this->$property = ereg_replace(151, "", $this->$property); // em dash
	    $this->$property = ereg_replace(8482, "", $this->$property); // trademark
	    $this->$property = ereg_replace(153, "", $this->$property); // trademark
	    $this->$property = ereg_replace(169, "", $this->$property); // copyright mark
	    $this->$property = ereg_replace(174, "", $this->$property); // registration mark	
	  }
      }
  }
}
?>