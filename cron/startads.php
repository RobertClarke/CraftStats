<?php
set_include_path('/var/www/cstats/');
$memcache_disable = true;
include 'inc/global.inc.php';

$endtime = time()+(60*60*24*30);

// this MUST be run in the NEXT month (if this is run in august it will process the auction that finished in july)
$auctionid=(date('n')-1).'-'.date('Y');

$bids = $database->query("SELECT * FROM promo_bids WHERE auctionID = '$auctionid' AND paid = 1 AND won = 1 AND `set` = 0 ORDER BY amount DESC LIMIT 3");
foreach($bids as $b){
	$database->query("UPDATE servers SET bannerpromo = '$endtime' WHERE ip = '$b[serverIP]'");
	$database->query("UPDATE promo_bids SET `set` = 1 WHERE id = '$b[id]'");
}
?>