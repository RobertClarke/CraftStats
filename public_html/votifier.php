<?php
include '../inc/global.inc.php'; 
$template->setTitle('Votifier');
$template->show('header');
$template->show('nav');
$template->show('logo');
?>
</div>
	<div id="container" class="clearfix">
	<div class="box boxleft clearfix" style="padding:20px;width:560px;float:left;height:660px;font-size:14px;"> 
		<p style="font-size:28px;color:#333;font-weight:bold;">Votifier and CraftStats</p>
		<p>Are you interested in driving more traffic to your Minecraft server? Other than our <a href="http://www.craftstats.com/promote">server promotion options</a>, Votifier is the best way of getting more people playing on your server. Interested? Here's how to getting it running on your server.</p>
		</br>
		<p><strong>1) </strong>You'll need to get the <a href="http://dev.bukkit.org/server-mods/votifier/">Votifier Bukkit plugin</a> and an appropriate <a href="http://dev.bukkit.org/server-mods/votifier/forum/vote-listeners/">"Votifier Listener"</a> installed onto your Minecraft server before we continue. We recommend the <a href="http://dev.bukkit.org/server-mods/votifier/forum/vote-listeners/44205-give-anything/#p1">Give Anything Listener</a>, because it makes it easy to reward your players with in-game items.</p>
		</br>
		<p><strong>2) </strong>Once you've got the plugin and listener installed, you'll need to restart or reload your server. Look in the "/plugins/votifier/" folder of your root Minceraft server folder, and find the base Votifier config file. In this file, you will see all the information you need to get Votifier working with CraftStats. While you have this folder open, take a look at the listener config files and edit them so that it will give your players whatever you would like after they vote. Then perform another server reload.</p>
		</br>
		<p><strong>3) </strong>Now go to your server voting page. To do this, simply enter this URL into your browser: "http://craftstats.com/server/*YourServerIp*/vote", or click "voting" on your CraftStats server page. Insert the information from the Votifier config file into CraftStats, and click "Update Votifier."</p>
		</br>
		<p><strong>4) </strong>That's it! Now get your players to vote for your server by visiting your CraftStats voting page, and they'll get a bonus whenever they vote (they can vote once every day), and your server will rise in our server ranks, bringing even more players to your server.</p>
		
	</div>
	
	<div class="box boxright clearfix" style="width:316px;padding:20px;height:660px;float:right;">
		<h2 style="text-align: left;margin-bottom:10px;">What is Votifier?</h1>
		<p>Votifier is a Bukkit plugin whose purpose is to alert a server when a vote is made on a Minecraft server list website for that specific server. Votifier allows these server list websites to "ping" the plugin whenever a vote is made and, using a simple protocol, relays information on the voter. Votifier is and always will be open-source.</p>
		<h2 style="text-align: left;margin-bottom:10px;">Why should I use it?</h1>
		<p>Essentially, Votifier is all about rewarding your players for voting for your server on places such as CraftStats. You can get Votifier "listeners(actions to be taken)" to reward your players on your server after they vote. When they vote, your server climbs in the CraftStats server ranking system, bringing even more awareness and, therefore, more players to your server.</p>
		
	</div>
<?php
$template->show('footer');
?>