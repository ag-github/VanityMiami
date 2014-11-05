#!/usr/local/bin/php -q
<?php
if (!isset($_SERVER["PROJECT_SOURCE_PATH"])) {
    chdir(dirname(__FILE__).'/../scripts/');
    
    require_once 'bootstrap.php';
    require_once 'Getopt.php';
} else {
    require_once $_SERVER["PROJECT_SOURCE_PATH"] . '/bootstrap.php';
    require_once $_SERVER["PROJECT_SOURCE_PATH"] . '/Getopt.php';
}

ob_start();
    Gpf_Module::run(new La_Mail_Pipe());
ob_end_clean();
?>
