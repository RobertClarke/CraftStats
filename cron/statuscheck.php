<?php
set_include_path('/var/www/cstats/');
$memcache_disable = true;
include 'inc/global.inc.php';
include 'lib/twitteroauth.php';
$time = time();
$cutoff = $time - 172800;
$database->query("DELETE FROM mcstatus WHERE time < $cutoff");
function startTimer(){
	global $starttime;
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$starttime = $mtime; 
}

function endTimer(){
	global $starttime;
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$endtime = $mtime; 
	return round(($endtime - $starttime),3); 
}
function checkStatus($database){
	$ctx=stream_context_create(array('http'=>
		array(
			'timeout' => 3
		)
	));

	$last = $database->query("SELECT * FROM mcstatus ORDER BY time DESC LIMIT 1",db::GET_ROW);
	startTimer();
	$all = json_decode(file_get_contents('http://status.mojang.com/check'),true);
	$website = ($all[0]['minecraft.net'] == 'green');
	$session = ($all[2]['session.minecraft.net'] == 'green');
	$skins = ($all[5]['skins.minecraft.net'] == 'green');
	$login = ($all[1]['login.minecraft.net'] == 'green');
	$time = time();
	$day = $time - 86400;
	
	$prev = $database->query("SELECT sum(case when login>0 THEN 1 ELSE 0 END) AS login,
	sum(case when session>0 THEN 1 ELSE 0 END) AS session, 
	sum(case when skins >0 THEN 1 ELSE 0 END) AS skins, 
	sum(case when website >0 THEN 1 ELSE 0 END)AS website 
	FROM mcstatus WHERE time > $day",db::GET_ROW);
	foreach($prev as $k => $p){
		$prev[$k] = round((($p*15) / 86400)*100,2);
		if($p>0){
			$somethingdown = true;
		}
	}
	/*$msg = '';
	$shouldtweet == 0;
	if(($last['website'] > 300 && $website == false) || ($last['session'] > 300 && $session == false) || ($last['skins'] > 300 && $skins == false) || ($last['login'] > 300 && $login == false)){
		$msg = 'There\'s a minecraft outage - '.(!$website ? 'Website [✘]' : '').(!$session ? (!$website ? ' • ' : '').'Sessions [✘]' : '').(!$skins ? ((!$website || !$session) ? ' • ' : '').'Skins [✘]' : '').(!$login ? ((!$website || !$session || !$skins) ? ' • ' : '').'Login [✘]' : '').' ';
		
		if((($last['website'] == 0 && $website == false) || ($last['skins'] == 0 && $skins == false) || ($last['session'] == 0 && $session == false) || ($last['skins'] == 0 && $skins == false))){
			$shouldtweet = 1;
		}
		
		if((($last['website'] > 300 && $website == true) || ($last['skins'] > 300 && $skins == true) || ($last['session'] > 300 && $session == true) || ($last['skins'] > 300 && $skins == true))){
			$msg = 'Status update • Website '.(!$website ? '[✘]' : '[✔]').' • Skins '.(!$website ? '[✘]' : '[✔]').' • Sessions '.(!$website ? '[✘]' : '[✔]').' • Login '.(!$website ? '[✘]' : '[✔]');
			$shouldtweet = 1;
		}
	}elseif($last['website'] > 300 || $last['session'] > 300 || $last['skins'] > 300 || $last['login'] > 300){
		$msg = 'All minecraft services are UP [✔]';
		$shouldtweet = 1;
	}
	*/
	$website = ($website ? 0 : $last['website'] + $time - $last['time']);
	$session = ($session ? 0 : $last['session'] + $time - $last['time']);
	$skins = ($skins ? 0 : $last['skins'] + $time - $last['time']);
	$login = ($login ? 0 : $last['login'] + $time - $last['time']);
	
	/*if($msg != '' && $shouldtweet == 1){
		$cstats = new TwitterOAuth('HyI8Rfv5NwhU2pP3pZ3TA', 'nKVSmnejMIgRBWZT2ZSOJAHTzslBo2ZmHhqxvG7otM','1012342404-q1wC6Rq5uY8MxHOyNgrQ8cy5wea18VPZ6xgUa3j','7AW6FlK5ShtEF6ErGZaPm4OX7sbXGMvSPWztS7WBo');
		$cstats->post('statuses/update', array('status' => $msg.' http://minecraftstatus.com #mcstatus')); 
	}*/
	
	$database->query("INSERT INTO mcstatus VALUES ('$time','$login','$session','$skins','$website','$login_r','$session_r','$skins_r','$website_r','$prev[login]','$prev[session]','$prev[skins]','$prev[website]')");
}

checkStatus($database);
sleep(15);
checkStatus($database);
sleep(15);
checkStatus($database);
sleep(15);
checkStatus($database);


?>
