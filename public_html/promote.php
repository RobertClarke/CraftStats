<?php
include '../inc/global.inc.php';
$svvalid = false;

if($_POST['buyi'] > 0){
	$sv = $database->query("SELECT * FROM servers WHERE ((resolved = '$_POST[promoip]' AND resolved != '') OR ip = '$_POST[promoip]') AND game = 'minecraft'",db::GET_ROW);
	if($database->num_rows == 1){
		$svvalid = true;
		$svid = $sv['ID'];
	}
}
if($_POST['buyi'] > 0 && $svvalid){
	require_once( '../lib/httprequest.php' );
	require_once( '../lib/paypal.php' );
	$r = new PayPal(true);
	
	$prices = array(1=>20,2=>35,3=>50,4=>90);
	$week = array(1=>1,2=>2,3=>1,4=>2);
	$type = ($_POST['buyi']>2 ? 1:0);
	
	$ret = ($r->doExpressCheckout($prices[$_POST['buyi']], $week[$_POST['buyi']].' Week '.($type == 1 ? 'Premium' : 'Standard').' Promotion for server '.$_POST['promoip']));

	if ($ret['ACK'] == 'Success') {
		$token = $ret['TOKEN'];
		$cost = $prices[$_POST['buyi']].'.00';
		$mf=$week[$_POST['buyi']];
		$database->query("INSERT INTO promo_order VALUES ('$token','$svid','$mf','$type','$cost','0',0,'','','')");
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
			$sv = $database->query("SELECT * FROM servers WHERE game = 'minecraft' AND ID = '$order[serverID]'",db::GET_ROW);
			
			$responses = array(
			'Check out this server! http://cstats.co/'.$sv['ip'],
			'This server looks pretty awesome! http://cstats.co/'.$sv['ip'],
			'This is a great server: http://cstats.co/'.$sv['ip']);
			
			$tmhOAuth = new tmhOAuth(array(
				'consumer_key'    => 'LikmqUGSLAAWgZ8zCVC2A',
				'consumer_secret' => '6PlWlXC6ugcwpY0SrlZ48uvc9KNHCPpVhpGjH6O6U',
				'user_token'      => '822604988-MrKWIjH8xH3eb5TvI6d0XIowqnkV3FE1YLE6u2zq',
				'user_secret'     => 'J9TiF64znmZaR3I4zxFAyB0HeNJbvlU8mQCuXbNnd78',
			));

			$code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update'), array(
				'status' => $responses[array_rand($responses)],
			));

			
			
			$stime = ($order['length']*60*60*24*7) + max($sv['sponsorTime'],time());
			
			$database->query("UPDATE promo_order SET paid = '1', expire = '$stime', first='{$r->details[FIRSTNAME]}',last='{$r->details[LASTNAME]}',email='{$r->details[EMAIL]}' WHERE token = '$token'");
			
			$database->query("UPDATE servers SET sponsorTime = '$stime', sponsorType = '$order[type]' WHERE ID = '$order[serverID]'");
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
	<?php
if(!$svvalid && $_POST['buyi'] > 0){
	?>
		<div class="alert-box"  style="margin-top:20px;">
			We're not currently tracking that server! Make sure you entered the IP address correctly and try again.
		</div>
	<?php
}
?>

<?php
if ($final['ACK'] == 'Success') {
	?>
		<div class="alert-box success"  style="margin-top:20px;">
			 Thank you for your payment, your server is now being promoted!
		</div>
	<?php
}
$database->query("SELECT * FROM servers WHERE sponsorTime > UNIX_TIMESTAMP() && sponsorType = 0"); 
if($database->num_rows < 18){
	$instock = true;
}else{
	$instock = false;
}

$a = $database->query("SELECT * FROM servers WHERE sponsorTime > UNIX_TIMESTAMP() AND blacklisted != 1 AND sponsorType = 1 AND game = 'minecraft'"); 
if($database->num_rows == 3){
	$instock2 = false;
}else{
	$instock2 = true;
}
?>

		<div class="twelve columns box">

			<div class="row" style="padding:20px;">
				<h3><?php echo ($_GET['ip'] ? 'Sponsor '.$_GET['ip'] : 'Promote a Minecraft Server'); ?></h3>
				<div style="width:100%;margin-top:15px;color:#777;font-size:14px;padding:15px;border-radius:10px;background:#eee;">
					<span style="font-weight:bold;font-size:18px;">thousands of minecraft players visit craftstats to find servers</span><br/><br/> you can purchase a slice of that traffic for <?php echo ($_GET['ip'] ? $_GET['ip'] : 'your own server'); ?>!
				</div>

				<div class="row">
					<div class="seven columns"><div style="float:left;margin-top:15px;margin-left:15px;">
						<h4 style="color:#333;">What you'll get</h4>
						<ul style="list-style:disc inside;margin-left:15px;margin-top:5px;font-size:14px;">
							<li>Featured listing on the front page</li>
							<li>Minecraft Server banners around the website</li>
							<li>Tweet from the CraftStats twitter account</li>
						</ul>
					</div>
					<img src="/images/promprev.png" style="margin-top:10px;border-radius:5px;"/>
					<div style="float:left;margin-left:15px;margin-top:20px;font-size:10px;color:#aaa;">
						If your purchase is ontop of an existing plan, it will extend your subscription.
					</div>
					</div>
					<div class="five columns" style="padding:10px 40px;">
						<div class="row">
							<h5 style="text-align:center;margin-bottom:-10px;" >One Week</h5>
							<h5 style="text-align:center;color:#3A87AD;font-size:42px;font-weight:bold;">$20</h5>
							<?php echo ($instock?'<form class="form-inline" action="/promote" method="post">':''); ?>
								<input type="hidden" name="buyi" value="1">
								<input type="text" name="promoip" class="input-medium" placeholder="Server IP"  value="<?php if($_POST['buym'] == 1)echo $_POST['promoip'];  if($_GET['ip'])echo $_GET['ip']; ?>">
								<button class="button expand" type="submit"><?php echo ($instock?'Buy now':'Sold out!'); ?></a>
							<?php echo ($instock?'</form>':''); ?>
						</div>
						<div class="row">
							<h5 style="text-align:center;margin-bottom:-10px;">Two Weeks</h5>
							<h5 style="text-align:center;color:#3A87AD;font-size:42px;font-weight:bold;">$35</h5>
							<?php echo ($instock?'<form class="form-inline" action="/promote" method="post">':''); ?>
							
								<input type="hidden" name="buyi" value="2">
								<input type="text" name="promoip" class="input-medium" placeholder="Server IP"  value="<?php if($_POST['buym'] == 2)echo $_POST['promoip']; if($_GET['ip'])echo $_GET['ip']; ?>">
								<button class="button expand"  type="submit"><?php echo ($instock?'Buy now':'Sold out!'); ?></a>
							<?php echo ($instock?'</form>':''); ?>
						</div>
					</div>
				</div>
			</div> 

		
		<div class="row" style="padding:20px;">
				<h3>Premium Promotion</h3>
				<div style="width:100%;margin-top:15px;color:#777;font-size:14px;padding:15px;border-radius:10px;background:#eee;">
					Looking to really give your server a boost? Grab one of our highlighted spots. These'll sit right along the top of the page, sure to catch the attention of viewers.
				</div>

				<div class="row">
					<div class="seven columns">
					<img src="/images/premprev.png" style="margin-top:10px;border-radius:5px;"/>
					<div style="float:left;margin-left:15px;margin-top:20px;font-size:10px;color:#aaa;">
						If your purchase is ontop of an existing plan, it will extend your subscription.
					</div>
					</div>
					<div class="five columns" style="padding:10px 40px;">
						<div class="row">
							<h5 style="text-align:center;margin-bottom:-10px;" >One Week</h5>
							<h5 style="text-align:center;color:#3A87AD;font-size:42px;font-weight:bold;">$50</h5>
							<?php echo ($instock2?'<form class="form-inline" action="/promote" method="post">':''); ?>
								<input type="hidden" name="buyi" value="3">
								<input type="text" name="promoip" class="input-medium" placeholder="Server IP"  value="<?php if($_POST['buym'] == 3)echo $_POST['promoip']; if($_GET['ip'])echo $_GET['ip']; ?>">
								<button class="button expand" type="submit"><?php echo ($instock2?'Buy now':'Sold out!'); ?></a>
							<?php echo ($instock2?'</form>':''); ?>
						</div>
						<div class="row">
							<h5 style="text-align:center;margin-bottom:-10px;">Two Weeks</h5>
							<h5 style="text-align:center;color:#3A87AD;font-size:42px;font-weight:bold;">$90</h5>
							<?php echo ($instock2?'<form class="form-inline" action="/promote" method="post">':''); ?>
							
								<input type="hidden" name="buyi" value="4">
								<input type="text" name="promoip" class="input-medium" placeholder="Server IP"  value="<?php if($_POST['buym'] == 4)echo $_POST['promoip'];  if($_GET['ip'])echo $_GET['ip']; ?>">
								<button class="button expand"  type="submit"><?php echo ($instock2?'Buy now':'Sold out!'); ?></a>
							<?php echo ($instock2?'</form>':''); ?>
						</div>
					</div>
				</div>
			</div> 
		</div>
	</div> 
</div> 
<?php
$template->show('footer');
?>
