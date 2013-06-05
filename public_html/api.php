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
	if($status['status'] != 'success')print_r($status);
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
	if($r['extra']!='')header('Location: /server/'.$r['extra']);
	exit;
}


?>