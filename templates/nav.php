<body>
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
        <a href="/">
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
            <div class="adsection hide-for-small">
              <div class="row collapse ad">
               <a href="/refer.php?url=cubedhost.org/craftstats"><img alt="CubedHost.org" src="/images/ads/cubedhost.png"/></a> 
              </div> 
            </div>
          <h6 class="subheader">Find a server</h6>
		  <?php if($_GET['sf']){ ?>
			<div class="alert-box alert">
				  Server not found
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
		  
          <h6 class="subheader" style="margin-top:0px;">Find a player</h6>
		   <?php if($_GET['pf']){ ?>
			<div class="alert-box alert">
				  Player not found
				  <a href="" class="close">&times;</a>
				</div>
		  <?php } ?>
          <div class="row collapse">
			<form action="/api" method="get">
				<input type="hidden" name="v1" value="player"/>
				  <input type="hidden" name="req" value="m10"/>
				<div class="eight mobile-three columns">
				  <input type="text" name="v2" placeholder="Username"/>
				</div>
				<div class="four mobile-one columns">
				  <button class="button expand postfix" style="padding:0px;">Search</button>
				</div>
			</form>
          </div>
		  <a class="button expand" href="/submit">Add a server</a>
          <h6 class="subheader">Minecraft Server Categories</h6>
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
            <a href="mailto:billing@craftstats.com?Subject=Sidebar%20advertisement"><img src="http://placehold.it/200x125"/></a>
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
                  <a href="/refer.php?url=imc.io#cstatsheader">start a server</a>
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
                  <a href="<?php echo ($_SESSION['username'] != '' ? '/account.php' : '/login')?>"><?php echo ($_SESSION['username'] != '' ? 'logged in as '.$_SESSION['username'] : 'login')?></a>
                </li>
				
				<?php echo ($_SESSION['username'] != '' ? '<li><a href="/oauth.php?logout=true">logout</a></li>' : '')?>
              </ul>
            </section>
          </nav>
       </div>
      </div>
