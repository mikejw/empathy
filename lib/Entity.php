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

  public function __construct($controller)
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
    //    try{
      $this->dbh = new \PDO('mysql:host='.DB_SERVER.';dbname='.DB_NAME,
			   DB_USER, DB_PASS);
      // }
      /*
    catch (PDOException $e) {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
    }
      */
  }

  
  public function query($sql, $error)    
  {
    $result = NULL;
   
    /*
    if(is_object($this->result))
      {
	$this->result->closeCursor();
      }
    */
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
	//	if(!in_array($property, $this->globally_ignored_property) && $this->$property != '')
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
    $this->rows = mysql_num_rows($result);   
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



  public function getPaginatePagesMultiJoinGroup($select, $table1, $table2, $table3, $sql_string, $page, $per_page, $group, $order)
  {
    $nav = array();
    $sql = 'SELECT '.$select.' FROM '.$table1.' t1, '.$table2.' t2, '.$table3.' t3 '.$sql_string;
    $sql .= ' GROUP BY '.$group.' ORDER BY '.$order;
    $error = 'Could not get rows from '.$table1;
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
    //while($row = mysql_fetch_array($result))
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
    while($row = mysql_fetch_array($result))
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
    while($row = mysql_fetch_array($result))
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
 

  public function toXHTMLChris($formatting)
  {
    $markup = '';
    foreach($this->properties as $property)
      {
	if(!is_numeric($property) && in_array($property, $formatting))	    
	  {	     	    
	    $markup = $this->$property;
	    $markup = str_replace("\r", "\n", $markup);
	    $markup = preg_replace("!\n\n+!", "\n", $markup);
	    
	    $markup = htmlentities($markup, ENT_QUOTES, 'UTF-8');

	    $markup = preg_replace('!&lt;a +href=&quot;((?:ht|f)tps?://.*?)&quot;(?: +title=&quot;(.*?)&quot;)? *&gt;(.*?)&lt;/a&gt;!m',
				   '<a href="$1" title="$2">$3</a>', $markup);
	  	    
	    $markup = preg_replace('!&lt;img +src=&quot;(https?://.*?)?&quot;(?: +id=&quot;(.*?)&quot;)?(?: +alt=&quot;(.*?)&quot;)? */&gt;!m', '<img src="$1" id="$2" alt="$3" />', $markup);
	    $markup = preg_replace('/ +id=""/', '', $markup);

	    $markup = preg_replace('!&lt;strong&gt;(.*?)&lt;/strong&gt;!m', '<strong>$1</strong>', $markup); 
	    $markup = preg_replace('!&lt;em&gt;(.*?)&lt;/em&gt;!m', '<em>$1</em>', $markup); 
	    
	    $lines = explode("\n", $markup);
	    foreach($lines as $key => $line)
	      {
		$lines[$key] = "<p>{$line}</p>";
	      }
	    $markup = implode("\n", $lines);	   
	    $this->$property = $markup;
	  }
      }
  }
  

  public function toXHTML($formatting)
  {
    foreach($this->properties as $property)
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
    foreach($this->properties as $property)
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