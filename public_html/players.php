<?php
include '../inc/global.inc.php';


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
        },
		legend:{
		position:"se"
		}
});

$.plot($("#pie2"), ['.$series5.'],
{
        series: {
            pie: { 
                innerRadius: 0.5,
                show: true
            }
        },
		legend:{
		position:"se"
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
	header('Location: /?pf=1');
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
?>
<div class="row">
	<div class="twelve columns">
	<?php
if($_POST['claimip'] != '' && $_SESSION['mcuser'] == $playername){
$cip = mysql_real_escape_string($_POST['claimip']);
	$server = $database->query("SELECT * FROM servers WHERE (resolved = '$cip' AND resolved != '') OR ip = '$cip'",db::GET_ROW);
	if($database->num_rows == 0 ){
		?>
		<div class="alert-box" style="margin-top:20px;">
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
			<div class="alert-box" style="margin-top:20px;">
				You're already an owner of <?php echo $server['ip']; ?>.
			</div>
			<?php
		}else{
			$vs = 'CS'.$server['ID'].'-'.$player['ID'];
			$ping = $api->pingServer($server['ip'],1);
			if(stristr($ping['info']['HostName'],$vs)){
				?>
				<div class="alert-box success" style="margin-top:20px;">
					We have successfully verified your ownership of <?php echo $server['ip']; ?>!
				</div>
				<?php
				
				$database->query("UPDATE serverplayers SET owner = '1' WHERE playerID = '$player[ID]' AND serverID = '$server[ID]'");
				
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

<?php if($_GET['name'] == '') { ?>
<div class="row">
<h2 style="text-align:center;">Minecraft Statistics</h2>
</div>

<div class="row">
	<h5 style="text-align:center;">48H Player Activity</h5>
	<div id="chart_div"  style="width:600px;height:280px;margin:20px 30px;float:left;"></div>
</div>

<div class="row">
	<h5 style="text-align:center;">Version Adoption</h5>
	<div id="chart_div2"  style="width:600px;height:280px;margin:20px 30px;float:left;"></div>
</div>
<div class="row">
	<h5 style="text-align:center;">Server OS Usage</h5>
	<div id="chart_div3"  style="width:600px;height:280px;margin:20px 30px;float:left;"></div>
</div>

<div class="row">
	<div class="six columns">
		<h5 style="text-align:center;">Client Mods</h5>
		<div id="pie1"  style="width:290px;height:280px;margin:20px auto;float:left;"></div>
	</div>
	<div class="six columns">
		<h5 style="text-align:center;">Texture Pack Usage</h5>
		<div id="pie2"  style="width:290px;height:280px;margin:20px auto;float:left;"></div>
	</div>
</div>


<?php }else{ ?>




<?php
if($_SESSION['mcuser'] == $playername){
	?>
	<div class="row">
	<div class="four columns" style="padding-top:20px;">
	
		<div class="row collapse">
			<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post" >
				<div class="eight mobile-three columns">
					<input type="text" name="claimip" placeholder="Server IP"/>
				</div>
				<div class="four mobile-one columns">
					<button class="button expand postfix" style="padding:0px;">Claim IP</button>
				</div>
			</form>
		</div>
	</div>	
	</div>
	<?php
}
?>
<div class="row">
	<div class="four columns" style="height:370px;">
	<h3 style="margin-bottom:25px;text-align:center;"><?php echo $playername; ?></h3> 
	<img src="/skins.php?user=<?php echo $playername; ?>&size=128" class="skinfront" style="position:absolute;top:70px;left:50%;margin-left:-64px;z-index:10;"/>
	<img src="/skins.php?user=<?php echo $playername; ?>&size=128&back" class="skinback" style="position:absolute;top:70px;left:50%;margin-left:-64px;z-index:9;"/>

	</div>
	<div class="eight columns" style="padding-bottom:30px;">
	<h1 style="font-size:14px;margin-top:20px;">Seen on:</h1>
	
	<ul style="position:relative;list-style:none;top:5px;left:5px;font-size:11px;font-weight:bold;margin-bottom:20px;">
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
	<?php
	$so = $database->query("SELECT * FROM serverplayers AS sp LEFT JOIN servers AS s ON s.ID = sp.serverID WHERE sp.playerID = '$player[ID]' AND s.ID != '' AND sp.owner = '1'");
	
	if($database->num_rows > 0){
	?>
	<h1 style="font-size:14px;margin-top:20px;">Owner of:</h1>
	<ul style="position:relative;top:5px;list-style:none;left:5px;font-size:11px;font-weight:bold;">
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


<?php if($showbadges){ ?>

<h3 style="font-size:14px;text-align:center;margin-top:20px;">badges</h3>
<?php
	
	foreach($badges as $b){
		echo '<img   class="has-tip tip-left" data-width="120" title="'.$b['name'].': '.str_replace('%PLAYERNAME%',$playername,htmlentities($b['description'])).'" style="height:87px;width:87px;margin:0px 7px;"src="/images/badges/'.$b['ID'].'.png"/>';
	}
?>

<?php } 
}
?>
</div>
</div>
</div>
</div>
</div>
<?
	$template->show('footer');
?>