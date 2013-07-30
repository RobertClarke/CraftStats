<?php
include 'inc/global.inc.php';
$database->query("SELECT * FROM users WHERE id = '$_SESSION[id]' AND admin = 1");
if($database->num_rows == 0){
	echo 'You\'re not an admin. Go away please.';exit;
}

$day = time()-60*60*24;
$week = time()-60*60*24*7;
$month = time()-60*60*24*31;
$day = $database->query("SELECT COUNT(DISTINCT user) AS c, link FROM referrals WHERE timestamp > $day GROUP BY link");
$week = $database->query("SELECT COUNT(DISTINCT user) AS c, link FROM referrals WHERE timestamp > $week GROUP BY link");
$month = $database->query("SELECT COUNT(DISTINCT user) AS c, link FROM referrals WHERE timestamp > $month GROUP BY link");
$links = array();
foreach($day as $r){

	if($r['c'] > 1)$links[$r['link']]['day'] = $r['c'];
}
foreach($week as $r){
	if($r['c'] > 1)$links[$r['link']]['week'] = $r['c'];
}
foreach($month as $r){
	if($r['c'] > 1)$links[$r['link']]['month'] = $r['c'];
}
?>
<style type="text/css">
td{
padding:5px;
text-align:right;
}
body{
	font-family:'arial';
}
</style>
<table>
<tr>
	<td><strong>Link</strong></td>
	<td><strong>Unique visitors - Day</strong></td>
	<td><strong>Week</strong></td>
	<td><strong>Month</strong></td>
	</tr>

<?php
foreach($links as $n => $l){
$n = urldecode($n);
	echo '<tr>
	<td>'.$n.'</td>
	<td>'.$l['day'].'</td>
	<td>'.$l['week'].'</td>
	<td>'.$l['month'].'</td>
	</tr>';
}
?>
</table>