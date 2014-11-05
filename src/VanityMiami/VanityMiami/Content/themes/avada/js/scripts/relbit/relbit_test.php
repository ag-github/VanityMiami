<?php
require_once("relbit_sdk.php");

$rb = new Relbit_API (array("acl_api_key" => 'b0a6e959d7f7b3eb5fda347aaf65e9612ffd79cc'));


print_r($rb->send_request ("domain.add",
    array("domain_name" => 'test2.ladesk.com',
          "domain_type" => 'vcs')));


// tu to padne na tom ze nemam prava na domenu. ked skusim neskor, tak to zbehne lebo uz ju vytvorilo
print_r($rb->send_request("storage.vcs_add",
    array("domain_name" => 'test2.ladesk.com',
          "url" => 'https://svn.qualityunit.com/svn/main/LiveAgentPro/trunk/latest_build/',
          "vcs_type" => 'svn')));


print_r($rb->send_request("storage.vcs_pull",
    array("domain_name" => 'test2.ladesk.com')));

$dbName = 'db_test2';
$dbPass = '5df8g4df';
    
print_r($rb->send_request("database.add",
    array("database_name" => $dbName,
          "database_pass" => $dbPass)));
    
// call when svn pull ready - to viem spravit podla toho status volania
// test2.ladesk.com/scripts/dbupdate.php?dbName=$dbName&dbPass=$dbPass

    
// ako nastavim command?
print_r($rb->send_request("cron.update",
    array("domain_name " => 'test2.ladesk.com',
          "cron_records " => array("-N" =>
                array("min" => '*',
                      "hour" => '*',
                      "day_of_month" => '*',
                      "month" => '*',
                      "day_of_week" => '*',
                      "single" => '0',
                      "command" => '???? How do I set what should be executed ????')))));
                

?>
