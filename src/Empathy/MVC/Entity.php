<?php

namespace Empathy\MVC;

use Empathy\MVC\Config;
use Empathy\MVC\LogItem;

/**
 * Empathy Entity
 * @package         Empathy
 * @file            Empathy/Entity.php
 * @description     Simple "ORM style" model objects for Empathy.
 * @author          Mike Whiting
 * @license         See LICENCE
 *
 * (c) copyright Mike Whiting
 * with this source code in the file licence.txt
 */
class Entity
{

    public function __construct()
    {
        if (Config::get('LEGACY_MODEL')) {
            ModelLegacy::load($this);
        }
    }


    /**
     * The name of the database table the entity maps to.
     */
    const TABLE = '';

    /**
     * Sanitize constant
     */
    const SANITIZE = 1;

    /**
     * Sanitize no post constant
     */
    const SANITIZE_NO_POST = 2;

    /**
     * Validation object for 'model'.
     */
    private $val;

    /**
     * The last query results.
     */
    private $result;

    /**
     * Fields to ignore while creating/updating records.
     *
     */
    private $globally_ignored_property = array('id', 'table'); // leaving in table to support old models

    /**
     * The current model properties/fields observed through reflrection.
     */
    private $properties;

    /**
     * The PDO database connection handle.
     */
    private $dbh;

    /**
     * Older Empathy applications may rely on this property instead of class constants for table name definitions.
     */
    protected static $table = '';

    /**
     * Get the current time in the form of a MySQL-friendly time and date stamp.
     *
     * @return string date stamp
     */
    public function MYSQLTime()
    {
        return '\'' . date('Y:m:d H:i:s', time()) . '\'';
    }


    /**
     * Instantiates validation object
     * and loads model properties/fields.
     *
     * @return void
     */
    public function init()
    {
        $this->val = new Validate();
        $this->properties = array();
        $this->loadProperties();
    }

    /**
     * Gets the last auto-incremented ID from MySQL.
     *
     * @return integer $id
     */
    public function insertId()
    {
        return $this->dbh->lastInsertId();
    }

    /**
     * Loads the properties/fields of the current mode.
     * Get properties from all subclasses in the right order
     * (with a bit of hackery to make 'table' last)
     *
     * @return void
     */
    private function loadProperties()
    {
        $super_class = 'Empathy\MVC\Entity';

        $r = new \ReflectionClass(get_class($this));

        if ($r->getParentClass()->getName() != $super_class) {
            $props = array();
            while (($class = $r->getName()) != $super_class) {
                $props[] = $r->getProperties();
                $r = $r->getParentClass();
            }
            $props = array_reverse($props);
            $properties = array();
            foreach ($props as $p) {
                foreach ($p as $rp) {
                    $name = $rp->name;
                    if (!in_array($name, $properties) && $name != 'table') {
                        $properties[] = $name;
                    }
                }
            }
            $properties[] = 'table';
            $this->properties = $properties;
        } else {
            // it's a straightforward single subclass
            foreach ($r->getProperties() as $item) {
                if (!$item->isPrivate()) {
                    array_push($this->properties, $item->name);
                }
            }
        }
        $this->properties = array_diff($this->properties, $this->globally_ignored_property);
    }

    /**
     * Connect to database. Only used in old fashoined applications
     * where Entities are not loaded through the Model class.
     *
     * @return @void
     */
    public function dbConnect()
    {
        if (!defined('DB_SERVER') || DB_SERVER == '') {
            throw new SafeException('DB Error: No database host given');
        }
        if (!defined('DB_NAME') || DB_NAME == '') {
            throw new SafeException('DB Error: No database name');
        }
        if (!defined('DB_USER') || DB_USER == '') {
            throw new SafeException('DB Error: No database username');
        }
        /* commenting this out because it's too annoying
           if (!defined('DB_PASS') || DB_PASS == '') {
           throw new SafeException('DB Error: No database password');
           }
        */
        $dsn = 'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME . ';';
        if (defined('DB_PORT') && is_numeric(DB_PORT)) {
            $dsn .= 'port=' . DB_PORT . ';';
        }
        $this->dbh = new \PDO($dsn, DB_USER, DB_PASS);
    }

    /**
     * Set the model objects database connecion handler
     *
     * @param PDO Handle $dbh
     *
     * @return void
     */
    public function setDBH(&$dbh)
    {
        $this->dbh = $dbh;
    }


    /**
     * Clear associated PDO objects
     *
     * @return void
     */
    public function dbDisconnect()
    {
        unset($this->result);
        $this->dbh = null;
    }

    private function logQuery($sql, $error, $params, $level)
    {
        $log = new LogItem(
            'sql query',
            array(
                'query' => $sql
            ),
            self::class,
            $level
        );

        if (sizeof($params) > 0) {
            $log->append('params', $params);
        }

        if ($level != 'debug') {
            $log->setMsg('sql query error');
            $log->append('error', $error);
        }
        $log->fire();
    }

    /**
     * Perform MySQL query
     *
     * @param string $sql the SQL query
     *
     * @param string $error the error to product on failure
     *
     * @return void
     */
    public function query($sql, $error = '', $params = array())
    {
        $result = null;
        $errors = array('', '', '');
        try {
            if (sizeof($params)) {
                $sth = $this->dbh->prepare($sql, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
                $sth->execute($params);
                $result = $sth;
            } else {
                if (($result = $this->dbh->query($sql)) == false) {
                    $errors = $this->dbh->errorInfo();

                    throw new \Exception("[" . htmlentities($sql)
                        . "]<br /><strong>MySQL</strong>: ($error): "
                        . htmlentities($errors[2]));
                } else {
                    $this->result = $result;
                }
            }
            $this->logQuery($sql, $error, $params, 'debug');
        } catch (\Exception $e) {
            $this->logQuery($sql, $error . $errors[2], $params, 'error');
            throw $e;
        }
        
        return $result;
    }

    /**
     * Load a record from a table based on the current id
     *
     * @param string $table Older applications assume they need to pass their table names but this was worked around
     *
     * @return boolean success of load
     */
    public function load($table = null)
    {
        if ($table == null) {
            $c = get_class($this);
            $table = $c::TABLE;
        }

        $loaded = true;
        $sql = "SELECT * FROM $table WHERE id = $this->id";
        $error = "Could not load record from $table.";

        $result = $this->query($sql, $error);
        if ($result->rowCount() > 0) {
            $row = $result->fetch();
            foreach ($row as $index => $value) {
                if (!is_integer($index)) {
                    $this->$index = $value;
                }
            }
        } else {
            $loaded = false;
        }

        return $loaded;
    }

    /**
     * Loads one column from all the rows from in a table and returns a data structure that
     * uses the row id as an index. Useful for building html select form elemements (hence the name).
     *
     * @param string $table the table to load data from
     *
     * @param string field the column to load
     *
     * @param string order the field to order the results by
     *
     * @return array data structure of 'options'
     */
    public function loadAsOptions($table, $field, $order = null)
    {
        $data = array();
        $sql = 'SELECT id,' . $field . ' FROM ' . $table;
        if ($order !== null && $order != '') {
            $sql .= ' ORDER BY ' . $order;
        } else {
            $sql .= ' ORDER BY ' . $field;
        }
        $error = 'Could not load ' . $table . ' as options';;
        $result = $this->query($sql, $error);
        foreach ($result as $row) {
            $id = $row['id'];
            $data[$id] = $row[$field];
        }

        return $data;
    }

    /**
     * Escape non-numeric and non-empty fields
     * Check the field exists within the POST data by default
     *
     * @return void
     */
    public function sanitize($checkPostValues = true)
    {
        foreach ($this->properties as $property) {
            if ((!$checkPostValues || ($checkPostValues && isset($_POST[$property]))) &&
                !in_array($property, $this->globally_ignored_property) &&
                $this->$property !== null &&
                !is_numeric($this->$property)) {
                $this->$property = substr($this->dbh->quote($this->$property), 1, -1);
            }
        }
    }


    /**
     * Escape non-numeric and non-empty fields.
     *
     * @return void
     */
    public function sanitizeNoPost()
    {
        $this->sanitize(false);
    }


    /**
     * Save object back to database (update query).
     *
     * @param string $table the table to save to.
     *
     * @param array $format List of fields to apply HTML filtering to.
     *
     * @param integer $sanitize the form of anti-injection attack sanitization to apply.
     * Either none, while checking POST data, while not checking POST data.
     *
     * @return void
     */
    public function save($table = null, $format = null, $sanitize = self::SANITIZE_NO_POST)
    {
        if ($table === null) {
            $table = $this::TABLE;
        }

        if ($format === null) {
            $format = array();
        }

        $this->toXHTMLChris($format);
        if ($sanitize == self::SANITIZE) {
            $this->sanitize();
        } elseif ($sanitize == self::SANITIZE_NO_POST) {
            $this->sanitizeNoPost();
        }

        $sql = "UPDATE $table SET ";

        $properties = array();

        foreach ($this->properties as $property) {
            array_push($properties, $property);
        }

        $i = 0;
        foreach ($properties as $property) {
            $sql .= "$property = ";
            if (is_numeric($this->$property) && !is_string($this->$property)) {
                $sql .= $this->$property;
            } elseif ($this->$property == '') {
                $sql .= 'NULL';
            } elseif ($this->$property == 'DEFAULT') {
                $sql .= 'DEFAULT';
            } elseif ($this->$property == 'MYSQLTIME') {
                $sql .= $this->MYSQLTime();
            } else {
                $sql .= "'" . $this->$property . "'";
            }

            if ($i + 1 < sizeof($properties)) {
                $sql .= ", ";
            }
            $i++;
        }
        $sql .= " WHERE id = $this->id";

        $error = "Could not update table '$table'";

        $this->query($sql, $error);
    }

    /**
     * Insert object into database.
     *
     * @param string $table the table to save to.
     *
     * @param boolean $id Whether or not the table has an auto-increment id field.
     *
     * @param array $format List of fields to apply HTML filtering to.
     *
     * @param integer $sanitize the method of sanitization for non-numeric fields.
     * Either none, while checking POST data, while not checking POST data.
     *
     * @return void
     */
    public function insert($table, $id, $format, $sanitize, $force_id = false)
    {
        $this->toXHTMLChris($format);
        if ($sanitize == self::SANITIZE) {
            $this->sanitize();
        } elseif ($sanitize == self::SANITIZE_NO_POST) {
            $this->sanitizeNoPost();
        }

        $sql = 'INSERT INTO ' . $table . ' VALUES(';
        if ($id) {
            $sql .= 'NULL, ';
        }

        $i = 0;
        $id = 0;

        foreach ($this->properties as $property) {
            if (($force_id && $property == 'id') || !$force_id) {
                if (is_numeric($this->$property) && !is_string($this->$property)) {
                    $sql .= $this->$property;
                } elseif ($this->$property == '') {
                    $sql .= 'NULL';
                } elseif ($this->$property == 'DEFAULT') {
                    $sql .= 'DEFAULT';
                } elseif ($this->$property == 'MYSQLTIME') {
                    $sql .= $this->MYSQLTime();
                } else {
                    $sql .= "'" . $this->$property . "'";
                }

                if (($i + 1) < sizeof($this->properties)) {
                    $sql .= ", ";
                }
            }
            $i++;
        }
        $sql .= ")";
        $error = "Could not insert to table '$table'";
        $this->query($sql, $error);

        return $this->insertId();
    }

    /**
     * Gets all rows from a table and returns array.*
     * @param string $table the table to fetch from.
     *
     * @return array $all all rows from the table.
     */
    public function getAll($table = null)
    {
        if ($table === null) {
            $table = $this::TABLE;
        }

        $all = array();
        $sql = 'SELECT * FROM ' . $table;
        $error = 'Could not get all rows from ' . $table;
        $result = $this->query($sql, $error);

        $i = 0;
        foreach ($result as $row) {
            $all[$i] = $row;
            $i++;
        }

        return $all;
    }

    /**
     * Similar to getAll but with custom SQL appended to the query.
     *
     * @param string $table the target table.
     *
     * @param string $sql_string the additional custom SQL.
     *
     * @return array $all the rows returned as a result of the query
     */
    public function getAllCustom($table, $sql_string, $params = array())
    {
        $all = array();
        $sql = 'SELECT * FROM ' . $table . ' ' . $sql_string;
        $error = 'Could not get all rows from ' . $table;
        $result = $this->query($sql, $error, $params);

        $i = 0;
        foreach ($result as $row) {
            $all[$i] = $row;
            $i++;
        }

        return $all;
    }

    public function getPaginatePages($table, $sql_string, $page, $per_page)
    {
        $nav = array();
        $sql = 'SELECT * FROM ' . $table . ' ' . $sql_string;
        //$sql = 'SELECT FOUND_ROWS()';
        $error = 'Could not get rows from ' . $table;
        $result = $this->query($sql, $error);
        $rows = $result->rowCount();
        $p_rows = $rows;
        $pages = ceil($rows / $per_page);
        $i = 1;
        while ($i <= $pages) {
            if ($i == $page) {
                $nav[$i] = 1;
            } else {
                $nav[$i] = 0;
            }
            $i++;
        }

        return $nav;
    }

    public function getPaginatePagesSimpleJoin($select, $table1, $table2, $sql_string, $page, $per_page, $leftJoins = '')
    {
        $nav = array();
        $sql = 'SELECT ' . $select . ' FROM ' . $table1 . ' t1';

        if ($leftJoins === '') {
            $sql .= ', ';
        }
        $sql .= $leftJoins . $table2 . ' t2 ' . $sql_string;

        $error = 'Could not get rows from ' . $table1;

        $result = $this->query($sql, $error);
        $rows = $result->rowCount();
        $p_rows = $rows;
        $pages = ceil($rows / $per_page);
        $i = 1;
        while ($i <= $pages) {
            if ($i == $page) {
                $nav[$i] = 1;
            } else {
                $nav[$i] = 0;
            }
            $i++;
        }

        return $nav;
    }

    public function getPaginatePagesMultiJoin($select, $table1, $table2, $table3, $sql_string, $page, $per_page)
    {
        $nav = array();
        $sql = 'SELECT ' . $select . ' FROM ' . $table1 . ' t1, ' . $table2 . ' t2, ' . $table3 . ' t3 ' . $sql_string;
        $error = 'Could not get rows from ' . $table1;
        $result = $this->query($sql, $error);
        $rows = $result->rowCount();
        $p_rows = $rows;
        $pages = ceil($rows / $per_page);
        $i = 1;
        while ($i <= $pages) {
            if ($i == $page) {
                $nav[$i] = 1;
            } else {
                $nav[$i] = 0;
            }
            $i++;
        }

        return $nav;
    }

    public function getPaginatePagesMultiJoinGroup(
        $select,
        $table1,
        $table2,
        $table3,
        $sql_string,
        $page,
        $per_page,
        $group,
        $order
    )
    {
        $nav = array();
        $sql = 'SELECT ' . $select . ' FROM ' . $table1 . ' t1, ' . $table2 . ' t2, ' . $table3 . ' t3 ' . $sql_string;
        $sql .= ' GROUP BY ' . $group . ' ORDER BY ' . $order;
        $error = 'Could not get rows from ' . $table1;
        $result = $this->query($sql, $error);
        $rows = $result->rowCount();
        $p_rows = $rows;
        $pages = ceil($rows / $per_page);
        $i = 1;
        while ($i <= $pages) {
            if ($i == $page) {
                $nav[$i] = 1;
            } else {
                $nav[$i] = 0;
            }
            $i++;
        }

        return $nav;
    }

    public function getAllCustomPaginate($table, $sql_string, $page, $per_page)
    {
        $all = array();
        $start = ($page - 1) * $per_page;
        $sql = 'SELECT * FROM ' . $table . ' ' . $sql_string . ' LIMIT ' . $start . ', ' . $per_page;
        //$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM '.$table.' '.$sql_string.' LIMIT '.$start.', '.$per_page;
        $error = 'Could not get rows from ' . $table;

        $result = $this->query($sql, $error);
        $i = 0;
        foreach ($result as $row) {
            $all[$i] = $row;
            $i++;
        }

        return $all;
    }

    public function getAllCustomPaginateSimpleJoin($select, $table1, $table2, $sql_string, $page, $per_page, $leftJoins = '')
    {
        $all = array();
        $start = ($page - 1) * $per_page;
        $sql = 'SELECT ' . $select . ' FROM ' . $table1 . ' t1';

        if ($leftJoins === '') {
            $sql .= ', ';
        }
        $sql .= $leftJoins . $table2 . ' t2 ' . $sql_string . ' LIMIT ' . $start . ', ' . $per_page;
        $error = 'Could not get rows from ' . $table1;

        $result = $this->query($sql, $error);
        $i = 0;
        foreach ($result as $row) {
            $all[$i] = $row;
            $i++;
        }

        return $all;
    }

    public function getAllCustomPaginateMultiJoin($select, $table1, $table2, $table3, $sql_string, $page, $per_page)
    {
        $all = array();
        $start = ($page - 1) * $per_page;
        $sql = 'SELECT ' . $select . ' FROM ' . $table1 . ' t1, ' . $table2 . ' t2, '
            . $table3 . ' t3 ' . $sql_string . ' LIMIT ' . $start . ', ' . $per_page;
        $error = 'Could not get rows from ' . $table1;
        $result = $this->query($sql, $error);
        $i = 0;
        foreach ($result as $row) {
            $all[$i] = $row;
            $i++;
        }

        return $all;
    }

    public function getAllCustomPaginateMultiJoinGroup(
        $select,
        $table1,
        $table2,
        $table3,
        $sql_string,
        $page,
        $per_page,
        $group,
        $order
    )
    {
        $all = array();
        $start = ($page - 1) * $per_page;
        $sql = 'SELECT ' . $select . ' FROM ' . $table1 . ' t1, ' . $table2 . ' t2, '
            . $table3 . ' t3 ' . $sql_string . ' GROUP BY ' . $group
            . ' ORDER BY ' . $order . ' LIMIT ' . $start . ', ' . $per_page;
        $error = 'Could not get rows from ' . $table1;
        $result = $this->query($sql, $error);
        $i = 0;
        foreach ($result as $row) {
            $all[$i] = $row;
            $i++;
        }

        return $all;
    }

    public function assignFromPost($ignore, $force_id = false)
    {
        foreach ($this->properties as $property) {
            if (($force_id && $property == 'id') ||
                (!in_array($property, $this->globally_ignored_property)
                    && !in_array($property, $ignore))) {
                $this->$property = $_POST[$property];
            }
        }
    }


    public function prepareOptions($first, $label, $table)
    {
        $option = array();
        $data = $this->getAll($table);
        if ($first != '') {
            $option[0] = $first;
        }

        foreach ($data as $index => $value) {
            $id = $value['id'];
            $option[$id] = $value[$label];
        }

        return $option;
    }

    // @todo deprecate function name?
    // `toFilteredHTML` might be better?
    public function toXHTMLChris($formatting)
    {
        $pTagPattern = '!&lt;p&gt;(.*?)&lt;/p&gt;!m';
        $aTagPattern = '!&lt;a +href=&quot;((?:ht|f)tps?://.*?)&quot;'
            . '(?: +title=&quot;(.*?)&quot;)?(?: +target=&quot;(.*?)&quot;)? *&gt;(.*?)&lt;/a&gt;!m';

        $imgTagPattern = '!&lt;img +src=&quot;(https?://.*?)?&quot;(?: +id=&quot;'
            . '(.*?)&quot;)?(?: +alt=&quot;(.*?)&quot;)? *&gt;!m';

        $preTagPattern1 = '!&lt;pre *&gt;\n*(.*?)&lt;/pre&gt;!ms';
        $preTagPattern2 = '!&lt;pre(?: +class=&quot;(.*?)&quot;)? *&gt;\n*(.*?)&lt;/pre&gt;!ms';

        foreach ($this->properties as $property) {
            if (!is_numeric($property) && in_array($property, $formatting)) {
                $markup = $this->$property;
                $markup = str_replace("\r", "\n", $markup);
                $markup = preg_replace("!\n\n+!", "\n", $markup);
                $markup = htmlentities($markup, ENT_QUOTES, 'UTF-8');

                $markup = preg_replace(
                    $pTagPattern,
                    '<p>$1</p>',
                    $markup
                );

                $markup = preg_replace(
                    $aTagPattern,
                    '<a href="$1" title="$2" target="$3">$4</a>',
                    $markup
                );

                $markup = preg_replace(
                    $imgTagPattern,
                    '<img src="$1" id="$2" alt="$3" class="img-fluid">',
                    $markup
                );

                $markup = preg_replace(
                    $preTagPattern1,
                    // must specifiy a language and not include a code block
                    // to get default prism styling
                    "<pre class=\"line-number language-bash\">$1</pre>",
                    $markup
                );

                $markup = preg_replace(
                    $preTagPattern2,
                    "<pre><code class=\"lang-$1\">$2</code></pre>",
                    $markup
                );

                $markup = preg_replace('/&amp;nbsp;/', '&nbsp;', $markup);
                $markup = preg_replace('/ +id=""/', '', $markup);
                $markup = preg_replace('!&lt;strong&gt;(.*?)&lt;/strong&gt;!m', '<strong>$1</strong>', $markup);
                $markup = preg_replace('!&lt;em&gt;(.*?)&lt;/em&gt;!m', '<em>$1</em>', $markup);
                $hasPTags = preg_match('/<p>/', $markup);

                if (!$hasPTags) {
                    $lines = explode("\n", $markup);
                    $insidePre = false;
                    foreach ($lines as $key => $line) {
                        if (preg_match('/<pre/', $line)) {
                            $insidePre = true;
                        }
                        if (!$insidePre) {
                            $lines[$key] = "<p>{$line}</p>";
                        }
                        if (preg_match('/\/pre>/', $line)) {
                            $insidePre = false;
                        }
                    }
                    $markup = implode("\n", $lines);
                }
                $this->$property = $markup;
            }
        }
    }

    public function toXHTML($formatting)
    {
        foreach ($this->properties as $property) {
            if (!(is_numeric($property))) {
                //$this->$property = ereg_replace(38, "&amp;", $this->$property);

                if (in_array($property, $formatting)) {
                    $this->$property = str_replace("\r\n", '<br />', $this->$property);
                } else {
                    $this->$property = str_replace("\r\n", ' ', $this->$property);
                }
            }
        }
    }

    public function buildUnionString($ids)
    {
        array_unshift($ids, 0);
        return '(' . implode(',', array_unique($ids)) . ')';
    }

    public function stripMSWordChars()
    {
        foreach ($this->properties as $property) {
            if (!(is_numeric($property))) {
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

    public function delete($table = null)
    {
        if ($table == null) {
            $c = get_class($this);
            $table = $c::TABLE;
        }
        $sql = 'DELETE FROM ' . $table . ' WHERE id = ' . $this->id;
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

    public function addValError($error, $field = '')
    {
        $this->val->addError($error, $field);
    }

    public function doValType($type, $field, $data, $optional, $message = null)
    {
        return $this->val->valType($type, $field, $data, $optional, $message);
    }


    /**
     * get entity properties
     *
     *
     *
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
