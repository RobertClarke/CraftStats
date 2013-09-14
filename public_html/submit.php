<?php
include '../inc/global.inc.php';

$template->setTitle('Submit a Server');
$template->show('header');
$template->show('nav');
?>
<div class="row">
	<div class="twelve columns">
		<div class="twelve columns box">
			<div class="row">
				<div class="twelve columns">
					<h5>Submit a Server</h5>
				</div>
			</div>
			<div class="row">
				<div class="six columns">
					<?php if($_GET['se']){ ?>
					<div class="alert-box alert">
					<?php echo $_GET['se']; ?>
					</div>
					<?php } ?>
					<form action="/api" method="get">
					<input type="hidden" name="req" value="m11"/>
					<input type="text" name="ip" placeholder="Server Address"/>
					<div class="row collapse">
							<div class="eight mobile-three columns">
							  <input type="text" name="email" placeholder="Email (optional)" <?php echo($_GET['ev']?'value="'.$_GET['ev'].'"':''); ?> />
							</div>
							<div class="four mobile-one columns">
							  <button class="button expand postfix" style="padding:0px;">Submit</button>
							</div>
					</div>
					</form>
				</div>
				<div class="six columns">
					<p>Adding your server to our site means we'll track uptime, player activity and other stats depending on your setup.</p>
					
					<p>If you want to increase exposure to your server on the site, get players to vote for it! Send them to your server vote page, and when they vote you'll be pushed closer to the front page of CraftStats.com.</p>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
$template->show('footer');
?>
