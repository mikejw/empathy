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

namespace Empathy;

class Entity
{
  private $val;
  private $controller;
  private $rows;
  private $result;
  private $globally_ignored_property = array('id', 'table');
  private $properties;
  private $dbh;

  public function __construct($controller = NULL)
  {
    $this->val = new Validate();
    $this->properties = array();
    $this->controller = $controller;

    if(!is_object($controller) || $this->controller->connected == false)
      {	
	$this->dbConnect();	
      }
    $this->loadProperties();
  }  

  protected function insertId()
  {
    return $this->dbh->lastInsertId();
  }

  private function loadProperties()
  {
    $r = new \ReflectionClass(get_class($this));
    foreach($r->getProperties() as $item)
      {
	array_push($this->properties, $item->name);
      }
  }

  
  public function dbConnect()
  {
    if(!defined('DB_SERVER') || DB_SERVER == '')
      {
	throw new SafeException('DB Error: No database host given');
      }
    if(!defined('DB_NAME') || DB_NAME == '')
      {
	throw new SafeException('DB Error: No database name');
      }
    if(!defined('DB_USER') || DB_USER == '')
      {
	throw new SafeException('DB Error: No database username');
      }
    if(!defined('DB_PASS') || DB_PASS == '')
      {
	throw new SafeException('DB Error: No database password');
      }
    $this->dbh = new \PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME,
			   DB_USER, DB_PASS);    
  }

  
  public function query($sql, $error)    
  {
    $result = NULL;
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

  
  public function load($table)
  {
    $loaded = true;
    $table = $this->appendPrefix($table);
    $sql = "SELECT * FROM $table WHERE id = $this->id";
    $error = "Could not load record from $table.";

    $result = $this->query($sql, $error);    
    if($result->rowCount() > 0)
      {
	$row = $result->fetch();
	foreach($row as $index => $value)
	  {
	    $this->$index = $value;
	  }
      }
    else
      {
	$loaded = false;
      }
    return $loaded;
  }
  
  
  public function loadAsOptions($table, $field, $order = NULL)
  {
    $data = array();
    $sql = 'SELECT id,'.$field.' FROM '.$table;
    if($order !== NULL && $order != '')
      {
	$sql .= ' ORDER BY '.$order;
      }
    else
      {
	$sql .= ' ORDER BY '.$field;
      }      
    $error = 'Could not load '.$table.' as options';;
    $result = $this->query($sql, $error);
    foreach($result as $row)
      {
	$id = $row['id'];
	$data[$id] = $row[$field];
      }
    return $data;
  }


  public function sanitize()
  {
    foreach($this->properties as $property)
      {
	if(isset($_POST[$property]) && !(is_numeric($property))) // && ($this->$property == $_POST[$property])) -> breaking where single quotes and break tags
	  {
	    $this->$property = mysql_escape_string($this->$property);
	  }
      }
  }

  public function sanitizeNoPost()
  {
    foreach($this->properties as $property)
      {
	if(!in_array($property, $this->globally_ignored_property) && !is_numeric($property))
	  {
	    $this->$property = mysql_escape_string($this->$property);
	  }
      }
  }

  
  public function save($table, $format, $sanitize)
  {
    $table = $this->appendPrefix($table);
    $this->toXHTMLChris($format);
    if($sanitize == 1)
      {
	$this->sanitize();
      }
    elseif($sanitize == 2)
      {
	$this->sanitizeNoPost();
      }

    $sql = "UPDATE $table SET ";

    $properties = array();
    
    foreach($this->properties as $property)
      {
	if(!in_array($property, $this->globally_ignored_property))
	  {
	    array_push($properties, $property);
	  }
      }

    $i = 0;
    foreach($properties as $property)
      {
	$sql .= "$property = ";
	if(is_numeric($this->$property) && !is_string($this->$property))
	  {
	    $sql .= $this->$property;
	  }
	elseif($this->$property == '')
	  {
	    $sql .= 'NULL';
	  }
	else
	  {
	    $sql .= "'".$this->$property."'";
	  }	
       
	if($i + 1 < sizeof($properties))
	  {
	    $sql .= ", ";
	  }
	$i++;	
      }
    $sql .= " WHERE id = $this->id";
    $error = "Could not update table '$table'";
    if($this->dbh->exec($sql) === false)
      {
	$errors = $this->dbh->errorInfo();
	$this->controller->error("[".htmlentities($sql)
				 ."]<br /><strong>MySQL</strong>: ($error): "
				 .htmlentities($errors[2]), 0);       
      }
  }  
  
  public function insert($table, $id, $format, $sanitize)
  {
    $table = $this->appendPrefix($table);
    $this->toXHTMLChris($format);
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

    $i = 0;
    $id = 0;    
    foreach($this->properties as $property)
      {
	if(!in_array($property, $this->globally_ignored_property))
	  {	    
	    if(is_numeric($this->$property) && !is_string($this->$property))
	      {
		$sql .= $this->$property;
	      }
	    elseif($this->$property == '')
	      {
		$sql .= 'NULL';
	      }	    
	    elseif($this->$property == 'DEFAULT')
	      { 
		$sql .= 'DEFAULT';
	      }
	    else
	      {
		$sql .= "'".$this->$property."'";
	      }
	    
	    if(($i + sizeof($this->globally_ignored_property)) != sizeof($this->properties))
	      {
		$sql .= ", ";
	      }
	  }
	$i++;
      }
    $sql .= ")";
   
    $error = "Could not insert to table '$table'";                


    if(($result = $this->query($sql, $error)) === false)
      {
	$errors = $this->dbh->errorInfo();
	$this->controller->error("[".htmlentities($sql)
				 ."]<br /><strong>MySQL</strong>: ($error): "
				 .htmlentities($errors[2]), 0);       
      }
    else
      {
	return $this->insertId();
      }    
  }
  
  public function appendPrefix($table)
  {
    return TBL_PREFIX.$table;
  }

  public function getRows(&$result)
  {
    $this->rows = $result->rowCount();   
  }


  public function getAll($table)
  {
    $all = array();
    $sql = 'SELECT * FROM '.$table;
    $error = 'Could not get all rows from '.$table;
    $result = $this->query($sql, $error);
    
    $i = 0;
    foreach($result as $row)
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
    foreach($result as $row)
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
    $rows = $result->rowCount();
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



  public function getPaginatePagesSimpleJoin($select, $table1, $table2, $sql_string, $page, $per_page)
  {
    $nav = array();
    $sql = 'SELECT '.$select.' FROM '.$table1.' t1, '.$table2.' t2 '.$sql_string;
    $error = 'Could not get rows from '.$table1;
    $result = $this->query($sql, $error);   
    $rows = $result->rowCount();
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


  public function getPaginatePagesMultiJoin($select, $table1, $table2, $table3, $sql_string, $page, $per_page)
  {
    $nav = array();
    $sql = 'SELECT '.$select.' FROM '.$table1.' t1, '.$table2.' t2, '.$table3.' t3 '.$sql_string;
    $error = 'Could not get rows from '.$table1;
    $result = $this->query($sql, $error);   
    $rows = $result->rowCount();
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



  public function getPaginatePagesMultiJoinGroup($select, $table1, $table2, $table3, $sql_string, $page, $per_page, $group, $order)
  {
    $nav = array();
    $sql = 'SELECT '.$select.' FROM '.$table1.' t1, '.$table2.' t2, '.$table3.' t3 '.$sql_string;
    $sql .= ' GROUP BY '.$group.' ORDER BY '.$order;
    $error = 'Could not get rows from '.$table1;
    $result = $this->query($sql, $error);   
    $rows = $result->rowCount();
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
    foreach($result as $row)
      {
	$all[$i] = $row;
	$i++;
      }
    return $all;    
  }

  public function getAllCustomPaginateSimpleJoin($select, $table1, $table2, $sql_string, $page, $per_page)
  {   
    $all = array();
    $start = ($page - 1) * $per_page;
    $sql = 'SELECT '.$select.' FROM '.$table1.' t1, '.$table2.' t2 '.$sql_string.' LIMIT '.$start.', '.$per_page;
    $error = 'Could not get rows from '.$table1;

    $result = $this->query($sql, $error);
    $i = 0;
    foreach($result as $row)
      {
	$all[$i] = $row;
	$i++;
      }
    return $all;    
  }

  public function getAllCustomPaginateMultiJoin($select, $table1, $table2, $table3, $sql_string, $page, $per_page)
  {   
    $all = array();
    $start = ($page - 1) * $per_page;
    $sql = 'SELECT '.$select.' FROM '.$table1.' t1, '.$table2.' t2, '.$table3.' t3 '.$sql_string.' LIMIT '.$start.', '.$per_page;
    $error = 'Could not get rows from '.$table1;
    $result = $this->query($sql, $error);
    $i = 0;
    foreach($result as $row)
      {
	$all[$i] = $row;
	$i++;
      }
    return $all;    
  }


  public function getAllCustomPaginateMultiJoinGroup($select, $table1, $table2, $table3, $sql_string, $page, $per_page, $group, $order)
  {   
    $all = array();
    $start = ($page - 1) * $per_page;
    $sql = 'SELECT '.$select.' FROM '.$table1.' t1, '.$table2.' t2, '.$table3.' t3 '.$sql_string.' GROUP BY '.$group
      .' ORDER BY '.$order.' LIMIT '.$start.', '.$per_page;
    $error = 'Could not get rows from '.$table1;
    $result = $this->query($sql, $error);
    $i = 0;
    foreach($result as $row)
      {
	$all[$i] = $row;
	$i++;
      }
    return $all;    
  }





  public function assignFromPost($ignore)
  {
    foreach($this->properties as $property)
      {
	if(!in_array($property, $this->globally_ignored_property)
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

 
  public function delete($table)
  {
    $sql = 'DELETE FROM '.$table.' WHERE id = '.$this->id;
    $error = 'Could not delete row.';
    $this->query($sql, $error);
  }

  public function hasValErrors()
  {
    return $this->val->hasErrors();
  }

  public function getValErrors()
  {
    return $this->val->getErrors();
  }

  public function addValError($error, $field='')
  {
    $this->val->addError($error, $field);
  }

  public function doValType($type, $field, $data, $optional)
  {
    return $this->val->valType($type, $field, $data, $optional);
  }
}
?>