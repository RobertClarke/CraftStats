<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=1397828010439963";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
  <?php global $index; 
  if($index){
?>  <div class="featurebg" style="background:url(/images/bigbg<?php echo rand(1,14); ?>.jpg) no-repeat center;background-size:100%;">
    
  </div> <?php } ?>
  <div class="navigation">
  </div>
  <div id="wrap">
    <div class="row" id="main">
    <div class="three columns sidecontainer">
      <div class="row  box logo"> 
        <a href="http://craftstats.com">
		<img src="/images/logo.png" alt="Minecraft Servers"/>
		</a>
		<hr/>
		<a href="http://mcpestats.com">
		<img src="http://mcpestats.com/images/logo.png" alt="Minecraft PE Servers"/>
		</a>
		<a class="togglelogos"></a>
      </div>
      <div class="row sidebar"> 
         <div class="twelve columns box">
            <div class="adsection hide-for-small" style="margin-bottom:0px;padding-bottom:0px;">
              <div class="row collapse ad">
			   <a href="/refer.php?url=enjin.com" style="margin-left:7px;margin-bottom:10px;display:block;"><img src="http://files.enjin.com/1340/minecraft-server-website-enjin.gif"/></a>
               <a href="/refer.php?url=bit.ly/18r9kzV"><img alt="CubedHost.org" src="/images/ads/cubedhost.png"/></a> 
			  
              </div> 
            </div>
          <h6 class="subheader" style="margin-top:0px;">Find a Minecraft server</h6>
		  <?php if($_GET['sf']){ ?>
			<div class="alert-box alert">
				  Server not found
				  <a href="" class="close">&times;</a>
				</div>
		  <?php } ?>
		  <?php if($_GET['blacklist']){ ?>
			<div class="alert-box alert">
				  Server is blacklisted
				  <a href="" class="close">&times;</a>
				</div>
		  <?php } ?>
          <div class="row collapse">
			<form action="/api" method="get">
				<input type="hidden" name="v1" value="server"/>
				  <input type="hidden" name="req" value="m10"/>
				<div class="eight mobile-three columns">
				  <input type="text" name="v2" placeholder="Server IP"/>
				</div>
				<div class="four mobile-one columns">
				  <button class="button expand postfix" style="padding:0px;">Search</button>
				</div>
			</form>
          </div>
		 
          
		  <a class="button expand" href="/submit" >Add a server</a>
		  <div style="margin-top:13px;margin-left:14px;">
			<div class="fb-like" data-href="https://www.facebook.com/craftstats" data-width="70" data-layout="button_count" data-show-faces="true" data-send="false"></div>

			<a href="https://twitter.com/craftstats" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @craftstats</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
<!-- Place this tag where you want the +1 button to render. -->
<div class="g-plusone" data-size="medium" data-annotation="none"></div>

<!-- Place this tag after the last +1 button tag. -->
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
</div>

<center>
<script type="text/javascript"><!--
google_ad_client = "ca-pub-8782622759360356";
/* CraftStats Long Sidebar */
google_ad_slot = "4934785430";
google_ad_width = 160;
google_ad_height = 600;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
</center>
          <h6 class="subheader">Sort Minecraft Servers</h6>
          <div class="row collapse tags">
		   <a href="/" class="button tiny" >Top Ranked</a>
	<a href="/category/new" class="button tiny secondary" >New</a>
	<a href="/category/reliable" class="button tiny secondary" >Uptime</a>
	<a href="/category/active" class="button tiny secondary" >Activity</a>
	
  <?php $vs = array_reverse($database->query("SELECT version FROM versions ORDER BY time DESC, percent DESC LIMIT 5")); 
  $first = true;
  foreach($vs as $vb){
	echo '<a href="/version/'.$vb['version'].'" class="button tiny '.(!$first ? 'secondary':'').'">'.$vb['version'].'</a>';
	$first = false;
  }
  ?>
  <?php $vs = array_reverse($database->query("SELECT category FROM servers WHERE category != '' GROUP BY category ORDER BY COUNT(category) DESC LIMIT 5")); 
  $first = true;
  foreach($vs as $vb){
	echo '<a href="/category/'.$vb['category'].'" class="button tiny '.(!$first ? 'secondary':'').'" >'.$vb['category'].'</a>';
	$first = false;
  }
  ?>
          </div>
        
          
          <div class="adsection hide-for-small">
            <div class="row collapse ad">
            	<script type="text/javascript"><!--
		google_ad_client = "ca-pub-8782622759360356";
		/* CraftStats Sidebar */
		google_ad_slot = "3748565036";
		google_ad_width = 200;
		google_ad_height = 200;
		//-->
		</script>
		<script type="text/javascript"
		src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>
	     </div>
            <div class="row collapse ad">
            <a href="mailto:billing@craftstats.com?Subject=Sidebar%20advertisement"><img src="http://placehold.it/200x125"/ alt="Ad Placeholder"></a>
            </div>
			
		 </div>
        </div>
      </div>
    </div>
	<div class="nine columns content">
      <div class="row">
       <div class="twelve columns">
          <nav class="top-bar">
            <ul>
              <li class="toggle-topbar"><a href="#"></a></li>
            </ul>

            <section>
              <!-- Left Nav Section -->
              <ul class="left">
                <li>
                  <a href="/promote">get more players</a>
                </li>
                <li>
                  <a href="/refer.php?url=bit.ly/18r9kzV">start a server</a>
                </li>
                <!--<li>
                  <a href="/refer.php?url=enjin.com/minecraft-websites">create a site</a>
                </li>-->
                <li>
                  <a href="/players">stats</a>
                </li>
                <li>
                  <a href="/hosting#">hosting</a>
                </li>
              </ul>

              <!-- Right Nav Section -->
              <ul class="right">
                <li>
                  <a href="<?php echo ($_SESSION['username'] != '' ? '/account.php' : '/login?post='.$_SERVER['REQUEST_URI'])?>"><?php echo ($_SESSION['username'] != '' ? 'logged in as '.$_SESSION['username'] : 'login')?></a>
                </li>
				
				<?php echo ($_SESSION['username'] != '' ? '<li><a href="/oauth.php?logout=true">logout</a></li>' : '')?>
              </ul>
            </section>
          </nav>
       </div>
      </div>
