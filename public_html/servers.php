<?php
include '../inc/global.inc.php';
$time = time();
if($_POST['advc'] == true){
	$database->query("UPDATE servers SET advCheck = 1 WHERE (resolved = '$_GET[ip]' AND resolved != '') OR ip = '$_GET[ip]'");
}

$data = array();

$server = $database->query("SELECT * FROM servers WHERE (resolved = '$_GET[ip]' AND resolved != '') OR ip = '$_GET[ip]' LIMIT 0,1",db::GET_ROW);
$sname = $server['name'];
$scat = $server['category'];
$template->setDesc($server['ip'].' | '.($sname != '' ? $sname.' | ':'').''.$server['connPlayers'].' players online | '.($scat != '' ? 'Minecraft '.($server['version'] != '' ? $server['version'].' ' : '').''.$scat.' server | ':'').'Come join one of the minecraft servers on Minecraft Servers today!');

$template->setKeys(($scat != '' ? 'minecraft '.$scat.' server, ':'').($scat != '' ? 'mc '.$scat.' server, ':'').' minecraft '.($server['version'] != '' ? $server['version'].' ' : '').'servers, '.($scat != '' ? 'minecraft '.$server['version'].' '.$scat.' servers, ':'').' '.($scat != '' ? 'minecraft '.$server['version'].' '.$scat.' server ':''));
if($server[blacklisted] == 1)
{
	header('Location: http://minecraftservers.com/?blacklist=1');
	exit;
}
$database->query("SELECT * FROM users WHERE id = '$_SESSION[id]' AND admin = 1");
if($database->num_rows == 1){
$isowner = true;
}else{
$owner = $database->query("SELECT * FROM serverowners WHERE userID = '$_SESSION[id]' AND serverID = '$server[ID]'",db::GET_ROW);
if($database->num_rows >= 1){
	$isowner = true;
}}


$bannerurl = $server['bannerurl'];
if($isowner && $_POST['scat']){
	$database->query("UPDATE servers SET category = '$_POST[scat]',graphshow = '$_POST[gshow]', name = '$_POST[sname]', bannerurl='$_POST[bannerurl]' WHERE ID = $server[ID]");
	$scat = $_POST['scat'];
	$sname = $_POST['sname'];
	$bannerurl = $_POST['bannerurl'];
}

if($isowner && $_POST['votip'] != ''){
$vottry = 1;
	$votfail = file_get_contents('http://192.119.145.28/api.php?a=2&ip='.$_POST[votip].'&user=CraftStats&port='.$_POST[votport].'&key='.base64_encode($_POST[votkey]));
	if($votfail == 'true')$database->query("UPDATE servers SET votifierIP = '$_POST[votip]', votifierPort = '$_POST[votport]', votifierKey = '$_POST[votkey]' WHERE (resolved = '$_GET[ip]' AND resolved != '') OR ip = '$_GET[ip]'");
}

/*
if($_POST['changeIP'] && $isowner && $_SESSION['mcuser'] == 'Chris1056'){
	$resolved = gethostbyname($server['changeIP']);
	$database->query("UPDATE servers SET ip = '$_POST[changeIP]', resolved = '".$resolved."' WHERE ID = $server[ID]");
	header('Location: http://craftstats.com/server/'.$_POST['changeIP']);
	exit;	
}
*/

if($isowner && $_GET['tab'] == 'blacklist'){
	$database->query("UPDATE servers SET blacklisted = '1' WHERE ID = $server[ID]");
	header('Location: http://minecraftservers.com/?blacklist=2');
	exit;
}

$server = $database->query("SELECT * FROM servers WHERE (resolved = '$_GET[ip]' AND resolved != '') OR ip = '$_GET[ip]' LIMIT 0,1",db::GET_ROW);

if($_GET['tab'] == 'vote'){
	$template->setTitle('Vote for '.($server['name'] ? $server['name'] : $server['ip']));
}else{
	$template->setTitle($server['ip']);
}
if($server['ID'] == ''){
	header("Location: /?sf=1");exit;
}
$dpoints = $database->query("SELECT * FROM (SELECT * FROM updates WHERE serverID = '$server[ID]' ORDER BY time DESC) AS u ORDER BY u.time ASC");
$uptimeavg = array();

$time = time();

if(count($dpoints) > 1){
foreach($dpoints as $n => $update){
	if($time - $update['time'] < 604800){
		array_push($data,array(($update[time]*1000), ($update[ping] > 0 ? $update[connPlayers] : 'null') ,($update[ping] > 0 ? $update[maxPlayers] : 'null')));
	}
}

foreach($data as $row){
	$r0 .= "{$frst}[$row[0], $row[1]]";
	$r1 .= "{$frst}[$row[0], $row[2]]";
	$frst = ',';
}

$series1 = '{data:['.$r1.'],label:"Max Players",color:"#cdcdcd"},{data:['.$r0.'],label:"Players Online",color:"#3A87AD",hoverable:true}';
}
$template->setHeadScripts('
<script language="javascript" type="text/javascript" src="/js/flot.js"></script>
<script language="javascript" type="text/javascript" src="/js/flot.time.js"></script>
<script language="javascript" type="text/javascript" src="/js/flot.stack.js"></script>
<script language="javascript" type="text/javascript" src="/js/cstats.js"></script>
<script type="text/javascript">
'.(count($dpoints) > 1 ? '
$(document).ready(function() {
$.plot($("#chart_div"), ['.$series1.'],{
grid:{
		labelMargin:20,
		borderWidth:1
	},
	series:{
		lines: { show: true, fill: true, steps: false }
	},xaxis:{
		mode:"time",
      timeformat: "%a"
	},
	legend:{
		position:"nw"
	}

});
});
':'').'
</script>
	<script type="text/javascript" src="/js/ZeroClipboard.js"></script>
	
 	<script type="text/javascript">
	$(document).ready(function() {
		$(window).load(function(){
		
			!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
			(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=151420601618450";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));
		});
	});
</script>');
$template->show('header');
$template->show('nav');
$database->query("SELECT * FROM uservotes WHERE serverID = '$server[ID]'");
$votes = $database->num_rows;
?>
<div class="row">
	<div class="twelve columns">
		<?php
			if($server['uptime'] <= 0 && count($dpoints) > 1){
			?>
				<div class="alert-box secondary" style="margin-top:20px;text-align:center;font-size:16px;padding:10px;">
				<?php $rs = $database->query("SELECT ip FROM servers WHERE sponsorTime > UNIX_TIMESTAMP() AND uptime > 1 ORDER BY RAND(NOW()) LIMIT 1",DB::GET_ROW); ?>
				  This server is currently offline, <a href="/server/<?php echo $rs['ip']; ?>">here's another while you're waiting</a>.
				  <a href="" class="close">&times;</a>
				</div>
			<?php
			}
		?>
		
			<?php
		if($vottry){
			if($votfail != 'true'){
				?>
				
				<div class="alert-box" style="margin-top:20px;">
					Unable to connect to votifier server
				</div>
				
				<?php
			}else{
				?>
				
				<div class="alert-box success" style="margin-top:20px;">
					Successfully updated Votifier details!
				</div>
				
				<?php
			}
		}
		if($isowner && $_POST['scat']){
		?>
		<div class="alert-box success" style="margin-top:20px;">
					Successfully updated server info. There may be a delay for changes to take effect.
				</div>
		<?php
		
		}
		
		if($_GET['tab'] == 'voted'){
		?>
		<div class="alert-box success" style="margin-top:20px;">
					You've voted for <?php echo $server['ip']; ?>!
				</div>
		<?php
		
		}
	?>
	<?php 
	if($_POST['del']){
		?>
		<div class="alert-box alert" style="margin-top:20px;">
					Are you sure you want to delete and blacklist this server? <a href="/server/<?php echo $server['ip']; ?>/blacklist" style="color:#fff;">Click here to continue.</a>
				</div>
		<?php
		
		}
	?>
		<div class="twelve columns box">
			<div class="seven columns">
				<h1 style="font-size:14px;margin-bottom:-17px;<?php if($server['motd'] == ''){echo 'margin-top:20px;';} ?>">  <?php echo $server['ip']; ?></h1> <h5><small><?php if($server['motd']!=''){ echo $server['motd'];} ?></small></h5>
			</div>
			<?php if($_GET['tab'] == ''){ ?>
			<div class="row">
				<div class="five columns" style="padding-top:12px;">
					<div class="row collapse">
					<a class="button expand postfix" href="/server/<?php echo $server['ip']; ?>/vote">Vote for this server</a>
					</div>
				</div>
			</div>
			<?php }elseif($_GET['tab'] == 'edit'){
?>
<div class="row">
				<div class="five columns" style="padding-top:12px;">
					<div class="row collapse">
					<a class="button expand postfix" href="/myservers">Back to my servers</a>
					</div>
				</div>
			</div>
<?php
				}else{
?>
<div class="row">
				<div class="five columns" style="padding-top:12px;">
					<div class="row collapse">
					<a class="button expand postfix" href="/server/<?php echo $server['ip']; ?>">Back to server page</a>
					</div>
				</div>
			</div>
<?php
				}			?>
		</div>
		
		<?php 
		if($_GET['tab'] == 'vote'){
			echo '<div class="twelve columns box"><img class="banner" src="'.($bannerurl ? $bannerurl : '/banner/'.$server['ip']).'" style="margin:10px auto;display:block;"/></div>';
		}
		?>
		<?php if($_GET['tab'] != 'edit'){ ?>
		<div class="twelve columns box"style="padding:10px;text-align:center;">
			<?php 
			echo '<h1 style="font-size:16px;">'.($_GET['tab'] == 'vote' ? 'Vote for ':'').''.($sname ? $sname : $server['ip']).'</h1>'; ?>
			<?php if($_GET['tab'] != 'vote'){ if(time() < $server['sponsorTime']){ echo ' <div style="text-align:center;color:#aaa;font-size:11px;'.($sname != '' ? 'margin-top:-8px;' :'').'">SPONSORED SERVER</div>';} ?>
		<div class="row">
			<div class="four columns">
			<h5><small>
			<?php echo ($scat != '' ? 'minecraft '.strtolower($scat).' server':'minecraft survival server'); ?>
			</small></h5>
			</div>
			<div class="four columns">
			<h5><small>
			<?php if($server['version']!=''){ echo 'currently running version '.$server['version']; }else{ echo 'currently running custom version'; }?>
			</small></h5>
			</div>
			<div class="four columns">
			<h5><small>
			<?php echo $server['connPlayers']; ?> players online <?php if($time - $server['lastUpdate'] < 1300442333 ){ ?> as of <?php echo ($time - $server['lastUpdate'] > 60 ? round(($time - $server['lastUpdate'])/60).'m' : $time - $server['lastUpdate'].'s'); ?> ago<?php } ?>
			</small></h5>
			</div>
			</div>
			<?php }else{ ?>
	
		
		
			<div class="row">
				<div class="six columns centered" style="padding-top:13px;">
					<div class="row collapse">
					<?php
						$time = time();
						$max = $time - 86400;
						$sv = $database->query("SELECT * FROM uservotes WHERE userID = '$_SESSION[id]' AND serverID = '$server[ID]' AND time > '$max'",db::GET_ROW);

						if($database->num_rows > 0){
							$msg = 'Come back in '.(ceil(($sv['time']-$max)/3600)).'h to vote';
							$disabled = 'disabled';
						}
						
						
						if($disabled == ''){
					?>	<form action="/api" method="get">
							<input type="hidden" name="req" value="m12"/>
							<input type="hidden" name="id" value="<?php echo $server['ID']; ?>"/>
							<div class="eight mobile-three columns">
								<input type="text" placeholder="Minecraft Username" name="usr"/>
							</div>
							<div class="four mobile-one columns">
								<button class="button expand postfix small">Vote</button>
							</div>
						</form>
					<?php
						}else{
						?>
						<a class="button expand small" style="margin-top:-4px;"><?php echo $msg; ?></a>
						<?php
						}
					?>
					</div>
				</div>
			</div><?php } ?>
		</div>
		<?php } if($_GET['tab'] != 'vote'){ ?>
		<div class="twelve columns box">
			<div class="three columns serverstat">
			<div>#<?php echo $server['ranking']; ?></div><br/>
			server rank
			</div>
			<div class="three columns serverstat">
			<div><?php echo $votes; ?></div><br/>
			votes this month
			</div>
			
			<div class="three columns serverstat">
			<div><?php echo $server['uptimeavg']; ?>%</div><br/>
			uptime
			</div>
			<div class="three columns serverstat">
			<div><?php echo $server['connPlayers']; ?></div><br/>
			players online
			</div>
			
			
		</div>
		<?php if($_GET['tab'] != 'edit'){ if($bannerurl || time() > $server['sponsorTime']){ ?>
		<div class="twelve columns box">
		<?php 
		if($bannerurl){
			echo '<img class="banner" src="'.$bannerurl.'" style="margin:10px auto;display:block;"/>';
		}
		?>
		
		<?php 
		$database->query("SELECT * FROM servers WHERE sponsorTime > UNIX_TIMESTAMP() && sponsorType = 0"); 
//Standard promotion stock
if($database->num_rows < 12){
	$instock = true;
}else{
	$instock = false;
}

//Premium promotion stock
$a = $database->query("SELECT * FROM servers WHERE sponsorTime > UNIX_TIMESTAMP() AND blacklisted != 1 AND sponsorType = 1 AND game = 'minecraft'"); 
if($database->num_rows >= 1){
	$instock2 = false;
}else{
	$instock2 = true;
}
if(time() > $server['sponsorTime'] && ($instock2 || $instock)){ ?>
			<div class="six columns centered">
				<a href="/promote?ip=<?php echo $server['ip']; ?>" class="button expand secondary" style="margin:10px 0px;">Sponsor this server</a>
			</div>
		<?php } ?>
		</div>
		<?php } ?>
		<?php if($server['graphshow']){ ?>
		<div class="twelve columns box">
			<div id="chart_div" style="margin:20px 0px;height:<?php echo(count($dpoints)<2?50:300);?>px;width:640px;text-align:center;">
				<?php if(count($dpoints < 2)){ ?>
					<div style="margin-top:50px;">We're currently gathering data for this server. Come back in a few minutes.</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
		<?php if($server['plCache'] != ''){ ?>
		<div class="twelve columns box" style="padding-bottom:20px;">
			<h5>Recently Active Players</h5>
			<?php foreach(explode('||',$server['plCache']) as $player){
		if($player == '')continue;
			echo '<img mcuser="'.$player.'" mcsize="32"/>';
		}?>
		</div>
		<?php } } 
		
		?>
		<div class="twelve columns box">
			<div style="width:600px;height:120px;margin:20px auto 0px auto;"><img class="banner bannertarget" style="margin-left:0px;" data-bbase="/banner/<?php echo $server['ip'];?>" src="/banner/<?php echo $server['ip'];?>"/>
			</div>
			<div class="button-bar" style="margin-bottom:20px;">
				<ul class="button-group bannerchange" style="margin: 0px auto;float:none;width:543px;">
					<li><a class="button secondary small">Hills</a></li>
					<li><a class="button secondary small">Rain</a></li>
					<li><a class="button secondary small">Beach</a></li>
					<li><a class="button secondary small">Grass</a></li>
					<li><a class="button secondary small">Shaft</a></li>
					<li><a class="button secondary small">Night</a></li>
					<li><a class="button secondary small">Sunrise</a></li>
					<li><a class="button secondary small">Cottage</a></li>
					<li><a class="button secondary small">Road</a></li>
				</ul>
			</div>

			<b>Direct:</b> <div class="panel embed"><?php echo 'http://minecraftservers.com/banner/'.$server['ip'];?><span class="bannerpost"></span></div>
			<b>HTML:</b><div class="panel embed">&lt;a href="<?php echo 'http://minecraftservers.com/server/'.$server['ip'];?>" title="<?php echo $server['ip']; ?>"&gt;&lt;img src="<?php echo 'http://minecraftservers.com/banner/'.$server['ip'];?><span class="bannerpost"></span>" alt="<?php echo $server['ip']; ?>" /&gt;&lt;/a&gt;</div>
			<b>BBCode:</b> <div class="panel embed">[url=<?php echo 'http://minecraftservers.com/server/'.$server['ip'];?>][img]<?php echo 'http://minecraftservers.com/banner/'.$server['ip'];?><span class="bannerpost"></span>[/img][/url]</div>
			
			
		</div>
		<div class="twelve columns box" ><h4>Server Settings</h4>
		
		<span style="font-size:12px;">
		<?php if($isowner){ ?>.
		<b>Votifier Settings:</b> <?php echo ($server['votifierIP'] == '' ? 'Not set :(' : $server['votifierIP'].':'.$server['votifierPort'])?> </span><br/><br/>
		<br/><form action="/server/<?php echo $server['ip']; ?>" method="post">
		<div class="row">
			<div class="four columns">
				<input name="votip" type="text" placeholder="votifier IP address"/>
			</div>
			<div class="four columns">
				<input name="votport" type="text"  placeholder="votifier port"/><br/>
			</div>
			<div class="four columns">
				<input name="votkey" type="text" placeholder="public key"/>
				<button class="button">Update Votifier</button>
			</div>
		</div>
			
			<!--<button id="blacklist-btn" class="mcreg mcreg-danger" onClick="$('#blacklist-alert').show(); $('#blacklist-btn').hide();">Blacklist My Server</button>
			<div id="blacklist-alert" style="display:none" class="alert alert-block alert-error fade in">
				<h4 class="alert-heading">Are You Sure?</h4>
				<p>Clicking 'Blacklist It' below will cause your server to be blocked on our website. No one will be able to re-add it or view its stats!</p>
				<br />
				<p>
				  <form action="/server/<?php echo $server['ip']; ?>/edit" style="float:left" method="post"><input type="hidden" name="blcklst" value="true"><button class="mcreg mcreg-danger">Blacklist It</button></form>    <button class="mcreg" onClick="$('#blacklist-alert').hide(); $('#blacklist-btn').show();">  Cancel!  </button>
				</p>
			</div>-->
		</form>
		</div>
		<div class="twelve columns box" >
		<h4>Server Page Settings</h4>
		
			
			<form action="/server/<?php echo $server['ip']; ?>" method="post">
				<div class="row">
				<span style="font-size:12px;"><b>Server Category</b> </span><br/>
				<div class="four columns"><select name="scat" style="margin:10px;">
					<?php
					$options = array(
					'Creative',
					'CTF',
          				'Drug',
					'Economy',
					'Factions',
					'Feed The Beast',
					'Hardcore',
					'Hub',
					'Hunger Games',
          				'Mindcrack',
                			'Parkour',
          				'Prison',
          				'PVE',
					'PVP',
					'Roleplaying',
					'Skyblock',
          				'Spoutcraft',
					'Survival',
					'Tekkit',
					'Vanilla',
					);
					foreach($options as $o){
						echo '<option '.($scat == $o ? 'selected="selected"' : '').'>'.$o.'</option>';
					}
					?>
				</select>
				</div>
				</div>
				<div class="row">
				<span style="font-size:12px;"><b>Show graphs</b> </span><br/>
				<div class="four columns"><select name="gshow" style="margin:10px;">
				<option <?php echo ($server['graphshow'] == 1 ? 'selected' : '');?> value="1">Yes</option>
				<option <?php echo ($server['graphshow'] == 0 ? 'selected' : '');?> value="0">No</option>
				</select>
				</div>
				</div>
				<div class="row">
					<span style="font-size:12px;"><b>Banner URL (max 600px width)</b> </span><br/>
				<div class="four columns">
				<input name="bannerurl" type="text" value="<?php echo $bannerurl; ?>"/>
				</div>
				</div>
				<div class="row">
				<span style="font-size:12px;"><b>Server Name</b> </span><br/>
				<div class="four columns">
				<input name="sname" type="text" value="<?php echo $sname; ?>"/>
				</div>
				<div class="four columns">
				<button class="button">Update Server Info</button>
				</div>
				</div>
			</form>
			<form action="/server/<?php echo $server['ip']; ?>" method="post">
				<div class="row">
				<div class="four columns offset-by-eight">
				<input type="hidden" name="del" value="1">
				<button class="button alert">Delete/blacklist server</button>
				</div>
				</div>
			</form>
			<?php }else{ ?>
		<span style="font-size:12px;">Claim this server or login to update settings.</span><br/><br/>
		<?php } ?>
		</div>
		<?php }else{
		
		?>
		
		<?php } ?>
	</div>
</div>
<?php

$template->show('footer');
?>
