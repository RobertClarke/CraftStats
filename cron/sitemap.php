<?php
include '../inc/global.inc.php';
$xml  = '';
$xml .='<?xml version="1.0" encoding="UTF-8"?>

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<sitemap>
      <loc>http://www.craftstats.com/</loc>
   </sitemap>
   <sitemap>
      <loc>http://www.craftstats.com/hosting</loc>
   </sitemap>
     <sitemap>
      <loc>http://www.craftstats.com/submit</loc>
   </sitemap>
   <sitemap>
      <loc>http://www.craftstats.com/players</loc>
   </sitemap>
   <sitemap>
      <loc>http://www.craftstats.com/promote</loc>
   </sitemap>
    <sitemap>
      <loc>http://www.craftstats.com/category/new</loc>
   </sitemap>
    <sitemap>
      <loc>http://www.craftstats.com/category/active</loc>
   </sitemap>
    <sitemap>
      <loc>http://www.craftstats.com/category/reliable</loc>
   </sitemap>
';

$servers = $database->query("SELECT ip, lastUpdate FROM servers WHERE uptime > -86400");
foreach($servers as $s){
	$xml .= '<sitemap>
      <loc>http://www.craftstats.com/server/'.$s['ip'].'</loc>
	  <lastmod>'.date(DATE_ATOM,$s['lastUpdate']).'</lastmod>
   </sitemap>';
}

$host = $database->query("SELECT slug FROM hosts");
foreach($host as $h){
	$xml .= '<sitemap>
      <loc>http://www.craftstats.com/host/'.$h['slug'].'</loc>
   </sitemap>';
}

 $vs = array_reverse($database->query("SELECT category FROM servers WHERE category != '' GROUP BY category")); 
  foreach($vs as $vb){
	$xml .= '<sitemap>
      <loc>http://www.craftstats.com/category/'.urlencode($vb['category']).'</loc>
   </sitemap>';
  }
  
   $vs = array_reverse($database->query("SELECT version FROM versions ORDER BY time DESC, percent DESC LIMIT 5")); 
  foreach($vs as $vb){
	$xml .= '<sitemap>
      <loc>http://www.craftstats.com/version/'.urlencode($vb['version']).'</loc>
   </sitemap>';
  }

$xml .= '

</sitemapindex>';

file_put_contents('/var/www/cstats/public_html/sitemap.xml', $xml);
?>