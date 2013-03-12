<?php
include '../inc/global.inc.php';
$svvalid = false;
if($_POST['buym'] > 0){
	$sv = $database->query("SELECT * FROM servers WHERE (resolved = '$_POST[promoip]' AND resolved != '') OR ip = '$_POST[promoip]'",db::GET_ROW);
	if($database->num_rows == 1){
		$svvalid = true;
		$svid = $sv['ID'];
	}
}
if($_POST['buym'] > 0 && $svvalid){
	require_once( '../lib/httprequest.php' );
	require_once( '../lib/paypal.php' );
	$r = new PayPal(true);
	
	$prices = array(1=>20,2=>35);
	
	$ret = ($r->doExpressCheckout($prices[$_POST['buym']], $_POST['buym'].' Month Promotion for server '.$_POST['promoip']));

	if ($ret['ACK'] == 'Success') {
		$token = $ret['TOKEN'];
		
		$cost = $prices[$_POST['buym']].'.00';
		
		$database->query("INSERT INTO promo_order VALUES ('$token','$svid','$_POST[buym]','$_POST[promocode]','$cost','0',0,'','','')");
		exit;
	}
}

if($_GET['paypal'] == 'paid'){
	require_once( '../lib/httprequest.php' );
	require_once( '../lib/paypal.php' );
	include_once '../lib/twitteroauth.php';
	$r = new PayPal(true);
	
	$final = $r->doPayment();
	
	if ($final['ACK'] == 'Success') {
		$token = $final['TOKEN'];
		$order = $database->query("SELECT * FROM promo_order WHERE token = '$token'",db::GET_ROW);
		if($order['paid'] == 0 && $database->num_rows == 1){
			$sv = $database->query("SELECT * FROM servers WHERE ID = '$order[serverID]'",db::GET_ROW);
			
			$cstats = new TwitterOAuth('HyI8Rfv5NwhU2pP3pZ3TA', 'nKVSmnejMIgRBWZT2ZSOJAHTzslBo2ZmHhqxvG7otM','822604988-MrKWIjH8xH3eb5TvI6d0XIowqnkV3FE1YLE6u2zq','J9TiF64znmZaR3I4zxFAyB0HeNJbvlU8mQCuXbNnd78');
			
			$responses = array(
			'Check out this server! http://cstats.co/'.$sv['ip'],
			'This server looks pretty awesome! http://cstats.co/'.$sv['ip'],
			'This is a great server: http://cstats.co/'.$sv['ip']);
			$cstats->post('statuses/update', array('status' => $responses[array_rand($responses)])); 
			
			$stime = ($order['length']*60*60*24*31) + max($sv['sponsorTime'],time());
			
			$database->query("UPDATE promo_order SET paid = '1', expire = '$stime', first='{$r->details[FIRSTNAME]}',last='{$r->details[LASTNAME]}',email='{$r->details[EMAIL]}' WHERE token = '$token'");
			
			$database->query("UPDATE servers SET sponsorTime = '$stime' WHERE ID = '$order[serverID]'");
		}
	}
}

$template->setHeadScripts('<script> 
		if (window != top) { 
	          top.location.replace(document.location); 
		} 
	   </script> ');

$template->setTitle('Promote a Minecraft Server');
$template->show('header');
$template->show('nav');
?>
<div class="row">
	<div class="twelve columns">
		<div class="twelve columns box">

<?php
if(!$svvalid && $_POST['buym'] > 0){
	?>
		<div class="alert alert-info">
			We're not currently tracking that server! Make sure you entered the IP address correctly and try again.
		</div>
	<?php
}
?>

<?php
if ($final['ACK'] == 'Success') {
	?>
		<div class="alert alert-success">
			 Thank you for your payment, your server is now being promoted!
		</div>
	<?php
}
$database->query("SELECT * FROM servers WHERE sponsorTime > UNIX_TIMESTAMP()"); 
if($database->num_rows < 18){
	$instock = true;
}else{
	$instock = false;
}
?>

<h3>Promote a Server</h3>
			<div style="width:100%;margin-top:15px;color:#777;font-size:14px;padding:15px;border-radius:10px;background:#eee;">
				<span style="font-weight:bold;font-size:18px;">thousands of minecraft players visit craftstats to find servers</span><br/><br/> you can purchase a slice of that traffic for your own server!
			</div>

			<div class="row">
				<div class="seven columns"><div style="float:left;margin-top:15px;margin-left:15px;">
					<h4 style="color:#333;">What you'll get</h4>
					<ul style="list-style:disc inside;margin-left:15px;margin-top:5px;font-size:14px;">
						<li>Featured listing on the front page</li>
						<li>Server banners around the website</li>
						<li>Tweet from the craftstats twitter account</li>
					</ul>
				</div>
				<!--<div style="width:620px;float:left;margin-top:0px;color:#777;font-size:14px;padding:15px;border-radius:10px;background:#fff;margin-top: 50px;">
					<h2 style="color:#333;font-weight: bold;">Black Friday/Cyber Monday Sale!</h2>
					<div style="margin-left: 15px;float: left;margin-top:5px;">
						Get 50% off any promoted server plan! You can bring dozens or hundreds of new players to your server, starting from $10 for one month*.
					</div>
				</div>-->
				<div style="float:left;margin-left:15px;margin-top:20px;font-size:10px;color:#aaa;">
					If your purchase is ontop of an existing plan, it will extend your subscription.
				</div>
				</div>
				<div class="five columns" style="padding:10px 40px;">
					<div class="row">
						<h5 style="text-align:center;margin-bottom:-10px;" >One Month</h5>
						<h5 style="text-align:center;color:#3A87AD;font-size:42px;font-weight:bold;">$20</h5>
						<?php echo ($instock?'<form class="form-inline" action="/promote" method="post">':''); ?>
							<input type="hidden" name="buym" value="1">
							<input type="text" name="promoip" class="input-medium" placeholder="Server IP"  value="<?php if($_POST['buym'] == 1)echo $_POST['promoip']; ?>">
							<button class="button expand" type="submit"><?php echo ($instock?'Buy now':'Sold out!'); ?></a>
						<?php echo ($instock?'</form>':''); ?>
					</div>
					<div class="row">
						<h5 style="text-align:center;margin-bottom:-10px;">Two Months</h5>
						<h5 style="text-align:center;color:#3A87AD;font-size:42px;font-weight:bold;">$35</h5>
						<?php echo ($instock?'<form class="form-inline" action="/promote" method="post">':''); ?>
						
							<input type="hidden" name="buym" value="2">
							<input type="text" name="promoip" class="input-medium" placeholder="Server IP"  value="<?php if($_POST['buym'] == 2)echo $_POST['promoip']; ?>">
							<button class="button expand"  type="submit"><?php echo ($instock?'Buy now':'Sold out!'); ?></a>
						<?php echo ($instock?'</form>':''); ?>
					</div>
				</div>
			</div>
		</div> 
	</div> 
</div> 
<?php
$template->show('footer');
?>