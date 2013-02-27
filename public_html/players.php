<?php
include '../inc/global.inc.php';

if($_GET['skins'] == 1){
$template->setTitle('Player Skins');

$template->show('header');
$template->show('nav');
$template->show('logo');
	?>
<div style="margin-top:50px;" <?php echo ($_GET[nofade] == 1 ? '' : 'class="lazyload"'); ?>>
<?php
foreach($database->query("SELECT username FROM players ORDER BY RAND(MINUTE(NOW())) LIMIT 0,600") as $player){
	echo '<img style="margin-bottom:-7px;" src="http://mag.racked.eu/tools/avatar.php?size=32&forceImage&name='.$player['username'].'"/>';
}
?></div> 

<?php
}else{

if($_GET['name'] == ''){
$time = time();
if($_GET['zoom'] < 1)$_GET['zoom']=1;
$zoom = 172800 / $_GET['zoom'];
$dpoints = $database->query("SELECT time, online FROM serviceinfo WHERE time > $time - $zoom AND slaveThread = 0 AND slaveID = 3 ORDER BY time ASC");

$data = array();
foreach($dpoints as $update){
$cont = explode(':',$update['online']);
array_push($data,array(($update[time]*1000), (int)$cont[0],(int)$cont[1],(int)$cont[4],(int)$cont[2],(int)$cont[3]));
}


foreach($data as $row){
	$r0 .= $first.'['.$row[0].', '.$row[1].']';
	$r1 .= $first.'['.$row[0].', '.$row[2].']';
	$r2 .= $first.'['.$row[0].', '.$row[3].']';
	$r3 .= $first.'['.$row[0].', '.$row[4].']';
	$first = ',';
} 

$series1 = '{data:['.$r0.'],label:"Total Players Online"},{data:['.$r1.'],label:"Players on American Servers"},{data:['.$r2.'],label:"Players on European Servers"},{data:['.$r3.'],label:"Players on Asian Servers"}';

//$data = $database->query("SELECT SUM(amount) AS sum, time FROM playerevent GROUP BY time ORDER BY time DESC LIMIT 1,31");

//foreach($data as $row){
	//$g3 .= $first4.'['.($row['time']*86400000).','.$row['sum'].']';
	//$first4 = ',';
//}
$g3='["Win7",19389107],["Linux",11074736],["XP",4495376],["OSX",2142686],["Vista",1741447],["Win8",1672349]';

$series3 = '{data:['.$g3.'],label:"Server Count"}';


$series4 = '{ label: "Vanilla",  data: 70.13},
{ label: "Forge",  data: 14.94},
{ label: "MCPVP",  data: 14.92}';
$series5 = '{ label: "Default",  data: 80.8},
{ label: "Faithful32",  data: 1.89},
{ label: "Sphax",  data: 1.74},
{ label: "Soartex Fanver", data:0.63},
{ label: "HD Mortal Kombat", data:0.37}';
$plc = time() - 2592000;
$a1 = $database->query("SELECT * FROM versions WHERE time > $plc ORDER BY time DESC, percent DESC");

$count = $database->num_rows;

$d = array();
$a = array();
foreach($a1 as $a2){
	if(!is_array($a[$a2['time']]))$a[$a2['time']]=array();
	$a[$a2['time']][$a2['version']] = $a2['percent'];
}


$mi = 0;

foreach($a as $a2){
	foreach($a2 as $version => $percent){
		if(!is_array($d[$version]))$d[$version]=array();
		$d[$version][$mi] = $percent;
	}
	$mi++;
}
$first2 = '';

foreach($d as $vn => $d1){

	$data = '';
	$first = '';
	for($i=0;$i<$mi;$i++){
		$data .= $first.'['.($mi-$i).','.($d1[$i]*100).']';
		$first = ',';
	}
	$series .= $first2.'{data:['.$data.'],label:"'.$vn.'"}';
	$first2 = ',';
}

$template->setHeadScripts('	
<script language="javascript" type="text/javascript" src="/js/flot.js"></script>
<script language="javascript" type="text/javascript" src="/js/flot.time.js"></script>
<script language="javascript" type="text/javascript" src="/js/flot.stack.js"></script>
<script language="javascript" type="text/javascript" src="/js/flot.pie.js"></script>
<script language="javascript" type="text/javascript" src="/js/flot.categories.js"></script>
<script type="text/javascript">
$(function () {
$.plot($("#chart_div"), ['.$series1.'],{
grid:{
		labelMargin:20
	},
	series:{
		lines: { show: true, fill: true, steps: false }
	},xaxis:{
		show:false
	},
	legend:{
		position:"nw"
	}
});
 $.plot($("#chart_div2"), ['.$series.'],{
	grid:{
		labelMargin:20
	},
	series:{
		stack:true,
		lines: { show: true, fill: true, steps: false }
	},
	yaxis:{
		max:100
	},
	xaxis:{
		show:false
	},
	legend:{
		position:"nw"
	}
	});
	



$.plot($("#chart_div3"), ['.$series3.'],{grid:{
		labelMargin:20
	},series:{bars:{show:true,align:"center"}},
			xaxis: {
				mode: "categories",
				tickLength: 0
			}});

$.plot($("#pie1"), ['.$series4.'],
{
        series: {
            pie: { 
                innerRadius: 0.5,
                show: true
            }
        }
});

$.plot($("#pie2"), ['.$series5.'],
{
        series: {
            pie: { 
                innerRadius: 0.5,
                show: true
            }
        }
});

});
</script>

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
</script>
');
	$template->setTitle('Minecraft Server Statistics');
}else{
$pn2 = mysql_real_escape_string($_GET['name']);

$player = $database->query("SELECT * FROM players WHERE username = '$pn2'",db::GET_ROW);

if($database->num_rows == 0){
	header('Location: /');
}
$playername = $player['username'];
$showbadges = false;
$user = $database->query("SELECT id, username FROM users WHERE mcuser = '$playername'",db::GET_ROW);
if($database->num_rows != 0){
	$userid = $user['id'];
	$twitter = $user['username'];
	$badges = $database->query("SELECT * FROM userbadge AS ub LEFT JOIN badges AS b ON b.ID = ub.badgeID WHERE ub.userID = '$userid'");
	
	if($database->num_rows != 0){
		 $showbadges = true;
	}
}

$template->setTitle($playername);
$template->setDesc($playername.' likes to play minecraft! Check out their stats and achievements on CraftStats.com');
}
$template->show('header');
$template->show('nav');
$template->show('logo');
?>
</div>
<div id="container" class="clearfix">

<?php if($_GET['name'] == '') { ?>
<div class="box boxtop clearfix" style="padding-left:30px;padding-top:10px;">
<h2 style="float:left;">Minecraft Statistics</h2>
<div style="float:left;margin-left:20px;margin-top:9px;">
<a href="https://twitter.com/share" class="twitter-share-button"  data-lang="en">Tweet</a>
<div class="fb-like" style="margin-left:-15px;position:relative;bottom:3px;margin-right:15px;" data-href="http://craftstats.org/players.php" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false" data-font="arial"></div>
<div id="fb-root"></div>
</div></div>
<div class="box boxbottom clearfix" style="padding-left:30px;padding-top:10px;min-height:410px;">
<h2 style="margin-left:120px;margin-top:20px;margin-bottom:-10px;">48H Player Activity</h2>
<div id="chart_div" style="width:900px;height:300px;margin-top:20px;"></div>
<h2 style="margin-left:135px;margin-top:20px;margin-bottom:-10px;float:left;">Version Adoption</h2>
<h2 style="margin-left:230px;margin-top:20px;margin-bottom:-10px;float:left;">Server OS Usage</h2>
<div id="chart_div2" style="width:435px;height:300px;margin-top:20px;margin-left:11px;float:left;"></div>

<div id="chart_div3" style="width:435px;height:300px;margin-top:20px;margin-left:11px;float:left;"></div>

<h2 style="margin-left:135px;margin-top:20px;margin-bottom:-10px;float:left;">Client Mods</h2>
<h2 style="margin-left:230px;margin-top:20px;margin-bottom:-10px;float:left;">Texture Pack Usage</h2>
<div id="pie1" width="435" height="300" style="width:435px;height:300px;margin-top:20px;margin-left:11px;float:left;"></div>
<div id="pie2" width="435" height="300" style="width:435px;height:300px;margin-top:20px;margin-left:11px;float:left;"></div>
</div>

<?php }else{ ?>


<?php
if($_POST['claimip'] != '' && $_SESSION['mcuser'] == $playername){
$cip = mysql_real_escape_string($_POST['claimip']);
	$server = $database->query("SELECT * FROM servers WHERE (resolved = '$cip' AND resolved != '') OR ip = '$cip'",db::GET_ROW);
	if($database->num_rows == 0 ){
		?>
		<div class="alert alert-info" style="margin-right:10px;">
			We're not currently tracking that server! Make sure you entered the IP address correctly and try again.
		</div>
		<?php
	}else{
	
		$sp = $database->query("SELECT * FROM serverplayers WHERE playerID = '$player[ID]' AND serverID = '$server[ID]'",db::GET_ROW);
		if($database->num_rows == 0){
			$database->query("INSERT INTO serverplayers VALUES ('$player[ID]',$server[ID],'0','0')");
		}
		if($sp['owner'] == 1){
			?>
			<div class="alert alert-info" style="margin-right:10px;">
				You're already an owner of <?php echo $server['ip']; ?>.
			</div>
			<?php
		}else{
			$vs = 'CS'.$server['ID'].'-'.$player['ID'];
			$ping = $api->pingServer($server['ip'],1);
			if(stristr($ping['info']['HostName'],$vs)){
				?>
				<div class="alert alert-success" style="margin-right:10px;">
					We have successfully verified your ownership of <?php echo $server['ip']; ?>!
				</div>
				<?php
				
				$database->query("UPDATE serverplayers SET owner = '1' WHERE playerID = '$player[ID]' AND serverID = '$server[ID]'");
				
			}elseif($ping['fail'] == true){
				?>
				<div class="alert" style="margin-right:10px;">
					We were unable to contact <?php echo $server['ip']; ?> to verify your ownership.
				</div>
				<?php
			}else{
				?>
				<div class="alert alert-info" style="margin-right:10px;">
					To verify your ownership of this server, add '<?php echo $vs; ?>' to the MOTD and try to claim the server again.
				</div>
				<?php
			}
		}
	}
}
?>

<div class="box boxtop clearfix" style="padding-left:30px;padding-top:10px;">
<h2 style="float:left;"><?php echo $playername; ?></h2> 

<?php if($twitter != ''){ ?>
<div style="float:left;position:relative;top:8px;left:10px;"><a href="https://twitter.com/<?php echo $twitter; ?>" class="twitter-follow-button" data-show-count="true" data-show-screen-name="false" data-lang="en">Follow</a></div>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<?php } ?>

<?php
if($_SESSION['mcuser'] == $playername){
	?>
	<form class="form-inline" action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" style="float:right;position:absolute;top:9px;right:12px;">
		<input type="text" name="claimip" class="input-medium" placeholder="Server IP" style="position:relative;top:1px;">
		<button type="submit" class="btn">Claim this Server</button>
	</form>
	<?php
}
?>
</div>

<div class="box <?php echo ($showbadges ? 'boxbottomleft' : 'boxbottom')?> clearfix" style="padding-left:30px;padding-top:10px;<?php echo ($showbadges ? 'width:700px;' : 'width:921px;')?>float:left;height:337px;">

<div class="box boxmiddle inset" style="padding:12px 25px;width:130px;margin:15px -50px 15px 0px;float:left;position:relative;height:280px;">
	<img src="/skins.php?user=<?php echo $playername; ?>&size=128" class="skinfront" style="margin-top:20px;position:absolute;top:6px;left:25px;z-index:10;"/>
	<img src="/skins.php?user=<?php echo $playername; ?>&size=128&back" class="skinback" style="margin-top:20px;position:absolute;top:6px;left:25px;z-index:9;"/>
</div>

<div style="width:200px;float:left;position:relative;left:50px;">
	<h1 style="font-size:14px;margin-top:11px;">Seen on:</h1>
	
	<div style="height:130px;width:280px;overflow-y:scroll;overflow-x:hidden;">
	<ul style="position:relative;top:5px;left:27px;font-size:11px;font-weight:bold;margin-bottom:20px;">
		<?php 
		$servers = $database->query("SELECT * FROM serverplayers AS sp LEFT JOIN servers AS s ON s.ID = sp.serverID WHERE playerID = '{$player[ID]}' AND s.ID != '' AND found > 0");
		if(count($servers) == 0){
			echo '<li style="width:300px;">no servers :(</li>';
		}
		foreach($servers as $server){
			echo '<li style="width:300px;"><img src="/images/flags/'.strtolower($server['country']).'.png" class="flag" style="margin-right:7px;position:relative;top:2px;"/><a href="/server/'.$server['ip'].'">'.$server['ip'].'</a>, '.$server['found'].' time'.($server['found'] > 1 ? 's' : '').'</li>';
		}
		?>
	</ul>
	</div>
	
	<?php
	$so = $database->query("SELECT * FROM serverplayers AS sp LEFT JOIN servers AS s ON s.ID = sp.serverID WHERE sp.playerID = '$player[ID]' AND s.ID != '' AND sp.owner = '1'");
	
	if($database->num_rows > 0){
	?>
	<h1 style="font-size:14px;margin-top:11px;">Owner of:</h1>
	<div style="height:130px;width:280px;overflow-y:scroll;overflow-x:hidden;">
	<ul style="position:relative;top:5px;left:27px;font-size:11px;font-weight:bold;">
		<?php
		foreach($so as $server){
			echo '<li style="width:300px;"><img src="/images/flags/'.strtolower($server['country']).'.png" class="flag" style="margin-right:7px;position:relative;top:2px;"/><a href="/server/'.$server['ip'].'">'.$server['ip'].'</a></li>';
		}
		?>
	</ul>
	</div>
	<?php
	}
	?>
</div>

</div>
<?php if($showbadges){ ?>
<div class="box boxbottomright badges" style="width:206px;height:340px;float:left;">
<h3 style="font-size:14px;text-align:center;">badges</h3>
<?php
	
	foreach($badges as $b){
		echo '<img  title="'.$b['name'].'" class="pover" data-content="'.str_replace('%PLAYERNAME%',$playername,htmlentities($b['description'])).'" data-trigger="hover" data-placement="top" style="height:87px;width:87px;"src="/images/badges/'.$b['ID'].'.png"/>';
	}
?>
</div>
<?php } ?>
<?php /*
<div <?php echo ($_GET[nofade] == 1 ? '' : 'class="lazyload"'); ?>>
<?php
foreach($database->query("SELECT username FROM players ORDER BY RAND(MINUTE(NOW())) LIMIT 0,3000") as $player){
	echo '<img style="margin-bottom:-7px;" src="http://mag.racked.eu/tools/avatar.php?size=128&forceImage&name='.$player['username'].'"/>';
}
?></div> */ ?>

<?php 
}
	$template->show('footer');
	
	/*<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
		var data = new google.visualization.DataTable();
        data.addColumn("datetime", "Time");
        data.addColumn("number", "Online Players (Worldwide)");
		data.addColumn("number", "Online Players (American Servers)");
		data.addColumn("number", "Online Players (European Servers)");
		data.addColumn("number", "Online Players (Asian Servers)");
		data.addColumn("number", "Online Players (Oceanian Servers)");
		data.addRows([
          '.$rows.'
        ]);
		
		var options = {
          title: \'\',
		  focusTarget: \'category\',
		  curveType: "function",
		  interpolateNulls: false,
		  backgroundColor: \'transparent\',
		  vAxis: {viewWindow:{min:0}},
		  hAxis: { format: \'d MMM\' },
		  legend: {position:\'bottom\'},
		  chartarea:{width:\'100%\',height:\'80%\'},
		  width:1120,
		  height:400,
        };
		
		var chart = new google.visualization.AreaChart(document.getElementById(\'chart_div\'));
        chart.draw(data, options);
      }
    </script>*/
	}
?>