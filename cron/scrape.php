<?php
set_include_path('/var/www/cstats/');
$memcache_disable = false;
include 'inc/global.inc.php';
include 'lib/simpledom.php';
$p=1;
$scount=0;
// MINECRAFTSERVERS.ORG SCRAPE


/*while(stristr(($file = file_get_html('http://minecraftservers.org/index/'.$p)),'<table class="serverList">')){
	$memcache->set('strip_page',$p,MEMCACHE_COMPRESSED,600);
	$count = 0;
	$r = $file->find('.serverList tr');
	foreach($r as $a){
		$name = $a->children(1)->plaintext;
		$ip = str_replace(array("\r\n", "\r",'IP: '), "",$a->children(2)->plaintext);
		$banner = $a->children(2)->children(0);
		if(is_object($banner)){
			$banner = 'http://minecraftservers.org'.$banner->children(0)->src;
		}else{
			$banner = '';
		}
		*/

// MINESTATUS.NET

while(!stristr(($file = file_get_html('https://minestatus.net/?page='.$p)),'No servers are listed yet')){
	$memcache->set('strip_page',$p,MEMCACHE_COMPRESSED,600);
	$count = 0;
	$r = $file->find('.servers ul');
	foreach($r as $a){
		$name = $a->children(1)->children(0)->children(0)->title;
		$ip = $a->children(1)->children(2);
		if(is_object($ip)){
			$ip = strtolower($ip->children(2)->plaintext);
		}else{
			$ip = '';
		}
		$ip = str_replace(array("\r\n", "\r",'IP: ',' '), "",$ip);
		
		$banner = 'http:'.$a->children(1)->children(0)->children(0)->src;
		
		$name = mysql_real_escape_string($name);
		$ip = mysql_real_escape_string($ip);
		$banner = mysql_real_escape_string($banner);
		
		$resolved = $api->validateIP($ip);
		if($resolved != false){
			$status = $api->trackServer($ip,true);
			if($status['status'] == 'success'){
				$database->query("UPDATE servers SET name = '$name' WHERE resolved = '$resolved'");
				$s = $database->query("SELECT ID FROM servers WHERE resolved = '$resolved'",db::GET_ROW);
				if(!stristr($banner,'nobanner') && $banner != 'http:'){
					if($s['ID'] != ''){
						$worked = file_put_contents('/var/www/cstats/public_html/images/banners/'.$s['ID'].'.png', file_get_contents($banner));
						if($worked){
							$database->query("UPDATE servers SET bannerurl='http://craftstats.com/images/banners/$s[ID].png' WHERE ID = '$s[ID]'");
						}
					}
				}
			}
		}
		usleep(50000);
		if($ip != ''){
		$scount++;
		$memcache->set('strip_last',$name,MEMCACHE_COMPRESSED,600);
		}
		$memcache->set('strip_min',$scount,MEMCACHE_COMPRESSED,600);
	}
	
	
	$p++;
}
?>