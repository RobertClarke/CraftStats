<?php
include 'inc/global.inc.php';
$database->query("SELECT * FROM users WHERE id = '$_SESSION[id]' AND admin = 1");
if($database->num_rows == 0){
	echo 'You\'re not an admin. Go away please.';exit;
}
$template->setTitle('Admin');
$template->show('header');
$template->show('nav');

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

if($_POST){
$memcache->set(md5('announce'),stripslashes($_POST['ann']),MEMCACHE_COMPRESSED,60*60*36);
}
?>
<div class="row">
	<div class="twelve columns">
	<a href="http://minecraftservers.com/reset.php?pass=8712DSJaaa011" class="button" style="margin-top:30px;">Reset slaves</a>
	<div class="twelve columns box">
		<h4>Set announcement</h4>
		<form action="/admin" method="post">
		
			<textarea name="ann" placeholder="Announcement message"></textarea>
			<button type="submit" class="button">Set</button>
		</form>
	</div>
<style type="text/css">
td{
padding:5px;
text-align:right;
}
body{
	font-family:'arial';
}
</style>
<div class="servers">
	<div class="row table">
		<div class="twelve columns">
			<table class="twelve">
<thead>
<tr>
	<th><strong>Link</strong></th>
	<th><strong>Uniques today</strong></th>
	<th><strong>Week</strong></th>
	<th><strong>Month</strong></th>
	</tr>
	</thead>

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
			
		</div>
		
	</div>
</div>
</div>
</div>
<?php
$template->show('footer');
?>
