			<div id="nav" class="box boxbottom">
				<ul>
					<a href="/"><li>home</li></a>
					<a href="/players"><li>players</li></a>
					<a href="/about"><li>about</li></a>
					<a href="/promote"><li>get more players</li></a>
					<a href="/hosting"><li>hosting reviews</li></a>
					<a href="http://dirtblock.com"><li>dirtblock</li></a>
					<?php if($_SESSION['username'] == ''){ ?>
					<?php } ?>
					<!--<a href="/stats.php"><li>status (heavy WIP)</li></a> YOU FOUND THE SECRET LINK. IT's SUPER FANCY, ALL THANKS TO @redream_-->
					<?php echo ($_SESSION['username'] != ''? '<li  style="float:right;border-right:none;border-left:1px solid #ddd !important;margin-right:0px;"><form action="/oauth.php" method="get" style="display:inline;"><input type="hidden" name="logout" value="true"/><button class="btn btn-small" style="margin-bottom:-26px;position:relative;bottom:14px;left:3px;1">logout</button></form></li>':'');?>
					<a href="<?php echo ($_SESSION['username'] != '' ? '/account.php' : '/oauth.php?login=twitter')?>"><li style="float:right;border-right:none;border-left:1px solid #ddd;margin-right:0px;<?php echo ($_SESSION['username'] != '' ? '">logged in as <span style="color:#333;font-weight:bold;">@'.$_SESSION['username'].'</span> ' : 'padding:15px 20px 5px 20px;"><img style="width:151px;height:24px;" src="/images/signintwitter.png">' ); ?></li></a>
				</ul>
			</div>