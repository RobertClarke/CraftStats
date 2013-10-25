<?php
include '../inc/global.inc.php';
$last = $database->query("SELECT * FROM mcstatus ORDER BY time DESC LIMIT 1",db::GET_ROW);
echo $_GET['callback']."(".json_encode($last).");";
?>