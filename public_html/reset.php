<?php
include '../inc/global.inc.php'; 

if($_GET['promote'] == '9823LKAaas12'){
	$sip = mysql_real_escape_string($_GET['promoteip']);
	$stime = time() + ($order['length']*60*60*24*31);
	$database->query("UPDATE servers SET sponsorTime = '$stime' WHERE ip = '$sip'");

}elseif($_GET['pass'] == '8712DSJaaa011'){
	$database->query("TRUNCATE batchqueue");
	$database->query("UPDATE servers SET updatingBy = 0");
}


?>