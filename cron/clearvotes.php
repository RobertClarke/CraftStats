<?php
set_include_path('/var/www/cstats/');
$memcache_disable = true;
include 'inc/global.inc.php';

$database->query("UPDATE servers SET votes = 0");
$database->query("TRUNCATE uservotes");
?>