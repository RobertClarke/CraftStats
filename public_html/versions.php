<?php
include '../inc/global.inc.php';
$template->setTitle('Minecraft Server Versions');
$template->show('header');

?>
<body>
<div class="navigation">
    
  </div>
  <div class="row" id="main" style="padding-bottom:0px;">
    
    <div class="three columns" style="height:170px;z-index:10;">
      <div class="row  box logo"> 

        <a href="/">
		<img src="/images/logo.png"/>
		</a>
		<hr/>
		<a href="http://mcpestats.com">
		<img src="http://mcpestats.com/images/logo.png"/>
		</a>
		<a class="togglelogos"></a>
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
                  <a href="http://servercrate.com/minecraft">start a server</a>
                </li>
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
	  </div>
	  </div>
		<div class="twelve columns box" style="margin-top:-70px;">

<div class="row">
<h2 style="text-align:center;">Minecraft Server Statistics</h2>
</div>


	<h5 style="text-align:center;">Version Adoption</h5>
	<div id="chart_div2"  style="width:100%;height:600px;padding:40px;float:left;overflow:auto;"><img src="http://i.imgur.com/Qy6T0hg.png" style=""/></div>

</div>
<?
	$template->show('footer');
?>