<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @since Version 4.0.0
 *   $Id: index.php 13370 2007-08-27 12:41:15Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */


if (@$_REQUEST['sid'] == '' || @$_REQUEST['iid'] == '' || @$_REQUEST['bu'] == '') {
    echo "Missing sid, iid or bu param.";
    exit();
}

require_once 'lib/invitation.php';

$settings = new Lib_SettingFile();
$settings->load();
$invitation = new Lib_Invitation($settings->getDb());
if (@$_GET['a'] == 'r') {
    $invitation->refuse(@$_REQUEST['iid']);
} else {
    $invitation->printJS(@$_REQUEST['sid'], @$_REQUEST['iid'], @$_REQUEST['bu']);
}
?>
