<?php
set_include_path('/home/cstats/');
$memcache_disable = true;
include 'inc/global.inc.php';

$api->batchProcess();
?>
