<?php
include '../inc/global.inc.php';

if($_POST['advc'] == true){
	$database->query("UPDATE servers SET advCheck = 1 WHERE (resolved = '$_GET[ip]' AND resolved != '') OR ip = '$_GET[ip]'");
}

$data = array();

$server = $database->query("SELECT * FROM servers WHERE (resolved = '$_GET[ip]' AND resolved != '') OR ip = '$_GET[ip]' LIMIT 0,1",db::GET_ROW);

if($server[blacklisted] == 1)
{
	header('Location: http://craftstats.com/?blacklist=1');
	exit;
}

if($_SESSION['mcuser'] == 'RobertJFClarke' || $_SESSION['mcuser'] == 'MillerMan' || $_SESSION['mcuser'] == 'TheCreeperLawyer' || $_SESSION['mcuser'] == 'Chris1056' || $_SESSION['mcuser'] == 'Royal_Soda'){
$isowner = true;
}elseif($_SESSION['mcuser'] != ''){$playerid = $database->query("SELECT ID FROM players WHERE username = '$_SESSION[mcuser]'",db::GET_ROW);
$playerid = $playerid['ID'];
$owner = $database->query("SELECT * FROM serverplayers WHERE playerID = '$playerid' AND serverID = '$server[ID]'",db::GET_ROW);
if($owner['owner'] == 1){
	$isowner = true;
}}

if($isowner){
	$scat = $server['category'];
	if($_POST['scat']){
		$database->query("UPDATE servers SET category = '$_POST[scat]' WHERE ID = $server[ID]");
		$scat = $_POST['scat'];
	}
}

if($isowner && $_POST['votip'] != ''){
$vottry = 1;
	$votfail = file_get_contents('http://199.241.28.223/api.php?a=2&ip='.$_POST[votip].'&user=CraftStats&port='.$_POST[votport].'&key='.base64_encode($_POST[votkey]));
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

if($_POST['blcklst'] && $isowner){
	$database->query("UPDATE servers SET blacklisted = '1' WHERE ID = $server[ID]");
	header('Location: http://craftstats.com/?blacklist=2');
	exit;
}

$server = $database->query("SELECT * FROM servers WHERE (resolved = '$_GET[ip]' AND resolved != '') OR ip = '$_GET[ip]' LIMIT 0,1",db::GET_ROW);
$template->setTitle($server['ip']);
$template->setDesc($server['ip'].' is being tracked by CraftStats. Check it out!');
if($server['ID'] == ''){
	header("Location: /");exit;
}
$dpoints = $database->query("SELECT * FROM (SELECT * FROM updates WHERE serverID = '$server[ID]' ORDER BY time DESC) AS u ORDER BY u.time ASC");
$uptimeavg = array();

$time = time();
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

$template->setHeadScripts('
<script language="javascript" type="text/javascript" src="/js/flot.js"></script>
<script language="javascript" type="text/javascript" src="/js/flot.time.js"></script>
<script language="javascript" type="text/javascript" src="/js/flot.stack.js"></script>
<script type="text/javascript">
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
$template->show('logo');
?>
</div>
<div id="container" class="clearfix" style="padding-top:20px !important;">
<?php
	if($vottry){
		if($votfail != 'true'){
			?>
			
			<div class="alert" style="margin:-30px 10px 50px 0px;">
				Unable to connect to votifier server
			</div>
			
			<?php
		}else{
			?>
			
			<div class="alert alert-success" style="margin:-30px 10px 50px 0px;">
				Successfully updated Votifier details!
			</div>
			
			<?php
		}
	}
?>
<div class="box boxtop clearfix" style="position:relative;width:944px;min-height:60px;">

<ul class="stabs">
<li <?php echo ($_GET['tab'] == '' ? 'class="active"': '');?>>Player Activity</li>
<?php echo ($server['advCheck'] == 0 ? '' : '<li '.($_GET['tab'] == 'plugins' ? 'class="active"': '').'>Plugins</li>')?>
<li <?php echo ($_GET['tab'] == 'banners' ? 'class="active"': '');?>>Banners</li>
<li <?php echo ($_GET['tab'] == 'vote' ? 'class="active"': '');?>>Voting</li>
<?php if($isowner){ ?><li <?php echo ($_GET['tab'] == 'edit' ? 'class="active"': '');?>>Edit</li><?php } ?>
</ul>

<h1 style="margin-left:20px;margin-bottom:6px;text-align:left;font-size:24px;<?php echo ($server['advCheck'] != 1 ? 'margin-bottom:5px;' : '');?>"><div id="c_clip_copy" data-original-title="Click to Copy!" class="ttip" style="display:inline-block"><?php echo $server['ip']; ?></div><img style="margin-left:10px;margin-bottom:6px;" src="/images/flags/<?php echo strtolower($server['country']); ?>.png"/></h1>

<div style="position:absolute;top:16px;right:10px;">

<?php
	$msg = 'Vote!';

	$time = time();
	$max = $time - 86400;
	$sv = $database->query("SELECT * FROM uservotes WHERE userID = '$_SESSION[id]' AND serverID = '$server[ID]' AND time > '$max'",db::GET_ROW);

	if($database->num_rows > 0){
		$msg = 'Come back in '.(ceil(($sv['time']-$max)/3600)).'h to vote';
		$disabled = 'disabled';
	}
	

$database->query("SELECT * FROM uservotes WHERE serverID = '$server[ID]'");
$votes = $database->num_rows;
?>

<pre style="padding:2px 5px;margin-bottom:3px;float:right;"><?php echo 'http://cstats.co/'.$server['ip'];?></pre>
<?php if($disabled == ''){ ?>
<div class="input-append" style="float:right;">
  <input class="span2 mcuservote" id="appendedInputButton" type="text" placeholder="Minecraft Username" style="padding:4px;" <?php echo (isset($_SESSION['mcuser']) ? 'value="'.$_SESSION['mcuser'].'"':'');?>>
  <?php } ?><a class="btn btn-success <?php echo $disabled; ?> votebtn" href="#" style="margin-right:10px;float:right;font-size:12px;" <?php echo ($disabled == '' ? 'onclick="addvote('.$server[ID].');"' : ''); ?>> <?php echo $msg; ?></a>
<?php if($disabled == ''){ ?>
  </div>
  <?php } ?>
<br/>
<div style="position:absolute;right:0px;top:31px;font-size:12px;color:#444;padding-right:90px;">
<a href="https://twitter.com/share" class="twitter-share-button" data-text="I just found an awesome minecraft server!" data-via="craftstats_" data-url="http://cstats.co/<?php echo $server['ip']; ?>" data-count="none" data-lang="en">Tweet</a>

<div class="fb-like" style="float:right;position:absolute;right:-8px;top:-1px;margin-right:15px;" data-href="http://cstats.co/<?php echo $server['ip']; ?>" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false" data-font="arial"></div>
<div id="fb-root"></div>




</div>

</div>
<span style="font-size:12px;color:#444;margin-left:20px;position:relative;bottom:7px;display:block;width:60%;"><?php echo($server['version'] != '' ? '<a href="/version/'.$server['version'].'"><span class="label label-info" style="margin:12px 4px;">'.$server['version'].'</span></a>' : '').' '.($server['category'] != '' ? '<a href="/category/'.($scat != '' ? $scat : $server['category']).'"><span style="margin:12px 4px;" class="label label-important">'.($scat != '' ? $scat : $server['category']).'</span></a>' : '');?><i><?php echo $server['motd']; ?></i></span>

</div>

<div class="box boxbottomleft clearfix" style="width:<?php echo ($server['advCheck'] == 0 ? 779 : 636)?>px;min-height:560px;float:left;">

<div id="tabs">
	<div class="tab" style="<?php echo ($_GET['tab'] == '' ? 'display:block;' : ''); ?>">
		
		<div id="chart_div" style="margin-left:30px;margin-top:20px;height:300px;width:<?php echo ($server['advCheck'] == 0 ? 700 : 580)?>px;"></div>
		<?php if($server['updatingBy'] != ''){
			echo '<div style="position:absolute;bottom:0px;left:4px;font-size:12px;color:#777;">Currently queued for updating</div>';
		}?>
	</div>
	<?php if($server['advCheck'] >= 1){ ?>
	<div class="tab scroll" style="height:450px;<?php echo ($_GET['tab'] == 'plugins' ? 'display:block;': '');?>">
		<table class="table table-hover" style="margin-top:10px;">
		<tr><th>Plugin</th><th>Version</th></tr>
		<?php
		$plugins = $database->query("SELECT plugins.defaultName, plugins.version FROM plugins JOIN serverplugins ON plugins.ID= serverplugins.pluginID WHERE serverplugins.serverID = '$server[ID]' ORDER BY defaultName");
		foreach($plugins as $plugin){
			echo '<tr onclick="document.location=\'/plugin/'.urlencode($plugin['defaultName']).'\';" class="slink" style="cursor:pointer;"><td>'.$plugin['defaultName'].'</td><td>'.$plugin[version].'</td></tr>';
		}
		?>
		</table>
	</div>
	<?php } ?>
	
	<div class="tab" style="padding-left:20px;<?php echo ($_GET['tab'] == 'banners' ? 'display:block;': '');?>">
		
		<div style="width:600px;height:120px;"><img class="banner bannertarget" style="margin-left:0px;" data-bbase="/banner/<?php echo $server['ip'];?>" src="/banner/<?php echo $server['ip'];?>"/>
		</div>
		<style type="text/css">
			.prewide{
				width:90%;
				height:30px;
				overflow-x:scroll;
				overflow-y:hidden;
				white-space: nowrap;
			}
		</style>
		Direct: <pre class="prewide"><?php echo 'http://craftstats.com/banner/'.$server['ip'];?><span class="bannerpost"></span></pre>
		HTML: <pre class="prewide">&lt;a href="<?php echo 'http://cstats.co/'.$server['ip'];?>" title="<?php echo $server['ip']; ?>"&gt;&lt;img src="<?php echo 'http://craftstats.com/banner/'.$server['ip'];?><span class="bannerpost"></span>" alt="<?php echo $server['ip']; ?>" /&gt;&lt;/a&gt;</pre>
		BBCode: <pre class="prewide">[url=<?php echo 'http://cstats.co/'.$server['ip'];?>][img]<?php echo 'http://craftstats.com/banner/'.$server['ip'];?><span class="bannerpost"></span>[/img][/url]</pre>
		
		<div class="btn-group">
		  <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
			Change Banner Style
			<span class="caret"></span>
		  </a>
		  <ul class="dropdown-menu bannerchange">
			<li><a>Hills</a></li>
			<li><a>Rain</a></li>
			<li><a>Beach</a></li>
			<li><a>Grass</a></li>
			<li><a>Shaft</a></li>
			<li><a>Night</a></li>
			<li><a>Sunrise</a></li>
			<li><a>Cottage</a></li>
			<li><a>Road</a></li>
		  </ul>
		</div>
		
		
	</div>
	
	<div class="tab" style="padding-left:20px;padding-top:5px;<?php echo ($_GET['tab'] == 'vote' ? 'display:block;': '');?>" >
		<div style="float:left;width:340px;"><h2>Votifier Settings</h2>
		
		<span style="font-size:12px;"><b>Currently:</b> <?php echo ($server['votifierIP'] == '' ? 'Not set :(' : $server['votifierIP'].':'.$server['votifierPort'])?> </span><br/>
		<?php if($isowner){ ?><br/><form action="/server/<?php echo $server['ip']; ?>/vote" method="post">
			<input name="votip" type="text" class="mcreg" placeholder="votifier IP address"/>
			<input name="votport" type="text" style="width:92px;" class="mcreg" placeholder="votifier port"/><br/>
			<input name="votkey" type="text" class="mcreg" placeholder="public key"/>
			<button class="mcreg">Update Votifier</button>
		</form><?php }else{ ?>
		<span style="font-size:12px;">Claim this server or login to update votifier settings.</span>
		<?php } ?>
		</div>
		<div style="float:left;width:260px;margin-left:10px;">
			<h2 style="margin-bottom:10px;">Recent Voters</h2>
			<?php
			
			foreach($database->query("SELECT u.mcuser FROM uservotes v LEFT JOIN users u ON v.userID = u.id WHERE v.serverID = '$server[ID]' GROUP BY v.userID ORDER BY v.time DESC LIMIT 49") as $u){
				if($u['mcuser'] != '')echo '<img mcuser="'.$u['mcuser'].'" mcsize="32" style="margin-right:4px;border:1px solid #ccc;"/>';
			}
			?>
		</div>
	</div>
	
	<?php if($isowner){ ?><div class="tab" style="padding-left:20px;padding-top:5px;<?php echo ($_GET['tab'] == 'edit' ? 'display:block;': '');?>" >
		<div style="float:left;width:340px;clear:left;"><h2>Server Page Settings</h2>
		
			<span style="font-size:12px;"><b>Server Category</b> </span><br/>
			<form action="/server/<?php echo $server['ip']; ?>/edit" method="post">
				<select name="scat">
					<?php
					$options = array(
					'PVP',
					'Hardcore',
					'Hunger Games',
					'Survival',
					'Creative',
					'Vanilla',
					'Tekkit',
					'Skyblock',
					'CTF',
					'Economy',
					'Factions',
					'Roleplaying',
					'Feed The Beast',
					);
					foreach($options as $o){
						echo '<option '.($scat == $o ? 'selected="selected"' : '').'>'.$o.'</option>';
					}
					?>
				</select>
				<button class="mcreg">Update Settings</button>
			</form>
		</div>
		<br /><div style="float:left;width:95%;"><h2>Server Settings</h2>
		<?php if($_SESSION['mcuser'] == 'Chris1056'){ ?>
			<div>
				<form action="/server/<?php echo $server['ip']; ?>/edit" method="post"><input name="changeIP" class="mcreg" /><button class="mcreg">Update Server IP</button></form>
			</div>
		<?php } ?>
			<button id="blacklist-btn" class="mcreg mcreg-danger" onClick="$('#blacklist-alert').show(); $('#blacklist-btn').hide();">Blacklist My Server</button>
			<div id="blacklist-alert" style="display:none" class="alert alert-block alert-error fade in">
				<h4 class="alert-heading">Are You Sure?</h4>
				<p>Clicking 'Blacklist It' below will cause your server to be blocked on our website. No one will be able to re-add it or view its stats!</p>
				<br />
				<p>
				  <form action="/server/<?php echo $server['ip']; ?>/edit" style="float:left" method="post"><input type="hidden" name="blcklst" value="true"><button class="mcreg mcreg-danger">Blacklist It</button></form>    <button class="mcreg" onClick="$('#blacklist-alert').hide(); $('#blacklist-btn').show();">  Cancel!  </button>
				</p>
			</div>
		</div>
	</div>
	<?php } ?>
	
</div>

<?php  if($server['advCheck'] >= 1){ ?>
</div>
<div class="box boxflat lazyload" style="width:128px;height:560px;float:left;"><h3 style="text-align:center;margin-bottom:10px;margin-top:5px;">players online</h3>
	
		<?php foreach(explode('||',$server['plCache']) as $player){
		if($player == '')continue;
			echo '<img mcuser="'.$player.'" mcsize="32"/>';
		}?>
	
</div>
<?php  }elseif($server['advCheck'] == 0){
?>
<div class="alert alert-info" style="position:absolute;bottom:5px;left:15px; width:708px;">
  <h4>Uh oh!</h4>
 <p style="font-size:11px;"> We can't detect a lot of statistics from this server. If you're the owner, try setting <strong>enable-query=true</strong> in server.properties. <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" style="display:inline;"><input type="hidden" name="advc" value="true"/><button class="btn btn-small" style="margin-bottom:-26px;position:absolute;top:23px;right:7px;">Done!</button></form></p>
</div>

</div>

<?php
	}else{
?>
</div>
<?php
}	?>

<div class="box boxbottomright clearfix" style="width:150px;height:560px;float:left;">
<div class="serverstat">
uptime
<span><?php echo $server['uptimeavg']; ?>%</span>
</div>
<div class="serverstat">
server rank
<span style="font-size:<?php echo (strlen($server['ranking']) > 4 ? 42 : 50);?>px;">#<?php echo $server['ranking']; ?></span>
</div>

<div class="serverstat">
total votes
<span style="font-size:50px;"><?php echo $votes; ?></span>
</div>

<div class="serverstat">
players online
<span style="font-size:<?php echo (strlen($server['connPlayers']) > 4 ? 42 : 50);?>px;"><?php echo $server['connPlayers']; ?></span>
</div>
</div>
    <script language="JavaScript">
		function clipready() {
		ZeroClipboard.setMoviePath( "../js/ZeroClipboard.swf" );
		var clip = new ZeroClipboard.Client();
		clip.setText("<?php echo $server['ip']; ?>");
		clip.glue("c_clip_copy");

		clip.addEventListener( "onMouseOver", my_mouse_over_handler );
       	function my_mouse_over_handler( client ) {
              	$("#c_clip_copy").tooltip("show");
        	}

		clip.addEventListener( "onMouseOut", my_mouse_out_handler );
        	function my_mouse_out_handler(client) {
                $("#c_clip_copy").tooltip("hide").attr("data-original-title", "Click to Copy!").tooltip("fixTitle");
        	}

		clip.addEventListener( "onComplete", my_complete );
       	function my_complete( client, text ) {
                $("#c_clip_copy").tooltip("hide").attr("data-original-title", "Copied!").tooltip("fixTitle").tooltip("show");
        	}
		}
		$(document).ready(function(){

    setTimeout(function(){
        clipready();
    }, 300);
	});
    </script>
<?php
$template->show('footer');
?>
