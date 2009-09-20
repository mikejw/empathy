<?php
require('config_by_yaml.php');
include('empathy/include/Bootstrap.php');
$boot = new Bootstrap($config['module'], $config['module_is_dynamic'], $config['specialised']);
?>