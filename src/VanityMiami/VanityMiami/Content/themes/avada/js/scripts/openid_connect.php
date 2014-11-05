<?php
/**
 *   @copyright Copyright (c) 2013 Quality Unit s.r.o.
 *   @author Juraj Simon

 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 */

require_once '../scripts/bootstrap.php';

$authUrl = @$_GET['url'];
$afterLoginUrl = @$_GET['afterLoginUrl'];

if ($authUrl == null) {
    throw new Gpf_Exception('Unknown auth url.');
}

Gpf_Http::setHeader("Cache-Control", "no-cache");

Gpf_Module::run(new La_Auth_OpenidConnect($authUrl, $afterLoginUrl));
?>
