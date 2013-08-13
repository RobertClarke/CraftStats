<?php
include '../inc/global.inc.php';

header('content-type: image/png');

$sip = mysql_real_escape_string($_GET[ip]);

$sip = preg_replace('/\.png*$/', '', $sip);

$banneroption1 = $_GET['back'];
if($banneroption1 == '' || $banneroption1 > 10 || $banneroption1 == 'hills'){
	$banneroption1 = 1;
}else if($banneroption1 == 'forest'){
	$banneroption1 = 2;
}else if($banneroption1 == 'rain'){
	$banneroption1 = 3;
}else if($banneroption1 == 'beach'){
	$banneroption1 = 4;
}else if($banneroption1 == 'grass'){
	$banneroption1 = 5;
}else if($banneroption1 == 'shaft'){
	$banneroption1 = 6;
}else if($banneroption1 == 'night'){
	$banneroption1 = 7;
}else if($banneroption1 == 'sunrise'){
	$banneroption1 = 8;
}else if($banneroption1 == 'cottage'){
	$banneroption1 = 9;
}else if($banneroption1 == 'road'){
	$banneroption1 = 10;
}

$offline = false;

$server = $database->query("SELECT * FROM servers WHERE ip = '$sip' LIMIT 0,1",db::GET_ROW);

if($server['uptime'] < 0){
	$offline = true;
}
	
if($_GET['switch'] == 1){
	$offline = !$offline;
}

$cache = FALSE;//($memcache_disable ? false:$memcache->get(md5('banner'.($offline?'offline':'online').$banneroption1.$sip)));
if(!$cache){
	

	$upd1 = $database->query("SELECT * FROM updates WHERE serverID = '$server[ID]' AND uptime <= '0'");
	$downcount = $database->num_rows;
	$upd2 = $database->query("SELECT * FROM updates WHERE serverID = '$server[ID]'");
	$allcount = $database->num_rows;

	$uptime = round((($allcount-$downcount)/$allcount)*100);
	$servername = $server['ip'];

	
	

	

	$image = imagecreatefrompng('images/banner/back' . $banneroption1 . '.png');
	imagealphablending($image, true);
	imagesavealpha($image, true);

	if($banneroption1 == 1){//hills
		$background = imagecolorallocate($image, 102, 102 , 102);
		$titlecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 90) / 100);
		$offlinecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 80) / 100);
		$craftstatscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 50) / 100);
		$playerscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 75) / 100);
		
		$blur = 4;
	}else if($banneroption1 == 2){//forest
		$background = imagecolorallocate($image, 102, 102 , 102);
		$titlecol = imagecolorallocatealpha($image, 100, 100, 100, 127 * (100 - 90) / 100);
		$offlinecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 80) / 100);
		$craftstatscol = imagecolorallocatealpha($image, 10, 10, 10, 127 * (100 - 50) / 100);
		$playerscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 80) / 100);
		
		$blur = 1;
	}else if($banneroption1 == 3){//rain
		$background = imagecolorallocate($image, 102, 102 , 102);
		$titlecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 90) / 100);
		$offlinecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 80) / 100);
		$craftstatscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 75) / 100);
		$playerscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 75) / 100);
		
		$blur = 0;
	}else if($banneroption1 == 4){//beach
		$background = imagecolorallocate($image, 102, 102 , 102);
		$titlecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 90) / 100);
		$offlinecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 80) / 100);
		$craftstatscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 50) / 100);
		$playerscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 75) / 100);
		
		$blur = 2;
	}else if($banneroption1 == 5){//grass
		$background = imagecolorallocate($image, 102, 102 , 102);
		$titlecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 90) / 100);
		$offlinecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 80) / 100);
		$craftstatscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 75) / 100);
		$playerscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 75) / 100);
		
		$blur = 0;
	}else if($banneroption1 == 6){//shaft
		$background = imagecolorallocate($image, 102, 102 , 102);
		$titlecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 95) / 100);
		$offlinecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 80) / 100);
		$craftstatscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 70) / 100);
		$playerscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 65) / 100);
		
		$blur = 0;
	}else if($banneroption1 == 7){//night
		$background = imagecolorallocate($image, 102, 102 , 102);
		$titlecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 90) / 100);
		$offlinecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 80) / 100);
		$craftstatscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 50) / 100);
		$playerscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 75) / 100);
		
		$blur = 1;
	}else if($banneroption1 == 8){//sunrise
		$background = imagecolorallocate($image, 102, 102 , 102);
		$titlecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 100) / 100);
		$offlinecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 80) / 100);
		$craftstatscol = imagecolorallocatealpha($image, 200, 200, 200, 127 * (100 - 90) / 100);
		$playerscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 80) / 100);
		
		$blur = 3;
	}else if($banneroption1 == 9){//cottage
		$background = imagecolorallocate($image, 102, 102 , 102);
		$titlecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 90) / 100);
		$offlinecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 80) / 100);
		$craftstatscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 50) / 100);
		$playerscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 85) / 100);
		
		$blur = 1;
	}else if($banneroption1 == 10){//road
		$background = imagecolorallocate($image, 102, 102 , 102);
		$titlecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 90) / 100);
		$offlinecol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 80) / 100);
		$craftstatscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 50) / 100);
		$playerscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 85) / 100);
		
		$blur = 2;
	}

	for($i = 0; $i < $blur; $i++){
		imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
	}

	if($offline){
		imagefilter($image, IMG_FILTER_GRAYSCALE);
	}

	$bigtext = 'fonts/bebas.ttf';
	$smalltext = 'fonts/arial.ttf';



	if(!$offline){
		$players = $server['connPlayers'].'  PLAYERS  ONLINE';
		$uptime = $uptime.'%  UPTIME';
		if($server['version'] != ''){
			$version = 'VERSION '.$server['version'];
		}
	}else{
	$playerscol = imagecolorallocatealpha($image, 255, 255, 255, 127 * (100 - 75) / 100);
		$uptime = 'SERVER  OFFLINE';
	}

	$craftstats = 'TRACKED BY MINECRAFTSERVERS.COM';

	imagettftext($image, 22, 0 , 4, 32, $titlecol, $bigtext, $servername);
	imagettftext($image, 15, 0 , 4, 68, $playerscol, $bigtext, $players);
	imagettftext($image, 15, 0 , 4, 93, ($offline == true ? $offlinecol : $playerscol), $bigtext, $uptime);
	imagettftext($image, 8, 0 , 421, 95, $craftstatscol, $smalltext, $craftstats);
	imagettftext($image, 15, 0, 485, 24, $playerscol, $bigtext, $version);

	//imagefilledrectangle($image,0,0,600,100,$offlinecol);

	//imagettftext($image, 22, 0 , 200, 60, $titlecol, $bigtext, 'SERVER OFFLINE');
	ob_start(); 
	imagepng($image);
	$result = ob_get_contents();
	ob_end_clean(); 
	
	if(!$memcache_disable)$memcache->set(md5('banner'.($offline?'offline':'online').$sip), $result, MEMCACHE_COMPRESSED, 600); 
	echo $result;
	imagedestroy($image);
}else{
	echo $cache;
}
?>