<?php
include '../inc/global.inc.php';
if($_SESSION['username'] == ''){
	header("Location: /login");
}
$template->setTitle('Manage Servers');
$template->show('header');
$template->show('nav');

?>
<div class="row">
	<div class="twelve columns">
		<?php if($_POST['claimip'] != ''){
$cip = mysql_real_escape_string($_POST['claimip']);
	$server = $database->query("SELECT * FROM servers WHERE ((resolved = '$cip' AND resolved != '') OR ip = '$cip') AND game = 'minecraft'",db::GET_ROW);
	if($database->num_rows == 0 ){
		?>
		<div class="alert-box" style="margin-top:20px;">
			We're not currently tracking that server! Make sure you entered the IP address correctly and try again.
		</div>
		<?php
	}else{
	
		$sp = $database->query("SELECT * FROM serverowners WHERE userID = '$_SESSION[id]' AND serverID = '$server[ID]'",db::GET_ROW);
		if($database->num_rows >= 1){
			?>
			<div class="alert-box" style="margin-top:20px;">
				You're already an owner of <?php echo $server['ip']; ?>.
			</div>
			<?php
		}else{
			$vs = 'CS'.$server['ID'].'-'.$_SESSION['id'];
			$ping = $api->pingServer($server['ip'],1);
			if(stristr($ping['info']['HostName'],$vs)){
				?>
				<div class="alert-box success" style="margin-top:20px;">
					We have successfully verified your ownership of <?php echo $server['ip']; ?>!
				</div>
				<?php
				
				$database->query("INSERT INTO serverowners VALUES('$server[ID]','$_SESSION[id]')");
				
			}elseif($ping['fail'] == true){
				?>
				<div class="alert-box" style="margin-top:20px;">
					We were unable to contact <?php echo $server['ip']; ?> to verify your ownership.
				</div>
				<?php
			}else{
				?>
				<div class="alert-box" style="margin-top:20px;">
					To verify your ownership of this server, add '<?php echo $vs; ?>' to the MOTD and try to claim the server again.
				</div>
				<?php
			}
		}
	}
}
?>
		<div class="twelve columns box">
			<div class="six columns">
			<h3>Manage your servers</h3>
			</div>
					<div class="six columns">
			
				<div class="row collapse" style="margin-top:15px;">
					<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" >
						<div class="seven mobile-three columns">
							<input type="text" name="claimip" placeholder="Server IP"/>
						</div>
						<div class="five mobile-one columns">
							<button class="button expand postfix" style="padding:0px 5px;">Claim Server</button>
						</div>
					</form>
				</div>

			</div>	
		</div>
	</div>
</div>
<div class="servers">
	<div class="row table">
		<div class="twelve columns">
			<table class="twelve">
				<thead>
					<tr>
						<th>Server Name</th>
						<th>Uptime</th>
						<th>Players</th>
						<th>Last Updated</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					
					<?php $so = $database->query("SELECT * FROM serverowners AS so LEFT JOIN servers AS s ON s.ID = so.serverID WHERE so.userID = '$_SESSION[id]' AND s.ID != '' ORDER BY s.lastUpdate DESC");
					
					$time = time();
					foreach($so as $server){
						echo '
						<tr onclick="document.location=\'/server/'.$server['ip'].'\';" class="slink '.($server['uptime'] <= 0 ? 'down':'').'">
						<td>'.($server['name'] != '' ? $server['name'] : $server['ip']).'</td>
						<td><span style="padding:3px 0px;display:block;width:50px !important;text-align:center;" class="button tiny '.($server['uptime'] <= 0 ? 'alert' : ($server['uptimeavg'] > 90 ? 'success' : ($server['uptimeavg'] > 70 ? 'secondary' : ($server['uptimeavg'] > 50 ? 'secondary' : 'alert')))).'">'.($server['uptime'] <= 0 ? 'down' : $server['uptimeavg'].'%').'</span></td>
						<td>'.$server['connPlayers'].' / '.$server['maxPlayers'].'</td>
						<td>'.($time - $server['lastUpdate'] > 60 ? round(($time - $server['lastUpdate'])/60).'m' : $time - $server['lastUpdate'].'s').' ago</td>
						<td><a href="/server/'.$server['ip'].'/edit" class="button small">Edit</a></td>
					</tr>
						';
					}
					
					if(count($so) == 0){
					?>
					<tr>
					<td>You have not claimed any servers.</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					</tr>
					<?php
					}
					?>
				</tbody>
			</table>
			
		</div>
		
	</div>
</div>
<?php
$template->show('footer');
?>