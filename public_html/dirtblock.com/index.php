<html>

<?php include 'inc/php/header.php'; ?>

<body>
	<div id="powered">
		<span><a href="http://www.craftstats.com">Powered by CraftStats.com</a> - Want to contribute? <a href="http://dev.bukkit.org/server-mods/dirtblock/">get the plugin</a></span>
		<br/><br/>
		<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.dirtblock.com" data-text="Check out these awesome minecraft stats!" data-related="craftstats_" data-hashtags="minecraft">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		<div class="fb-like" style="position:relative;bottom:4px;margin-right:30px;" data-href="http://dirtblock.com" data-send="false" data-layout="button_count" data-width="450" data-show-faces="true"></div>
		
		<!-- Place this tag where you want the +1 button to render. -->
<div class="g-plusone" data-size="medium"></div>

<!-- Place this tag after the last +1 button tag. -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
	</div>

	<div id="container">
		<div id="greenbar" class="bar">
			<img src="inc/img/dirtblock.png">
			<div style="position:relative;"><h1 id="bbroken"></h1></div>
			<h2>Total Blocks Broken</h2>
		</div>
		
		<div id="purplebar" class="bar">
			<img src="inc/img/woodblock.png">
			<div style="position:relative;"><h1 id="bplaced"></h1></div>
			<h2>Total Blocks Placed</h2>
		</div>

		<div id="bluebar" class="bar">
			<img src="inc/img/craftingtable.png">
			<div style="position:relative;"><h1 id="icrafted"></h1></div>
			<h2>Total Items Crafted</h2>
		</div>

		<div id="redbar" class="bar">
			<img src="inc/img/ironsword.png">
			<div style="position:relative;"><h1 id="mkilled"></h1></div>
			<h2>Total Mobs Killed</h2>
		</div>
		<div id="goldbar" class="bar">
			<img src="inc/img/bow.png">
			<div style="position:relative;"><h1 id="ashot"></h1></div>
			<h2>Total Arrows Shot</h2>
		</div>
	</div>

<?php include 'inc/php/footer.php'; ?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=503417166336742";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
</html>