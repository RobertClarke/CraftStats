<?php
$memcache_disable = true;

include '../inc/global.inc.php';
if($_SESSION['username'] == ''){
	header("Location: /login?post=/promote/bid");
}



$auctionend = mktime(0,0,0,date("n"),24);
$auctionid=date('n').'-'.date('Y');

if(time() > $auctionend){
	$running = false;
}else{
	$running = true;
}


if($_POST['ip']){
	$sv = $database->query("SELECT * FROM servers WHERE ((resolved = '$_POST[ip]' AND resolved != '') OR ip = '$_POST[ip]') AND game = 'minecraft'",db::GET_ROW);
	if($database->num_rows == 1){
		$svvalid = true;
		$svid = $sv['ID'];
	}
}
$bids = $database->query("SELECT * FROM promo_bids WHERE auctionID = '$auctionid' ORDER BY amount DESC LIMIT 3");

$startbid = 100;
foreach($bids as $b){
	$startbid = max($startbid,$b['amount']+10);
}


if($_POST['bid']){
	if(!is_numeric($_POST['bid'])){
		$badbid=true;
	}
	
	if($_POST['bid'] < $startbid){
		$invalidbid = true;
	}
	
	if($_POST['ip'] && $svvalid){
		$time = time();
		$database->query("INSERT INTO promo_bids VALUES ('','$auctionid','$time','$_SESSION[id]','$_POST[ip]','$_POST[bid]','','','','')");
		$bids = $database->query("SELECT * FROM promo_bids WHERE auctionID = '$auctionid' ORDER BY amount DESC LIMIT 3");
		foreach($bids as $b){
			$startbid = max($startbid,($b['amount']+10));
		}
	}
}


if($_GET['pay']){
	$bid = $database->query("SELECT * FROM promo_bids WHERE won = '1' AND id = '$_GET[pay]' AND auctionID = '$auctionid'",db::GET_ROW);
	if($database->num_rows == 0){
		header('Location: /promote/bid');
	}
	include '../lib/httprequest.php';
	include '../lib/paypal.php';
	

	$r = new PayPal(true);
	$r->pp_return = 'http://craftstats.com/bid?pp=paid';
	$r->pp_cancel = 'http://craftstats.com/promote/bid';
	$ret = $r->doExpressCheckout($bid['amount'], '30 day banner promotion for '.$bid['serverIP']);

	if($ret['ACK'] == 'Success'){
		$token = $ret['TOKEN'];
		$database->query("UPDATE promo_bids SET token = '$token' WHERE id = '$bid[id]'");
	}
}

if($_GET['pp'] == 'paid' && $_GET['ttoken']){
	require_once( '../lib/httprequest.php' );
	require_once( '../lib/paypal.php' );
	$r = new PayPal(true);
	if(!$_GET['ttoken'])$final = $r->doPayment();
	if ($final['ACK'] == 'Success' || $_GET['ttoken']) {
		$token = $final['TOKEN'];
		if($_GET['ttoken'])$token = $_GET['ttoken'];
		$database->query("UPDATE promo_bids SET paid = 1 WHERE token = '$token'",db::GET_ROW);
		$stime = (60*60*24*30) + time();
		$haspaid=true;
	}
}
$template->setHeadScripts('<script> 
		if (window != top) { 
	          top.location.replace(document.location); 
		} 
	   </script> ');
$template->setTitle('Banner Auction');
$template->show('header');
$template->show('nav');
?>
<div class="row">
	<div class="twelve columns">
		<div class="twelve columns box" style="margin-bottom:20px;">
			<div class="six columns">
				<h3>Banner Auction</h3>
			</div>
			<div class="six columns" style="color:#666;font-size:12px;padding-top:25px;">
			<?php
				$diff=$auctionend-time();
				$days=floor($diff/(60*60*24));
				$hours=round(($diff-$days*60*60*24)/(60*60));
				echo 'This auction ends in '.$days.' day'.($days != 1 ? 's':'').', '.$hours.' hour'.($hours != 1 ? 's':'').'.';
			?>
			</div>
		</div>
	</div>
</div>
<?php 
if($haspaid){
?>
<div class="alert-box success"  style="margin-top:20px;">
	You've successfully paid for your bid. Your server promotion will start at the beginning of next month.
	</div>
<?php
}
?>
<div class="row">
	<div class="twelve columns">
		<div class="twelve columns box" style="padding:10px;margin-top:0px;margin-bottom:15px;line-height:1.4;color:#333;">
			This auction will end on <?php echo date('F jS, g:ia T',$auctionend);?>. The top three bids will have exactly 72 hours to pay their bids. In the event that a bid is not paid, the bidder will be restricted from bidding in any other auctions. The <strong>top 3 bidders'</strong> ads will be featured on our front page after payment. Obviously, the 3 top bidders will be the only users payingn after the auction ends. Good luck!
		</div>
	</div>
</div>
<div class="row">
	<div class="twelve columns">
		<div class="servers">
			<div class="row table">
				<div class="six columns">
					<b>Top Bids</b>
					
					<table class="twelve">
						<thead>
							<tr>
								<th>#</th>
								<th>Server</th>
								<th>Bid Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php
								if($database->num_rows == 0){
								?>
								<tr>
									<td></td>
									<td>No bids have been placed.</td>
									<td></td>
								</tr>
								<?php
								}else{
								
									$i=1;
									foreach($bids as $b){
										echo '<tr>
									<td>'.$i.'</td>
									<td>'.$b['serverIP'].'</td>
									<td>$'.$b['amount'].'</td>
								</tr>';
								$i++;
									}
								}
								
							?>
						</tbody>
					</table>
				</div>
				<div class="six columns">
					<b>Place a bid (Minimum $<?php echo $startbid; ?>)</b>
<?php if($_POST['ip'] && !$svvalid){ ?>
						<div class="alert-box"  style="margin-top:20px;">
						We're not currently tracking that server! Make sure you entered the IP address correctly and try again.
						</div>
					<?php } ?>
					
					<?php 
					if($invalidbid){
					?>
					<div class="alert-box"  style="margin-top:20px;">
						That bid was lower than the minimum bid of $<?php echo $startbid; ?>.
						</div>
					<?php
					}
					?>
					<?php 
					if($badbid){
					?>
					<div class="alert-box"  style="margin-top:20px;">
						Invalid bid.
						</div>
					<?php
					}
					?>
					<div class="twelve columns box" style="padding-left:0px;padding-right:15px;padding-top:10px;padding-bottom:10px;">
							<?php if($running){ ?>
							<form action="/promote/bid" method="post">
								<div class="six columns">
									<input type="text" name="bid" placeholder="<?php echo $startbid; ?>" value="<?php echo $_POST['bid']; ?>" />
								</div>
								<div class="six columns">
									<input type="text" name="ip" placeholder="Server IP" />
								</div>
								<div class="twelve columns">
									<button class="button expand">Submit</button>
								</div>
							</form>
							<?php }else{
							?>
							<div class="twelve columns">
							All bids have ended for this month. Come back in <?php echo floor((mktime(0,0,0,date("n")+1,1)-time())/86400);?> days.
							</div>
							<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$template->show('footer');
?>
