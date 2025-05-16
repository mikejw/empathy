<?php

namespace Empathy\MVC;

use Empathy\MVC\Config;
use Empathy\MVC\LogItem;

class Entity
{
    public function __construct()
    {
        if (Config::get('LEGACY_MODEL')) {
            ModelLegacy::load($this);
        }
    }

    const TABLE = '';

    private const GLOBALLY_IGNORED_PROPERTIES = ['id', 'table']; // leaving in table to support old models
    private $val;
    private $result;
    private $properties;
    private $dbh;

    /**
     * Older Empathy applications may rely on this property instead of class constants for table name definitions.
     */
    protected static $table = '';

    public function MYSQLTime()
    {
        return '\'' . date('Y:m:d H:i:s', time()) . '\'';
    }

    public function init()
    {
        $this->val = new Validate();
        $this->properties = [];
        $this->loadProperties();
    }

    public function insertId()
    {
        return $this->dbh->lastInsertId();
    }

    private function loadProperties()
    {
        $super_class = 'Empathy\MVC\Entity';

        $r = new \ReflectionClass(get_class($this));

        if ($r->getParentClass()->getName() != $super_class) {
            $props = [];
            while (($class = $r->getName()) != $super_class) {
                $props[] = $r->getProperties();
                $r = $r->getParentClass();
            }
            $props = array_reverse($props);
            $properties = [];
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
        $this->properties = array_diff($this->properties, self::GLOBALLY_IGNORED_PROPERTIES);
    }

    /**
     * Used in old fashioned applications
     * where Entities are not loaded through the Model class.
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
        $dsn = 'mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME . ';';
        if (defined('DB_PORT') && is_numeric(DB_PORT)) {
            $dsn .= 'port=' . DB_PORT . ';';
        }
        $this->dbh = new \PDO($dsn, DB_USER, DB_PASS);
    }

    public function setDBH(&$dbh)
    {
        $this->dbh = $dbh;
    }

    public function dbDisconnect()
    {
        unset($this->result);
        $this->dbh = null;
    }

    private function logQuery($sql, $error, $params, $level)
    {
        $log = new LogItem(
            'sql query',
            [
                'query' => $sql
            ],
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

    public function query($sql, $error = '', $params = [])
    {
        $result = null;
        $errors = ['', '', ''];
        try {
            if (sizeof($params)) {
                $sth = $this->dbh->prepare($sql, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
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

    public function load($id = null)
    {
        if (isset($id) && is_numeric($id)) {
            $this->id = intval($id);
        } else {
            $this->id = 0;
            return false;
        }

        $table = $this::TABLE;
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
            return true;
        } else {
            return false;
        }
    }

    public function loadAsOptions($field, $order = null)
    {
        $data = [];
        $table = $this::TABLE;
        $sql = "SELECT id, $field FROM $table";
        if ($order !== null && $order != '') {
            $sql .= " ORDER BY $order";
        } else {
            $sql .= " ORDER BY $field";
        }
        $error = "Could not load $table as options";
        $result = $this->query($sql, $error);
        foreach ($result as $row) {
            $id = $row['id'];
            $data[$id] = $row[$field];
        }

        return $data;
    }

    public function save($filter = [])
    {
        $this->toFilteredHTML($filter);
        $table = $this::TABLE;
        $sql = "UPDATE $table SET ";

        $i = 0;
        $params = [];
        foreach ($this->properties as $property) {
            $sql .= "$property = ";

            if ($this->$property == '') {
                $sql .= 'NULL';
            } elseif ($this->$property == 'DEFAULT') {
                $sql .= 'DEFAULT';
            } elseif ($this->$property == 'MYSQLTIME') {
                $sql .= $this->MYSQLTime();
            } else {
                $sql .= '?';
                $params[] = $this->$property;
            }
            if ($i + 1 < sizeof($this->properties)) {
                $sql .= ', ';
            }
            $i++;
        }
        $sql .= " WHERE id = $this->id";
        $error = "Could not update table '$table'";
        $this->query($sql, $error, $params);
    }

    public function insert($filter = [], $id = true)
    {
        $this->toFilteredHTML($filter);
        $table = $this::TABLE;
        $sql = "INSERT INTO $table VALUES(";
        if ($id) {
            $sql .= 'NULL, ';
        }
        $i = 0;
        $params = [];
        foreach ($this->properties as $property) {
            if ($this->$property == '') {
                $sql .= 'NULL';
            } elseif ($this->$property == 'DEFAULT') {
                $sql .= 'DEFAULT';
            } elseif ($this->$property == 'MYSQLTIME') {
                $sql .= $this->MYSQLTime();
            } else {
                $sql .= '?';
                $params[] = $this->$property;
            }
            if (($i + 1) < sizeof($this->properties)) {
                $sql .= ', ';
            }
            $i++;
        }
        $sql .= ')';
        $error = "Could not insert to table '$table'";
        $this->query($sql, $error, $params);

        return $this->insertId();
    }

    public function getAll()
    {
        $table = $this::TABLE;
        $all = [];
        $sql = "SELECT * FROM $table";
        $error = "Could not get all rows from '$table'";
        $result = $this->query($sql, $error);

        $i = 0;
        foreach ($result as $row) {
            $all[$i] = $row;
            $i++;
        }

        return $all;
    }

    public function getAllCustom($sqlString, $params = [])
    {
        $table = $this::TABLE;
        $all = [];
        $sql = "SELECT * FROM $table $sqlString";
        $error = "Could not get all rows from '$table'";
        $result = $this->query($sql, $error, $params);

        $i = 0;
        foreach ($result as $row) {
            $all[$i] = $row;
            $i++;
        }
        return $all;
    }

    public function getPaginatePages($sqlString, $page, $perPage, $params = [])
    {
        $table = $this::TABLE;
        $nav = [];
        $sql = "SELECT * FROM $table $sqlString";
        //$sql = 'SELECT FOUND_ROWS()';
        $error = "Could not get rows from '$table'";
        $result = $this->query($sql, $error, $params);
        $rows = $result->rowCount();
        $pages = ceil($rows / $perPage);
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

    public function getPaginatePagesSimpleJoin($select, $table2, $sqlString, $page, $perPage, $leftJoins = '', $params = [])
    {
        $nav = [];
        $table1 = $this::TABLE;
        $sql = "SELECT $select FROM $table1 t1";

        if ($leftJoins === '') {
            $sql .= ', ';
        }
        $sql .= "$leftJoins $table2 t2 $sqlString";
        $error = "Could not get rows from '$table1'";
        $result = $this->query($sql, $error, $params);
        $rows = $result->rowCount();
        $pages = ceil($rows / $perPage);
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

    public function getPaginatePagesMultiJoin($select, $table2, $table3, $sqlString, $page, $perPage, $params = [])
    {
        $nav = [];
        $table1 = $this::TABLE;
        $sql = "SELECT $select FROM $table1 t1, $table2 t2, $table3 t3 $sqlString";
        $error = "Could not get rows from '$table1'";
        $result = $this->query($sql, $error, $params);
        $rows = $result->rowCount();
        $pages = ceil($rows / $perPage);
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
        $table2,
        $table3,
        $sqlString,
        $page,
        $perPage,
        $group,
        $order,
        $params = []
    ) {
        $nav = [];
        $table1 = $this::TABLE;
        $sql = "SELECT $select FROM $table1 t1, $table2 t2, $table3 t3 $sqlString";
        $sql .= " GROUP BY $group ORDER BY $order";
        $error = "Could not get rows from '$table1'";
        $result = $this->query($sql, $error, $params);
        $rows = $result->rowCount();
        $pages = ceil($rows / $perPage);
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

    public function getAllCustomPaginate($sqlString, $page, $perPage, $params = [])
    {
        $all = [];
        $table = $this::TABLE;
        $start = ($page - 1) * $perPage;
        $sql = "SELECT * FROM $table $sqlString LIMIT $start, $perPage";
        $error = "Could not get rows from '$table'";
        $result = $this->query($sql, $error, $params);
        $i = 0;
        foreach ($result as $row) {
            $all[$i] = $row;
            $i++;
        }
        return $all;
    }

    public function getAllCustomPaginateSimpleJoin($select, $table2, $sqlString, $page, $perPage, $leftJoins = '', $params = [])
    {
        $all = [];
        $table1 = $this::TABLE;
        $start = ($page - 1) * $perPage;
        $sql = "SELECT $select FROM $table1 t1";

        if ($leftJoins === '') {
            $sql .= ', ';
        }
        $sql .= "$leftJoins $table2 t2 $sqlString LIMIT $start, $perPage";
        $error = "Could not get rows from '$table1'";

        $result = $this->query($sql, $error, $params);
        $i = 0;
        foreach ($result as $row) {
            $all[$i] = $row;
            $i++;
        }
        return $all;
    }

    public function getAllCustomPaginateMultiJoin($select, $table2, $table3, $sqlString, $page, $perPage, $params = [])
    {
        $all = [];
        $table1 = $this::TABLE;
        $start = ($page - 1) * $perPage;
        $sql = "SELECT $select FROM $table1 t1, $table2 t2,"
            . " $table3 t3 $sqlString LIMIT $start, $perPage";
        $error = "Could not get rows from '$table1'";
        $result = $this->query($sql, $error, $params);
        $i = 0;
        foreach ($result as $row) {
            $all[$i] = $row;
            $i++;
        }
        return $all;
    }

    public function getAllCustomPaginateMultiJoinGroup(
        $select,
        $table2,
        $table3,
        $sqlString,
        $page,
        $perPage,
        $group,
        $order,
        $params = []
    )
    {
        $all = [];
        $table1 = $this::TABLE;
        $start = ($page - 1) * $perPage;
        $sql = "SELECT $select FROM $table1 t1, $table2 t2,"
            . " $table3 t3 $sqlString GROUP BY $group"
            . " ORDER BY $order LIMIT $start, $perPage";
        $error = "Could not get rows from '$table1'";
        $result = $this->query($sql, $error, $params);
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
                (!in_array($property, self::GLOBALLY_IGNORED_PROPERTIES)
                    && !in_array($property, $ignore))) {
                $this->$property = $_POST[$property];
            }
        }
    }

    public function prepareOptions($first, $label)
    {
        $option = [];
        $data = $this->getAll();
        if ($first != '') {
            $option[0] = $first;
        }

        foreach ($data as $index => $value) {
            $id = $value['id'];
            $option[$id] = $value[$label];
        }

        return $option;
    }

    public function toFilteredHTML($filtering)
    {
        $pTagPattern = '!&lt;p&gt;(.*?)&lt;/p&gt;!m';
        $aTagPattern = '!&lt;a +href=&quot;((?:ht|f)tps?://.*?)&quot;'
            . '(?: +title=&quot;(.*?)&quot;)?(?: +target=&quot;(.*?)&quot;)? *&gt;(.*?)&lt;/a&gt;!m';

        $imgTagPattern = '!&lt;img +src=&quot;(https?://.*?)?&quot;(?: +id=&quot;'
            . '(.*?)&quot;)?(?: +alt=&quot;(.*?)&quot;)? *&gt;!m';

        $preTagPattern1 = '!&lt;pre *&gt;\n*(.*?)&lt;/pre&gt;!ms';
        $preTagPattern2 = '!&lt;pre(?: +class=&quot;(.*?)&quot;)? *&gt;\n*(.*?)&lt;/pre&gt;!ms';

        foreach ($this->properties as $property) {
            if (!is_numeric($property) && in_array($property, $filtering)) {
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
                    "<pre>$1</pre>",
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

    public function buildUnionString($ids)
    {
        array_unshift($ids, 0);
        return '(' . implode(',', array_unique($ids)) . ')';
    }

    public function delete()
    {
        $table = $this::TABLE;
        $id = $this->id;
        $sql = "DELETE FROM $table WHERE id = $id";
        $error = "Could not delete row.";
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

    public function getProperties()
    {
        return $this->properties;
    }
}
