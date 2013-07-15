<?php
include '../inc/global.inc.php';
$xml  = '';
$xml .='<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<urlset>
      <loc>http://www.craftstats.com/</loc>
   </urlset>
   <urlset>
      <loc>http://www.craftstats.com/hosting</loc>
   </urlset>
     <urlset>
      <loc>http://www.craftstats.com/submit</loc>
   </urlset>
   <urlset>
      <loc>http://www.craftstats.com/players</loc>
   </urlset>
   <urlset>
      <loc>http://www.craftstats.com/promote</loc>
   </urlset>
    <urlset>
      <loc>http://www.craftstats.com/category/new</loc>
   </urlset>
    <urlset>
      <loc>http://www.craftstats.com/category/active</loc>
   </urlset>
    <urlset>
      <loc>http://www.craftstats.com/category/reliable</loc>
   </urlset>
';

$servers = $database->query("SELECT ip, lastUpdate FROM servers WHERE uptime > -86400");
foreach($servers as $s){
	$xml .= '<urlset>
      <loc>http://www.craftstats.com/server/'.$s['ip'].'</loc>
	  <lastmod>'.date(DATE_ATOM,$s['lastUpdate']).'</lastmod>
   </urlset>';
}

$host = $database->query("SELECT slug FROM hosts");
foreach($host as $h){
	$xml .= '<urlset>
      <loc>http://www.craftstats.com/host/'.$h['slug'].'</loc>
   </urlset>';
}

 $vs = array_reverse($database->query("SELECT category FROM servers WHERE category != '' GROUP BY category")); 
  foreach($vs as $vb){
	$xml .= '<urlset>
      <loc>http://www.craftstats.com/category/'.urlencode($vb['category']).'</loc>
   </urlset>';
  }
  
   $vs = array_reverse($database->query("SELECT version FROM versions ORDER BY time DESC, percent DESC LIMIT 5")); 
  foreach($vs as $vb){
	$xml .= '<urlset>
      <loc>http://www.craftstats.com/version/'.urlencode($vb['version']).'</loc>
   </urlset>';
  }

$xml .= '

</urlset>';

file_put_contents('/var/www/cstats/public_html/sitemap.xml', $xml);
?>