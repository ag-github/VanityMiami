<?php
error_reporting(E_ALL & ~E_STRICT);

@ini_set('session.gc_maxlifetime', 28800);
@ini_set('session.auto_start', 0);
@ini_set('session.cookie_path', '/');
@ini_set('session.use_cookies', true);
@ini_set('session.use_trans_sid', false);

define('DEBUG', true);
define('DB_TABLE_PREFIX', "qu_");

@include_once('custom.php');
@include_once('../include/Compiled/Core.php');

require_once 'paths.php';
Gpf_Application::create(new La_Application());
?>
