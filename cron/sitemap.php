<?php
include '../inc/global.inc.php';
$xml  = '';
$xml .='<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url>
      <loc>http://minecraftservers.com/</loc>
   </url>
   <url>
      <loc>http://minecraftservers.com/hosting</loc>
   </url>
     <url>
      <loc>http://minecraftservers.com/submit</loc>
   </url>
   <url>
      <loc>http://minecraftservers.com/players</loc>
   </url>
   <url>
      <loc>http://minecraftservers.com/promote</loc>
   </url>
    <url>
      <loc>http://minecraftservers.com/category/new</loc>
   </url>
    <url>
      <loc>http://minecraftservers.com/category/active</loc>
   </url>
    <url>
      <loc>http://minecraftservers.com/category/reliable</loc>
   </url>
';

$servers = $database->query("SELECT ip, lastUpdate FROM servers WHERE uptime > -86400");
foreach($servers as $s){
	$xml .= '<url>
      <loc>http://minecraftservers.com/server/'.$s['ip'].'</loc>
	  <lastmod>'.date(DATE_ATOM,($s['lastUpdate'] == 0 ? time() : $s['lastUpdate'])).'</lastmod>
   </url>';
   $xml .= '<url>
      <loc>http://minecraftservers.com/server/'.$s['ip'].'/vote</loc>
	  <lastmod>'.date(DATE_ATOM,($s['lastUpdate'] == 0 ? time() : $s['lastUpdate'])).'</lastmod>
   </url>';
}

$host = $database->query("SELECT slug FROM hosts");
foreach($host as $h){
	$xml .= '<url>
      <loc>http://minecraftservers.com/host/'.$h['slug'].'</loc>
   </url>';
}

 $vs = array_reverse($database->query("SELECT category FROM servers WHERE category != '' GROUP BY category")); 
  foreach($vs as $vb){
	$xml .= '<url>
      <loc>http://minecraftservers.com/category/'.urlencode($vb['category']).'</loc>
   </url>';
  }
  
   $vs = array_reverse($database->query("SELECT version FROM versions ORDER BY time DESC, percent DESC LIMIT 5")); 
  foreach($vs as $vb){
	$xml .= '<url>
      <loc>http://minecraftservers.com/version/'.urlencode($vb['version']).'</loc>
   </url>';
  }

$xml .= '

</urlset>';

file_put_contents('/var/www/cstats/public_html/sitemap.xml', $xml);
?>
