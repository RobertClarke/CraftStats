<?php
set_include_path('/var/www/cstats/');
$memcache_disable = true;
include 'inc/global.inc.php';

$api->batchProcess();
?>
