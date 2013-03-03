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
	
	$prices = array(1=>25,2=>40);
	
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
			'Check out this Minecraft server! http://cstats.co/'.$sv['ip'],
			'This  Minecraft server looks pretty awesome! http://cstats.co/'.$sv['ip'],
			'This is a great Minecraft server http://cstats.co/'.$sv['ip'],
			'If you have a second be sure to say hi to this Minecraft server http://cstats.co/'.$sv['ip'],
			'This Minecraft server gets the thumbs up from CraftStats http://cstats.co/'.$sv['ip']);
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
$template->show('logo');
?>
</div>
<div id="container" class="clearfix">

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
//$instock = false;
?>
	<div class="box boxtop clearfix" style="padding-left:30px;padding-top:10px;">
<h2 style="float:left;">Promote a Server</h2>
</div>
<div class="box boxbottom clearfix" style="padding-left:30px;padding-top:10px;">

		<div style="float:left;width:650px;">
			<div style="width:620px;float:left;margin-top:15px;color:#777;font-size:14px;padding:15px;border-radius:10px;background:#fff;">
				<span style="font-weight:bold;font-size:18px;">thousands of minecraft players visit craftstats to find servers</span><br/> you can purchase a slice of that traffic for your own server!
			</div>
			
			<div style="float:left;margin-top:15px;margin-left:15px;">
				<h2 style="color:#333;">What you'll get</h2>
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
			<div style="float:left;margin-left:15px;margin-top:20px;font-size:12px;">
				If your purchase is ontop of an existing plan, it will extend your subscription.
			</div>
		</div>

		<div style="width:240px;float:right;" class="clearfix">
			<div class="box boxmiddle inset" style="padding:12px 25px;height:170px;width:150px;margin:15px 0px;position:relative;">
				<h2 style="text-align:center;margin-bottom:-10px;" >One Month</h2>
				<h2 style="text-align:center;color:#3A87AD;font-size:42px;font-weight:bold;">$25</h2>
				<?php echo ($instock?'<form class="form-inline" action="/promote" method="post">':''); ?>
					<input type="hidden" name="buym" value="1">
					<input type="text" name="promoip" class="input-medium" placeholder="Server IP"  value="<?php if($_POST['buym'] == 1)echo $_POST['promoip']; ?>" style="position:relative;right:7px;margin-bottom:7px;">
					<button class="btn btn-large btn-block btn-info <?php echo ($instock?'':'disabled'); ?>" type="submit"><?php echo ($instock?'Buy now':'Sold out!'); ?></a>
				<?php echo ($instock?'</form>':''); ?>
			</div>
			
			<div class="box boxmiddle inset" style="padding:12px 25px;height:170px;width:150px;margin:15px 0px;position:relative;">
				<h2 style="text-align:center;margin-bottom:-10px;">Two Months</h2>
				<h2 style="text-align:center;color:#3A87AD;font-size:42px;font-weight:bold;">$40</h2>
				<?php echo ($instock?'<form class="form-inline" action="/promote" method="post">':''); ?>
				
					<input type="hidden" name="buym" value="2">
					<input type="text" name="promoip" class="input-medium" placeholder="Server IP"  value="<?php if($_POST['buym'] == 2)echo $_POST['promoip']; ?>" style="position:relative;right:7px;margin-bottom:7px;">
					<button class="btn btn-large btn-block btn-info <?php echo ($instock?'':'disabled'); ?>" type="submit"><?php echo ($instock?'Buy now':'Sold out!'); ?></a>
				<?php echo ($instock?'</form>':''); ?>
			</div>
		</div>
		
	</div> 
</div> 
<?php
$template->show('footer');
?>