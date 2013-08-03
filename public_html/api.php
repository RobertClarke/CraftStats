<?php
error_reporting(E_ERROR | E_PARSE);
$logrequest = false;
$memcache_disable = true;
include '../inc/global.inc.php';
include_once '../lib/twitteroauth.php';

if($_GET['test'] != ''){
	print_r($api->pingServer($_GET['test'],1));
	//$sr = $database->query("SELECT * 
//FROM  `servers` 
//WHERE  `ip` LIKE  '%mcsg.in'");
	//foreach($sr as $s){
	//	$database->query("INSERT INTO serverplayers VALUES(67663,$s[ID],0,1)");
	//}
}

//add new server to DB
if($_POST[req] == 'm01'){
	$api->trackServer($_POST[ip]);	
	exit;
}

if($_GET[req] == 'm01'){
	$api->trackServer($_GET[ip]);	
	exit;
}

if($_GET[req] == 'm011'){
	$status = $api->trackServer($_GET[ip],false,true);	
	if($status['status'] != 'success')echo json_encode($status);
	exit;
}

// used for /stats
if($_POST[req] == 'm02'){
	$api->getUpdateStats($_POST[since]);	
	exit;
}

if($_GET[req] == 'm02'){
	$api->getUpdateStats($_GET[since]);
	exit;
}

// get list for slaves
if($_GET[req] == 'm03'){
	echo $api->getUpdateList($_GET[t],$_GET[mt],$_GET[ip],$_GET[sc]);	
	exit;
}

// put raw ping data
if($_POST[req] == 'm04'){
	echo $api->storeBatch($_POST[ip],$_POST[sc],$_POST['av'],$_POST['t'],$_POST['r']);	
	exit;
}

if($_GET[req] == 'm04'){
	echo $api->storeBatch($_GET[ip],$_GET[sc],$_POST['av'],$_GET['t'],$_GET['r']);	
	exit;
}

//vote for server ID
if($_POST[req] == 'm05'){
	echo json_encode($api->addvote($_POST[id],$_POST[usr]));	
	exit;
}

if($_GET[req] == 'm05'){
	echo json_encode($api->addvote($_GET[id],$_GET[usr]));	
	exit;
}

//update to advCheck = 2
if($_POST[req] == 'm06'){
	echo $api->advCheck($_POST[ip]);	
	exit;
}

if($_GET[req] == 'm06'){
	echo $api->advCheck($_GET[ip]);	
	exit;
}

// plugin registers with server
if($_POST[req] == 'm07'){
	$api->registerCSPlus($_SERVER['REMOTE_ADDR']);
	exit;
}

if($_GET[req] == 'm07'){
	$api->registerCSPlus($_SERVER['REMOTE_ADDR']);
	exit;
}

if($_POST[req] == 'm08'){
Header("Content-Type: application/x-javascript; charset=UTF-8");
	echo $_GET['callback']."(".json_encode($api->getDirtBlocks()).");";
	exit;
}

if($_GET[req] == 'm08'){
Header("Content-Type: application/x-javascript; charset=UTF-8");
	echo $_GET['callback']."(".json_encode($api->getDirtBlocks()).");";
	exit;
}

if($_POST[req] == 'm09'){
	echo json_encode($api->serverStatus($_POST['ip']));
	exit;
}

if($_GET[req] == 'm09'){
	echo json_encode($api->serverStatus($_GET['ip']));
	exit;
}

if($_GET[req] == 'm10'){
	header('Location: /'.$_GET['v1'].'/'.$_GET['v2']);
	exit;
}


if($_GET[req] == 'm11'){
	$r = $api->trackServer($_GET[ip],true);
	if($r['extra']!=''){
		header('Location: /server/'.$r['extra']);
		if(filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)){
			$database->query('UPDATE users SET email = \''.$_GET['email'].'\' WHERE id = \''.$_SESSION['id'].'\'');
		}
	}else{
		header('Location: /submit?se='.urlencode($r['info']).'&ev='.urlencode($_GET['email']));
	}
	exit;
}


//vote for server ID
if($_POST[req] == 'm12'){
	echo json_encode($api->addvote($_POST[id],$_POST[usr]));	
	exit;
}

if($_GET[req] == 'm12'){
	$r = $api->addvote($_GET[id],$_GET[usr]);	
	if($r['extra']!='')header('Location: /server/'.$r['extra'].'/voted');
	exit;
}

if($_GET[req] == 'm122312893'){
	$pl = $database->query("SELECT * FROM serverplayers AS sp LEFT JOIN players AS p ON sp.playerID = p.ID WHERE sp.owner = 1");
	foreach($pl as $p){
		$on = $database->query("SELECT * FROM users WHERE mcuser = '$p[username]'",db::GET_ROW);
		if($on['mcuser'] != ''){
			$database->query("INSERT INTO serverowners VALUES ('$p[serverID]',$on[id])");
		}
	}
}

if($_GET[req] == 'scrape2'){
	echo json_encode(array(
	
	'min'=>$memcache->get('strip_min'),
	'page'=>$memcache->get('strip_page'),
	'last'=>$memcache->get('strip_last')
	));	
}

if($_GET[req] == 'scrape'){
$template->setHeadScripts('
<script type="text/javascript">
window.setInterval(updateStats, 500);
var last = '.(time()).';
function updateStats(){
	
	$.ajax({					
			type: "GET",
			url: "/api.php",
			data: "req=scrape2",
			async: true,
			cache: false,
			dataType: "json",
			
			success: function(data){
				$(\'.min\').html(data.min);
				$(\'.last\').html(data.last);
				$(\'.page\').html(data.page);
			}
			
		});
}

</script>
');
	$template->show('header');
	?>
	<h3 style="text-align:center;">Scraped <span class="min">0</span> servers (on page <span class="page">1</span>)</h3>
	<h4 style="text-align:center;">Last scraped: <span class="last"></span></h3>
	<?php
}



?>