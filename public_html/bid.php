<?php
include '../inc/global.inc.php';
if($_SESSION['username'] == ''){
	header("Location: /login");
}
$template->setTitle('Banner Auction');
$template->show('header');
$template->show('nav');


$auctionend = mktime(0,0,0,date("n"),5);
$auctionid=date('N').'-'.date('Y');

if(time() > $auctionend){
	$running = false;
}else{
	$running = true;
}

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
								$bids = $database->query("SELECT * FROM promo_bids WHERE auctionID = '$auctionid'");
								if($database->num_rows == 0){
								?>
								<tr>
									<td></td>
									<td>No bids have been placed.</td>
									<td></td>
								</tr>
								<?php
								}
							?>
						</tbody>
					</table>
				</div>
				<div class="six columns">
					<b>Place a bid</b>

					<div class="twelve columns box" style="padding-left:0px;padding-right:15px;padding-top:10px;padding-bottom:10px;">
							<?php if($running){ ?>
							<form action="/promote/bid" method="post">
								<div class="six columns">
									<input type="text" name="bid" placeholder="60.12" />
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