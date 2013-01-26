<?php
set_include_path('/home/cstats/');
$memcache_disable = true;
include 'inc/global.inc.php';

$scutoff = -(60*60*24*31);
$updcutoff = time() - 60*60*24*8;
$bcutoff = time() - 60*45;
$database->query("DELETE FROM updates WHERE time < $updcutoff");
$database->query("DELETE FROM serviceinfo WHERE time < $updcutoff");
$database->query("DELETE FROM batchqueue WHERE time < $bcutoff");
$database->query("DELETE FROM servers WHERE uptime < $scutoff");

$info = $database->query("SELECT COUNT(*) as c FROM players",db::GET_ROW);
$players = $info['c'];

$info = $database->query("SELECT COUNT(*) as c FROM (SELECT * FROM plugins GROUP BY defaultName) AS pt",db::GET_ROW);
$plugins = $info['c'];

$info = $database->query("SELECT COUNT(*) as c FROM servers",db::GET_ROW);
$servers = $info['c'];

$info = $database->query("SELECT SUM(amount) as c FROM playerevent",db::GET_ROW);
$events = $info['c'];

$time = time();

$vs = $database->query("
SELECT s.version, COUNT( * ) AS amt
FROM servers s
WHERE s.version !=  ''
GROUP BY s.version");

foreach($vs as $v){
	$vtotal += $v['amt'];
}

$vp = array();

foreach($vs as $v){
	$vp[$v['version']] = $v['amt']/$vtotal;
}

foreach($vp as $vn => $p){
	if($p<0.01)continue;
	$database->query("INSERT INTO versions VALUES ($time,'$vn',$p)");
}

$database->query("INSERT INTO sitegrowth VALUES ($time,$players,$servers,$plugins,$events)");

foreach($database->query("SELECT * FROM badges") as $badge){
	if($badge['ID'] >= 6 && $badge['ID'] <= 9){
		$eligible = $database->query("SELECT COUNT(sp.serverID) AS sc, u.ID AS uid FROM users u JOIN players p ON u.mcuser = p.username JOIN serverplayers sp ON sp.playerID = p.ID WHERE u.mcuser != '' AND sp.found > 0 GROUP BY u.mcuser");
		$gotthem = $database->query("SELECT * FROM userbadge WHERE badgeID >= 6 AND badgeID <= 9");
		$users = array();
		foreach($gotthem as $u){
			$users[$u['userID']] = max($users[$u['userID']],$u['badgeID']);
		}
		
		foreach($eligible as $u){
			if($u['sc'] > 50){
				$toaward = 9;
			}elseif($u['sc'] >= 25){
				$toaward = 8;
			}elseif($u['sc'] >= 10){
				$toaward = 7;
			}elseif($u['sc'] > 0){
				$toaward = 6;
			}
			
			if($users[$u['uid']] > 0 && $users[$u['uid']] < $toaward){
				$database->query("UPDATE userbadge SET badgeID = '$toaward' WHERE userID = '{$u[uid]}' AND badgeID = '{$users[$u[uid]]}'");
			}
			$time = time();
			if(!isset($users[$u['uid']]))$database->query("INSERT INTO userbadge VALUES ('{$u[uid]}','$toaward','$time')");
		}
	}
	if($badge['ID'] == 5){
		$eligible = $database->query("SELECT ID AS uid FROM users WHERE mcuser != '' AND username != ''");
		$gotthem = $database->query("SELECT * FROM userbadge WHERE badgeID = 5");
		$users = array();
		foreach($gotthem as $u){
			$users[$u['userID']] = max($users[$u['userID']],$u['badgeID']);
		}
		
		foreach($eligible as $u){
			$time = time();
			if(!isset($users[$u['uid']]))$database->query("INSERT INTO userbadge VALUES ('{$u[uid]}','5','$time')");
		}
	}
	
	if($badge['ID'] == 3){
		$eligible = $database->query("SELECT COUNT(sp.serverID) AS sc, u.ID AS uid FROM users u JOIN players p ON u.mcuser = p.username JOIN serverplayers sp ON sp.playerID = p.ID WHERE u.mcuser != '' AND owner = 1 GROUP BY u.mcuser");
		$gotthem = $database->query("SELECT * FROM userbadge WHERE badgeID = 3");
		$users = array();
		foreach($gotthem as $u){
			$users[$u['userID']] = max($users[$u['userID']],$u['badgeID']);
		}
		
		foreach($eligible as $u){
			$time = time();
			if(!isset($users[$u['uid']]))$database->query("INSERT INTO userbadge VALUES ('{$u[uid]}','3','$time')");
		}
	}
}
?>