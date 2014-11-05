<?php
require_once 'bootstrap.php';

if(!(isset($argv[1])) ) {
    echo ("usage is $argv[0] Gpf_Tests_Benchmark arguments \n ");
    exit();
}

$benchmark = Gpf::newObj($argv[1], $argv);
$benchmark->run();
?>
