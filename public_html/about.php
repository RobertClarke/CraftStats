<?php
include '../inc/global.inc.php'; 
$template->setTitle('About');
$template->show('header');
$template->show('nav');
$template->show('logo');
?>
</div>
	<div id="container" class="clearfix">
	<div class="box boxleft clearfix" style="padding:20px;width:560px;float:left;height:760px;font-size:14px;"> 
		<h1 style="text-align: left;">About</h1>
		<p>CraftStats is the product of an idea that Thomas and his friend Edward had one in one of their 'late night brainstorms'. They joined up with a good friend and web developer, Alexander Miller, to draw up the concept of a Minecraft server tracker with many advanced features, and thus, CraftStats was born.</p></br>
		<p><b>On September 4th 2012</b>, Robert joined the team, and helped push CraftStats out onto the web.</p></br>
		<p><b>On September 6th</b>, the Craftstats team unveiled many new features, including the top servers table, players and stats page and many new development features.</p></br>
		<p><b>On September 7th</b>, Alex finished the final tweaks for the new account system. Powered by Twitter, the account feature paves the way for many advanced features designed with the server admin in mind.</p></br>
		<p><b>By September 9th</b>, the automated tracking system had performed over 654,130 updates over 626 servers and 1,641 plugins. It was also tracking over 112,417 players, which is over 1.5% of all premium Minecraft accounts.</p><br/>
		<p><b>On September 15th</b>, Chris joined CraftStats to help develop a Bukkit plugin, enabling vast statistics to be tracked on the activity of players. The CraftStats network was expanding rapidly, with over 1100 servers and 2 million updates performed.</p><br/>
		<p><b>On October 29th</b>, Jeb <a href="https://twitter.com/jeb_/status/262834244051689472">tweeted</a> a link to CraftStats, bringing lots of exposure to the website.</p><br/>
		<p><b>On November 14th</b>, CraftStats hit 1,000,000 million players tracked in the database.</p><br/>
		<p><b>On November 30th</b>, CraftStats started to track servers with 1,000,000 updates every 24 hours, which is just under 700 updates per minute.</p><br/>
		<p><b>On December 16th</b>, CraftStats launched the <a href="/hosting">hosting</a> page, containing information and reviews from hosting companies around the world.</p><br/>
	</div>
	
	<div class="box boxright clearfix" style="width:316px;padding:20px;height:760px;float:right;">
		<h1 style="text-align: left;margin-bottom:10px;">The Team</h1>
		<ul>
			<style>
				.boxright ul img{
					box-shadow:0px 0px 4px #AAA;
					border:1px solid #999;
				}
				
				.boxright ul li{
					padding-bottom:8px;
					position:relative;
				}
				
				.boxright ul li p{
					position:relative;bottom:30px;left:10px;
					display:inline-block;
					font-size:12px;
				}
			</style>
			<?php
				$team = array(
				array(
				'Robert Clarke','Project Manager','RobertJFClarke','robertjfclarke'
				),
				array(
				'Alexander Miller','Lead Developer','MillerMan','redream_'
				),
				array(
				'Chris Wood','Web & Java Developer','Chris1056','chrisoneillwood'
				),
				 
				
				
				);
				
				foreach($team as $m){
			?>
			<li>
				<img mcuser="<?php echo $m[2]; ?>" mcsize="48"/>
			
				<p style="font-size:20px;color:#333;font-weight:bold;">
					<?php echo $m[0]; ?>
				</p> 
				
				<p style="position:absolute;bottom:45px;left:75px;">
				<?php echo $m[1]; ?>
				
				</p>
				
				<a href="https://twitter.com/<?php echo $m[3]; ?>" class="twitter-follow-button" data-show-count="true" data-show-screen-name="false" data-lang="en">Follow</a></li>
			<?php } ?>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</ul>
</div>
<?php
$template->show('footer');
?>