<?php
ob_start();

chdir(dirname(__FILE__));

require_once 'lib/fast_init.php';

$settings = new Lib_SettingFile();
$settings->load();

if ($settings->get('cronSleepUntil') != "") {
    if (time() < strtotime($settings->get('cronSleepUntil'))) {
    	$content = ob_get_contents();
    	@ob_end_clean();
    	echo trim($content);
        exit();
    }
}

require_once 'bootstrap.php';
require_once 'Getopt.php';

if (!isset($argv) && @$_REQUEST['time'] == '') {
    // if script is running from browser time is limited to 24s
    $_REQUEST['time'] = 24;
}

Gpf_Module::run(new La_Jobs());

$content = ob_get_contents();
@ob_end_clean();
echo trim($content);
?>
