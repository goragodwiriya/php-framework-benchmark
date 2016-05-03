<?php

require __DIR__.'/../libs/parse_results.php';
require __DIR__.'/../libs/build_table.php';
$module = $_SERVER['argv'][1];
if (in_array($module, array_keys($modules))) {
    $results = parse_results(__DIR__.'/../output/'.$module.'/results.hello_world.log');
    //var_dump($results);
    echo $modules[$module];
    echo "\n";
    echo build_table($results);
    echo "\n";
}