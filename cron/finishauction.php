<?php
set_include_path('/var/www/cstats/');
$memcache_disable = true;
include 'inc/global.inc.php';
$auctionid=date('n').'-'.date('Y');

$bids = $database->query("SELECT * FROM promo_bids WHERE auctionID = '$auctionid' ORDER BY amount DESC LIMIT 3");

foreach($bids as $b){
$user = $database->query("SELECT * FROM users WHERE id = '$b[userID]'",db::GET_ROW);
$database->query("UPDATE promo_bids SET won = 1 WHERE id = '$b[id]'");
$to      = $user['email'];
$subject = 'CraftStats.com - You\'ve won this month\'s auction.';
$message = '<img src="http://craftstats.com/images/logo.png"/><br/><br/>
<b>Congratulations on winning the auction!<b><br/>
Please pay for your bid within 72 hours, or you may be restricted from participating in future auctions. Also, make sure you set a banner on your server page!
<br/><br/> <a href="http://craftstats.com/bid?pay='.$b['id'].'">Click here to activate your banner.</a>';
$headers = 'From: noreply@craftstats.com'."\r\n";
$headers  .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
mail($to,$subject,$message,$headers);
echo $to;
}

?>
