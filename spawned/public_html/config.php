<?php
date_default_timezone_set('Europe/London');

// about
define('NAME', '');
define('TITLE', '');
define('AUTHOR', '');
define('SUB_TITLE', '');

// locations
define('DBMS', 'MYSQL');
define('DOC_ROOT', '/var/www/localhost/htdocs/knowing');
define('WEB_ROOT', '192.168.0.6/knowing');
define('PUBLIC_DIR', '/public_html');

// database
define('DB_SERVER', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pie');
define('TBL_PREFIX', '');

// modules / sections / session vars
define('TPL_BY_CLASS', 1); // looks for teplates with same name as current class
define('DEF_MOD', 0);
$module = array('front');
$moduleIsDynamic = array(0);

$sessionVar = array("app_error", "user_id", "failed_uri", "cookies");
?>
