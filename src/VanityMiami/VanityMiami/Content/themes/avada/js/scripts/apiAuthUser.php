<?php
/**
 *   @copyright Copyright (c) 2012 Quality Unit s.r.o.
 *   @author Juraj Simon
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 */
require_once 'lib/fast_init.php';

function isLogin($action) {
    return strpos($action, 'login') !== false;
}

function isLogout($action) {
    return strpos($action, 'logout') !== false;
}

$authHash = @$_GET['ahash'];
$action = @$_GET['act'];

if ($authHash == null || $action == null) {
    echo "false - no action or token";
    return;
}

if (isLogin($action) && (isset($_COOKIE['A_auth']) || isset($_COOKIE['V_auth']))) {
    echo "false - already logged in";
    return;
}

$settings = new Lib_SettingFile();
$settings->load();
$lib = $settings->getDb();
try {
    $row = $lib->getOneRow('SELECT value FROM  `qu_g_settings` WHERE  `name` = \'api_key\'');
} catch (Exception $e) {
    echo "false - No API key found";
    return;
}
$apiKey = $row['value'];

try {
    $row = $lib->getOneRow('SELECT au.authtoken, gu.roleid FROM  `qu_g_authusers` au INNER JOIN qu_g_users gu ON au.authid = gu.authid WHERE MD5( CONCAT( au.username, au.authtoken, \''.$apiKey.'\') ) =  \''.$authHash.'\'');
} catch (Exception $e) {
    echo "false" . $e->getMessage();
    return;
}

if ($row['roleid'] == 'la_own' || $row['roleid'] == 'la_adm' || $row['roleid'] == 'la_agent') {
    $cookieName = 'A_auth';
} else if ($row['roleid'] == 'la_visit' || $row['roleid'] == 'la_reg_v') {
    $cookieName = 'V_auth';
}
if (isLogout($action)) {
    setcookie($cookieName, '' ,time()-3600, '/');
    setcookie('_la_sid', '' ,time()-3600, '/');
} else if (isLogin($action)) {
    setcookie($cookieName, $row['authtoken'] ,time()+60*60*24*356, '/');
} else {
    echo "false - unknown action";
    return;
}
echo 'true';
?>
