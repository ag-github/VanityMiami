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

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type' . ': ' . 'application/x-javascript', true);

require_once 'bootstrap.php';

$sessionId = @$_GET['sid'];
if ($sessionId == '') {
    $sessionId = @$_COOKIE[Lib_Server::SESSION_COOKIE];
}
if ($sessionId == '') {
    exit();
}

La_Model_BrowserEvent::deleteEvent(@$_GET['id']);

?>
