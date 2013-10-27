<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
  <link rel='stylesheet' href='/assets/css/normalize.css'>
  <link rel='stylesheet' href='/assets/css/fontello.css'>
  <link rel='stylesheet' href='/assets/css/style.css'>

  <script src="/assets/js/jquery-1.8.3.min.js"></script>
  <?php echo $this->headscripts; ?>

  <title><?php echo $this->title; ?></title>
  <meta name="description" content="<?php echo $this->desc; ?>">
  <meta name="keywords" content="<?php echo $this->keys; ?>">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
  <!--Gosquared-->
  <script type="text/javascript">
  var GoSquared = {};
  GoSquared.acct = "GSN-830142-V";
  (function(w){
    function gs(){
      w._gstc_lt = +new Date;
      var d = document, g = d.createElement("script");
      g.type = "text/javascript";
      g.src = "//d1l6p2sc9645hc.cloudfront.net/tracker.js";
      var s = d.getElementsByTagName("script")[0];
      s.parentNode.insertBefore(g, s);
    }
    w.addEventListener ?
      w.addEventListener("load", gs, false) :
      w.attachEvent("onload", gs);
  })(window);
  </script>

  <!--Google Analytics-->
  <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-8521263-30', 'craftstats.com');
  ga('send', 'pageview');

  </script>
  <!-- Site online -->
</head>

<body>
  <!-- Facebook like button code -->
  <div id="fb-root"></div>
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=1397828010439963";
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  </script>
  <div class="header_wrap">
  <header>
    <a href="/" class="current site_logo">CraftStats</a>
    <nav>
  <ul>
    <li><a href="/promote">Promote</a></li>
    <li><a href="/refer.php?url=servercrate.com/minecrafthosting">Start a server</a></li>
    <li><a href="/links">Links</a></li>
    <li><a href="/hosting">Hosting</a></li>
  </ul>
</nav>
    <form id="search_header" class="search_area" action="/api" method="get">
      <input type="hidden" name="v1" value="server"/>
      <input type="hidden" name="req" value="m10"/>
      <input type="text" name="v2" placeholder="Search..." />
      <input type="submit" value="Search">
    </form>
    <div class="account_dropdown">
      <a href="<?php echo ($_SESSION['username'] != '' ? '/' : '/login?post='.$_SERVER['REQUEST_URI'])?>"><?php echo ($_SESSION['username'] != '' ? $_SESSION['username'] : 'Login')?> </a><i class="icon icon-down-dir"></i>
      <?php if($_SESSION['username'] != ''){echo '<ul class="dropdown_content">'
        .'<li><a href="/account">My Servers</a></li>';
        $database->query("SELECT * FROM users WHERE id = '$_SESSION[id]' AND admin = 1");
        if($database->num_rows == 1){echo '<li><a href="/admin">Admin</a></li>';}
        echo '<li><a href="/oauth.php?logout=true">Logout</a></li>';
      echo '</ul>';} ?>
    </div>
  </header>
  </div>
  <div class="stats_header">
    <section>
      <h1>
        <?php echo $this->mainH1; ?>
      </h1>
      <ul>
<?php $sponsoredp = $database->query("SELECT ID as sid, country, category, name, ip, advCheck, connPlayers AS cp, maxPlayers AS mp, version, motd, lastUpdate, uptimeavg, ranking FROM servers WHERE sponsorTime > UNIX_TIMESTAMP() AND blacklisted != 1 AND sponsorType = 1 AND game = 'minecraft' ORDER BY sponsorRank DESC, ranking ASC LIMIT 3");
      foreach($sponsoredp as $sp){
      ?>
        <li class="server">
          <span class="server_version"><?php echo $sp['version']; ?></span>
          <a href="/server/<?php echo $sp['ip']; ?>"><?php echo $sp['ip']; ?></a>
          <p><?php echo $sp['cp'].'/'.$sp['mp'].' Players'; ?></p>
          <p><?php echo $sp['uptimeavg'].'% uptime'; ?></p>
        </li>
<?php 
}?>
      </ul>
    </section>
  </div>
  <div class="content_area">
    <section class="content">
      <h2>Minecraft Server List</h2>
      <p>
        <script type="text/javascript"><!--
        google_ad_client = "ca-pub-8782622759360356";
        /* CraftStats 300x Sidebar */
        google_ad_slot = "4772041432";
        google_ad_width = 300;
        google_ad_height = 250;
        //-->
        </script>
        <script type="text/javascript"
        src="//pagead2.googlesyndication.com/pagead/show_ads.js">
        </script>
      </p>
      <center><p style="padding-top:5px;">
        <a href="/submit" class="btn">Add a server</a>
        <span class="social_buttons">
          <a href="http://twitter.com/craftstats" class="btn">Follow us</a>
          <a href="http://facebook.com/craftstats" class="btn">Like us</a>
        </span>
      </p></cemter>
      <p>
        <a href="/refer.php?url=servercrate.com/minecrafthosting"><img src="/assets/img/ads/sc_startaserver.jpg" alt="Minecraft hosting" style="padding-top:10px"></a>
      </p>
    <br>
    </section>
