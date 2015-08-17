<?php

if(false == @include('../vendor/autoload.php')) {

} else {
    $argv = array(
        '--testsuite', 'exp',
        '--configuration', './phpunit.xml'
    );
    $_SERVER['argv'] = $argv;
    PHPUnit_TextUI_Command::main(false);
    
}
?>