<?php
include 'inc/global.inc.php';

$template->setTitle('CraftStats Admin');
$template->show('header');
$template->show('nav');

$database->query("SELECT * FROM users WHERE id = '$_SESSION[id]' AND admin = 1");
if($database->num_rows == 0){
	echo '</br><strong>For site issues, please contact robert@rjfc.net, for the quickest response time.</br>Also follow us on Twitter @CraftStats_, we can provide some support there also.</strong>';exit;
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

if($_POST['setann']){
$memcache->set(md5('announce'),stripslashes($_POST['ann']),MEMCACHE_COMPRESSED,60*60*36);
}
if($_POST['addowner']){
	$u = $database->query("SELECT * FROM users WHERE username = '$_POST[user]'",db::GET_ROW);
	if($database->num_rows == 0)$addownerfail=true;
	$s= $database->query("SELECT * FROM servers WHERE ip = '$_POST[server]'",db::GET_ROW);
	if($database->num_rows == 0)$addownerfail=true;
	
	if(!$addownerfail){
		$database->query("INSERT INTO serverowners VALUES ('$s[ID]','$u[id]')");
		
	}
}
?>
<div class="row">
	<div class="twelve columns">
	<a href="http://minecraftservers.com/reset.php?pass=8712DSJaaa011" class="button" style="margin-top:30px;">Reset slaves</a>
	<div class="twelve columns box">
		<h4>Set announcement</h4>
		<form action="/admin" method="post">
			<input type="hidden" name="setann" value="1"/>
			<textarea name="ann" placeholder="Announcement message"></textarea>
			<button type="submit" class="button">Set</button>
		</form>
	</div>
	<div class="twelve columns box">
		<h4>Add owner</h4>
		<form action="/admin" method="post">
			<input type="hidden" name="addowner" value="1"/>
			<?php if($addownerfail){ ?><div class="alert-box alert" style="margin-top:20px;">
					User or server does not exist
			</div><?php }elseif($_PSOT['addowner']){ ?>
<div class="alert-box success" style="margin-top:20px;">
					User '<?php echo $u['username']; ?>' is now an owner of '<?php echo $s['ID']; ?>'
			</div>
			<?php } ?>
			<div class="four columns"><input name="user" placeholder="Username" type="text"/></div>
			<div class="four columns"><input name="server" placeholder="Server IP" type="text"/></div>
			<button type="submit" class="button">Add as Owner</button>
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
