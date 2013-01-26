<?php include '../inc/global.inc.php';

$stats = $database->query("SELECT * FROM sitegrowth ORDER BY time DESC LIMIT 10");

$template->setHeadscripts('<script src="/js/spinning.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	$(".spinner0").spinning({initial_value:'.$stats[0]['players'].',ID:0});
	$(".spinner1").spinning({initial_value:'.$stats[0]['servers'].',ID:1});
	$(".spinner2").spinning({initial_value:'.$stats[0]['plugins'].',ID:2});
	setInterval(function(){$(".spinner0").data("spinning").increment(1,0);},'.round((1/(max($stats[0]['players']-$stats[9]['players'],1)/6000))*1000).');
	setInterval(function(){$(".spinner1").data("spinning").increment(1,1);},'.round((1/(max($stats[0]['servers']-$stats[9]['players'],1)/6000))*1000).');
	setInterval(function(){$(".spinner2").data("spinning").increment(1,2);},'.round((1/(max($stats[0]['plugins']-$stats[9]['players'],1)/6000))*1000).');
	});
</script>');
if(strtolower($_GET['cat']) == 'new'){
	$_GET['cat'] = 'New';
}
if(strtolower($_GET['cat']) == 'reliable'){
	$_GET['cat'] = 'Reliable';
}
if(strtolower($_GET['cat']) == 'active'){
	$_GET['cat'] = 'Active';
}
$template->setTitle(($_GET['version'] ? $_GET['version'].' Minecraft Servers' : ($_GET['cat'] ? $_GET['cat'].' Minecraft Servers':'Best Minecraft Servers List')));
$template->show('header');
$template->show('nav');
$template->show('logo');

?>
</div>
<div id="container" class="preload1 clearfix">
<?php
if($_GET['blacklist'] == 1){ echo ('<div class="alert alert-error fade in"><strong>Error!</strong> That server has been blacklisted!</div> ');}
if($_GET['blacklist'] == 2){ echo ('<div class="alert alert-success fade in"><strong>Success!</strong> Your server has been blacklisted.</div> ');}
?>
<?php if(!$_GET['version'] && !$_GET['cat']){ ?>
<div class="clearfix"><!--<div class="box boxleft checking left clearfix" style="width:590px;height:230px;">
<h1 style="padding-top:20px;">Search Servers</h1>
<h3 style="margin-left:55px;margin-top:40px;margin-bottom:5px;">Enter Server Address</h3>


<div class="emsg"></div>
</div>
<div class="box boxright left clearfix" style="width:338px;height:230px;">
<h1 style="font-size:20px;">Random Servers</h1>

<div class="rotate">
<?php
/*$servers = $database->query("SELECT 
  ID as sid, country, 
  name, ip, advCheck,
  connPlayers AS cp, maxPlayers AS mp, version
FROM servers WHERE uptime > 0 ORDER BY RAND(MINUTE(NOW())) LIMIT 0,20");
shuffle($servers);
	foreach($servers as $server){
		if($i < 5){
			if($i == 0){
				$cl = 'boxtop';
			}else{
				$cl = 'boxmid';
			}
		}else{
			$cl = 'boxbottom';
			if($i > 5){
				$stl = 'style="display:none;"';
			}
		}
		
		echo '<div '.$stl.' class="box '.$cl.'"><img class="flag" src="/images/flags/'.strtolower($server[country]).'.png"/><a href="/server/'.$server[ip].'">'.($server[name] == '' ? $server[ip] : $server[name]).'</a> '.($server['advCheck'] != 2 ? '' : '<span style="font-weight:bold;color:#4A4;font-size:15px;position:absolute;right:94px;top:6px;">+</span>').'<div class="right usrs"> <img src="/images/usericon.png"/>'.$server[cp].' / '.$server[mp].'</div></div>';
		
		$cl = '';
		$stl = '';
		$i++;
	}*/
?>
</div>
</div>
</div>-->

<?php } ?>
<style type="text/css">
	.search h1{
		width:40%;
		float:left;
		margin-top:2px;
	}
	.emsg{
		float:right;
		font-size:12px;
		padding-top:7px;
		margin-right:10px;
		color:#333;
	}
</style>
<div class="box boxmiddle left search" style="width:944px;">
<?php
 echo ($_GET['version'] ? '<h1>'.$_GET['version'].' Minecraft Servers</h1>' : ($_GET['cat'] ? '<h1 >'.$_GET['cat'].' Minecraft Servers</h1>':'<h1>Minecraft Servers</h1>')); ?>
<div style="float:right;margin:5px 0px;"><form onSubmit="event.preventDefault();advanceLookup();" style="margin-bottom:0px;"><input class="ipinput" type="text"/><div class="trackbtn">
<button class="stg1" ></button>
<button class="stg2" ></button></div></form></div>
<h3 class="emsg">Find a server </h3>
</div>
<div class="box boxmiddle left" id="slist" style="width:944px;margin-top:20px;">



<div style="margin-top:8px;margin-bottom:8px; display: -moz-inline-stack;display: inline-block;vertical-align: middle;zoom: 1;position: relative;left: 50%;">
		<div style="position: relative;left: -50%;" class="btn-group">
		<a href="/" class="btn btn-small <?php if(strtolower($_GET['cat']) == '' && $_GET['version'] == '')echo 'disabled'; ?>" >Top Ranked</a>
	<a href="/category/new" class="btn btn-small <?php if(strtolower($_GET['cat']) == 'new')echo 'disabled'; ?>" >New</a>
	<a href="/category/reliable" class="btn btn-small <?php if(strtolower($_GET['cat']) == 'reliable')echo 'disabled'; ?>" >Uptime</a>
	<a href="/category/active" class="btn btn-small <?php if(strtolower($_GET['cat']) == 'active')echo 'disabled'; ?>" >Activity</a>
	
  <?php $vs = array_reverse($database->query("SELECT version FROM versions ORDER BY time DESC, percent DESC LIMIT 5")); 
  foreach($vs as $vb){
	echo '<a href="/version/'.$vb['version'].'" class="btn btn-small '.($_GET['version'] == $vb['version'] ? 'disabled':'').'">'.$vb['version'].'</a>';
  }
  ?>
  <?php $vs = array_reverse($database->query("SELECT category FROM servers WHERE category != '' GROUP BY category ORDER BY COUNT(category) DESC LIMIT 5")); 
  foreach($vs as $vb){
	echo '<a href="/category/'.$vb['category'].'" class="btn btn-small '.($_GET['cat'] == $vb['category'] ? 'disabled':'').'" >'.$vb['category'].'</a>';
  }
  ?>
  </div>
</div>
<table style="width:100%;margin-top:10px;" class="table table-hover topservers">
<tr><th>#</th><th>Connect to</th><th>MOTD</th><th>Uptime</th><th>Players</th><th>Last Ping</th></tr>
<?php
function FosMerge($arr1, $arr2) {
    $res=array();
    $arr1=array_reverse($arr1);
    $arr2=array_reverse($arr2);

    foreach ($arr1 as $a1) {
        if (count($arr1)==0) {
            break;
        }
        array_push($res, array_pop($arr1));
		$rate = count($arr1)/(count($arr2)==0?1:count($arr2));
        if (count($arr2)!=0 && (rand(0,floor($rate)) == 1 || $rate < 2)) {
            array_push($res, array_pop($arr2));
        }
    }
    return array_merge($res, $arr2);
}

$cpage = ($_GET['p'] != 0 ? $_GET['p'] : 0);

if($_GET['version']){
	$version = 'AND version = \''.mysql_real_escape_string($_GET['version']).'\'';
	$sprefix = '/version/'.$_GET['version'];
}
if($_GET['cat']){
	if($_GET['cat'] != 'New' && $_GET['cat'] != 'Reliable' && $_GET['cat'] != 'Active')$version = 'AND category = \''.mysql_real_escape_string($_GET['cat']).'\'';
	$sprefix = '/category/'.$_GET['cat'];
}

if(strtolower($_GET['cat']) == 'new'){
	$new = 'ID DESC,';
}
if(strtolower($_GET['cat']) == 'reliable'){
	$new = 'uptimeavg DESC,';
}
if(strtolower($_GET['cat']) == 'active'){
	$new = 'connPlayers DESC,';
}

$tservers = $database->query("SELECT COUNT(*) AS c FROM servers WHERE blacklisted != 1 $version",db::GET_ROW);
$tservers = floor($tservers['c']/30)-1;

$cpage = max(0,min($cpage,$tservers));
$pagemin = $cpage*30;
$pagemax = 30;
$time = time();


$servers = $database->query("SELECT 
  ID as sid, country, 
  name, ip,advCheck,
  connPlayers AS cp,sponsorTime AS st, category, maxPlayers AS mp, version, motd, lastUpdate, uptimeavg, ranking, uptime
FROM servers WHERE sponsorTime < UNIX_TIMESTAMP() AND blacklisted != 1 $version ORDER BY $new ranking ASC LIMIT $pagemin, $pagemax");
if($cpage == 0 && $new == ''){
	$sponsored = $database->query("SELECT 
  ID as sid, country,sponsorTime AS st, category,
  name, ip, advCheck,
  connPlayers AS cp, maxPlayers AS mp, version, motd, lastUpdate, uptimeavg, ranking, uptime
FROM servers WHERE sponsorTime > UNIX_TIMESTAMP() AND blacklisted != 1 $version ORDER BY sponsorRank DESC, ranking ASC");
$servers = FosMerge($servers,$sponsored);
}
$time = time();
foreach($servers as $server){
	echo '<tr onclick="document.location=\'/server/'.$server['ip'].'\';" '.($server['st'] > $time ? 'style="background-color:#f5f5f5;"':'').'class="slink">
	<td>'.($server['st'] > $time ? '<i class="icon-star"></i>': $server['ranking']).'</td>
	<td> <strong>'.$server['ip'].' '.($server['version'] != '' ? '<div style="float:right;margin-left:7px;"><a href="/version/'.$server['version'].'"><span class="label label-info">'.$server['version'].'</span></a></div>' : '').' 
	'.($server['category'] != '' ? '<div style="float:right;margin-left:7px;"><a href="/category/'.$server['category'].'"><span class="label label-important">'.$server['category'].'</span></a></div>' : '').'
	'.($server['advCheck'] == 2 ? '<div style="float:right;"><span class="label label-success">DirtBlock</span></div>' : '').'</strong></td><td><em>'.substr($server['motd'],0,40).'</em></td><td><span style="padding:3px 0px;display:block;width:50px !important;text-align:center;" class="label label-'.($server['uptime'] <= 0 ? 'important' : ($server['uptimeavg'] > 90 ? 'success' : ($server['uptimeavg'] > 70 ? 'info' : ($server['uptimeavg'] > 50 ? 'important' : 'inverse')))).'">'.($server['uptime'] <= 0 ? 'down' : $server['uptimeavg'].'%').'</span></td><td>'.$server['cp'].' / '.$server['mp'].'</td><td>'.($time - $server['lastUpdate'] > 60 ? round(($time - $server['lastUpdate'])/60).'m' : $time - $server['lastUpdate'].'s').' ago</td></tr>';
}

?>
</table>
<div class="pagination pagination-centered">
  <ul>
	<?php
	
	echo '<li'.($cpage == 0 ? ' class="disabled"':'').'><a href="'.$sprefix.'/p/0#slist">&laquo;</a></li>';
	
	for($i = $cpage-2;$i<$cpage+3;$i++){
		if($i >= 0 && $i <= $tservers-1){
			echo '<li'.($i == $cpage ? ' class="active"':'').'><a href="'.$sprefix.'/p/'.$i.'#slist">'.($i+1).'</a></li>';
		}
	}
	
	echo '<li'.($cpage == $tservers ? ' class="disabled"':'').'><a href="'.$sprefix.'/p/'.$tservers.'#slist">&raquo;</a></li>';
	?>
  </ul>
</div>
</div>

<div class="box boxmiddle left" style="margin:15px 0px;width:944px;height:230px;">
	<a href="http://dirtblock.com"><img src="/images/dbbanner.png" style="margin:0 auto;"/></a>
</div>
<?php
$template->show('footer');
?>

