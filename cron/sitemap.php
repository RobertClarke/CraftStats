<?php
include '../inc/global.inc.php';
$xml  = '';
$xml .='<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<url>
      <loc>http://craftstats.com/</loc>
      <priority>1</priority>
      <changefreq>always</changefreq>
   </url>
   <url>
      <loc>http://craftstats.com/hosting</loc>
   </url>
     <url>
      <loc>http://craftstats.com/submit</loc>
   </url>
   <url>
      <loc>http://craftstats.com/players</loc>
   </url>
   <url>
      <loc>http://craftstats.com/promote</loc>
   </url>
    <url>
      <loc>http://craftstats.com/category/new</loc>
      <changefreq>always</changefreq>
      <priority>0.8</priority>
   </url>
    <url>
      <loc>http://craftstats.com/category/active</loc>
      <changefreq>always</changefreq>
      <priority>0.8</priority>
   </url>
    <url>
      <loc>http://craftstats.com/category/reliable</loc>
      <changefreq>always</changefreq>
      <priority>0.8</priority>
   </url>
';

$servers = $database->query("SELECT ip, lastUpdate FROM servers WHERE game = minecraft AND uptime > -86400");
foreach($servers as $s){
	$xml .= '<url>
      <loc>http://craftstats.com/server/'.$s['ip'].'</loc>
	  <lastmod>'.date(DATE_ATOM,($s['lastUpdate'] == 0 ? time() : $s['lastUpdate'])).'</lastmod>
   </url>';
   $xml .= '<url>
      <loc>http://craftstats.com/server/'.$s['ip'].'/vote</loc>
	  <lastmod>'.date(DATE_ATOM,($s['lastUpdate'] == 0 ? time() : $s['lastUpdate'])).'</lastmod>
   </url>';
}

$host = $database->query("SELECT slug FROM hosts");
foreach($host as $h){
	$xml .= '<url>
      <loc>http://craftstats.com/host/'.$h['slug'].'</loc>
   </url>';
}

 $vs = array_reverse($database->query("SELECT category FROM servers WHERE category != '' GROUP BY category")); 
  foreach($vs as $vb){
	$xml .= '<url>
      <loc>http://craftstats.com/category/'.urlencode($vb['category']).'</loc>
      <priority>0.8</priority>
      <changefreq>always</changefreq>
      
   </url>';
  }
  
   $vs = array_reverse($database->query("SELECT version FROM versions ORDER BY time DESC, percent DESC LIMIT 5")); 
  foreach($vs as $vb){
	$xml .= '<url>
      <loc>http://craftstats.com/version/'.urlencode($vb['version']).'</loc>
      <priority>0.8</priority>
      <changefreq>always</changefreq>
   </url>';
  }

$xml .= '

</urlset>';

file_put_contents('/var/www/cstats/public_html/sitemap.xml', $xml);
?>
