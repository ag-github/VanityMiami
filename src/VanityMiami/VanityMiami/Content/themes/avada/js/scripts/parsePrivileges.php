<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Andrej Harsani
 *   @since Version 1.0.0
 *   $Id: importLanguage.php 13163 2007-08-07 11:15:49Z aharsani $
 *
 *   Licensed under the Quality Unit, s.r.o. Standard End User License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/license
 *
 */

require_once 'bootstrap.php';

$generator = new Gpf_Rpc_PermissionGenerator('La_Privileges', 'com.qualityunit.liveagent.client.Permissions');
$generator->addApplicationPermission('agent_ranking', 'read_own');
$generator->generate();
?>
