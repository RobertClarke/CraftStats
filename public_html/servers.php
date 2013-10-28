<?php
include '../inc/global.inc.php';
$time = time();
if($_POST['advc'] == true){
  $database->query("UPDATE servers SET advCheck = 1 WHERE (resolved = '$_GET[ip]' AND resolved != '') OR ip = '$_GET[ip]'");
}
$data = array();
$server = $database->query("SELECT * FROM servers WHERE (resolved = '$_GET[ip]' AND resolved != '') OR ip = '$_GET[ip]' LIMIT 0,1",db::GET_ROW);
$sname = $server['name'];
$scat = $server['category'];

if($isowner && $_GET['tab'] == 'blacklist'){
        $database->query("UPDATE servers SET blacklisted = '1' WHERE ID = $server[ID]");
        $removedError = true;
        exit;
}

$notfoundError = false;
$removedError = false;

if($server[blacklisted] == 1){
        $removedError = true;
}
if($server[removed] == 1){
        $removedError = true;
}
if($server[game] == mcpe){
        $notfoundError = true;
}
if($server['ID'] == ''){
        $removedError = true;
}

if($notfoundError == true){
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        $template->setTitle('404');
        $template->show('header');
        echo '<section class="content main_content">'
        .'<div class="sub_content">'
        .'<div class="wrap">'
        .'<h1>404 Not found</h1>'
        .'<p>The page you\'re looking for cannot be found.</p>'
        .'</div>'
        .'</div>'
        .'</section>'
        .'</div>';
        $template->show('footer');
        exit;
}
if($removedError == true){
        header($_SERVER["SERVER_PROTOCOL"]." 410 Removed");
        $template->setTitle('410');
        $template->show('header');
        echo '<section class="content main_content">'
        .'<div class="sub_content">'
        .'<div class="wrap">'
        .'<h1>410 Removed</h1>'
        .'<p>The page you\'re looking has been removed.</p>'
        .'</div>'
        .'</div>'
        .'</section>'
        .'</div>';
        $template->show('footer');
        exit;
}

$database->query("SELECT * FROM users WHERE id = '$_SESSION[id]' AND admin = 1");
if($database->num_rows == 1){
$isowner = true;
}else{
$owner = $database->query("SELECT * FROM serverowners WHERE userID = '$_SESSION[id]' AND serverID = '$server[ID]'",db::GET_ROW);
if($database->num_rows >= 1){
  $isowner = true;
}}

$bannerurl = $server['bannerurl'];

if($isowner && $_POST['scat']){
  $database->query("UPDATE servers SET category = '$_POST[scat]',graphshow = '$_POST[gshow]', name = '$_POST[sname]', description = '$_POST[sdesc]', bannerurl='$_POST[bannerurl]' WHERE ID = $server[ID]");
  $scat = $_POST['scat'];
  $sname = $_POST['sname'];
  $sdesc = $_POST['sdesc'];
  $bannerurl = $_POST['bannerurl'];
}

if($isowner && $_POST['votip'] != ''){
$vottry = 1;
  $votfail = file_get_contents('http://192.119.145.28/api.php?a=2&ip='.$_POST[votip].'&user=CraftStats&port='.$_POST[votport].'&key='.base64_encode($_POST[votkey]));
  if($votfail == 'true')$database->query("UPDATE servers SET votifierIP = '$_POST[votip]', votifierPort = '$_POST[votport]', votifierKey = '$_POST[votkey]' WHERE (resolved = '$_GET[ip]' AND resolved != '') OR ip = '$_GET[ip]'");
}
$server = $database->query("SELECT * FROM servers WHERE (resolved = '$_GET[ip]' AND resolved != '') OR ip = '$_GET[ip]' LIMIT 0,1",db::GET_ROW);

$dpoints = $database->query("SELECT * FROM (SELECT * FROM updates WHERE serverID = '$server[ID]' ORDER BY time DESC) AS u ORDER BY u.time ASC");
$uptimeavg = array();

if(count($dpoints) > 1){
foreach($dpoints as $n => $update){
  if($time - $update['time'] < 604800){
    array_push($data,array(($update[time]*1000), ($update[ping] > 0 ? $update[connPlayers] : 'null') ,($update[ping] > 0 ? $update[maxPlayers] : 'null')));
  }
}

foreach($data as $row){
  $r0 .= "{$frst}[$row[0], $row[1]]";
  $r1 .= "{$frst}[$row[0], $row[2]]";
  $frst = ',';
}

$series1 = '{data:['.$r1.'],color:"#cdcdcd"},{data:['.$r0.'],color:"#3A87AD",hoverable:true}';
}

$template->setHeadScripts('
<script language="javascript" type="text/javascript" src="/assets/js/jquery-1.8.3.min.js"></script>
<script language="javascript" type="text/javascript" src="/assets/js/flot.js"></script>
<script language="javascript" type="text/javascript" src="/assets/js/cstats.js"></script>
<script language="javascript" type="text/javascript" src="/assets/js/flot.time.js"></script>
<script language="javascript" type="text/javascript" src="/assets/js/cstats.js"></script>
<script type="text/javascript">
'.(count($dpoints) > 1 ? '
$(document).ready(function() {
$.plot($("#chart_div"), ['.$series1.'],{
grid:{
    labelMargin:20,
    borderWidth:1
  },
  series:{
    lines: { show: true, fill: true, steps: false }
  },xaxis:{
    mode:"time",
      timeformat: "%a"
  },
  legend:{
    position:"nw"
  }

});
});
':'').'
</script>
  <script type="text/javascript" src="/js/ZeroClipboard.js"></script>
  
  <script type="text/javascript">
  $(document).ready(function() {
    $(window).load(function(){
    
      !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
      (function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=151420601618450";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));
    });
  });
</script>');
if($_GET['tab'] == 'vote'){
  $template->setTitle('Vote for '.($server['name'] ? $server['name'] : $server['ip']));
}else{
  $template->setTitle($server['name'] ? $server['name'] : $server['ip']);
}
//if($_GET['tab'] == 'vote'){
//  $template->setMainH1('Vote for '.($server['name'] ? $server['name'] : $server['ip']));
//}else{
//  $template->setMainH1($server['name'] ? $server['name'] : $server['ip']);
//}
$template->setMainH1($server['ip']);
$template->setDesc($server['ip'].' | '.($sname != '' ? $sname.' | ':'').''.$server['connPlayers'].' players online | '.($scat != '' ? 'Minecraft '.($server['version'] != '' ? $server['version'].' ' : '').''.$scat.' server | ':'').'This is one of the minecraft servers tracked on CraftStats.');
$template->setKeys(($scat != '' ? 'minecraft '.$scat.' server, ':'').($scat != '' ? 'mc '.$scat.' server, ':'').' minecraft '.($server['version'] != '' ? $server['version'].' ' : '').'servers, '.($scat != '' ? 'minecraft '.$server['version'].' '.$scat.' servers, ':'').' '.($scat != '' ? 'minecraft '.$server['version'].' '.$scat.' server ':''));
$template->show('header');
$database->query("SELECT * FROM uservotes WHERE serverID = '$server[ID]'");
$votes = $database->num_rows;
?>
    <!-- Votifier connect fail -->
    <?php if($vottry){
     if($votfail != 'true'){echo '<div class="alert-box negative">'
      .'Cannot connect to Votifier Server.'
    .'</div>';
     }else{echo '<div class="alert-box positive">'
      .'Successfully updated Votifier details!'
    .'</div>';}} ?>
    <!-- Voted -->
    <?php if($_GET['tab'] == 'voted'){echo '<div class="alert-box positive">'
      .'You\'ve voted for '.$server['ip'].'!'
    .'</div>';} ?>
    <!-- Updated server info -->
    <?php if($_POST['scat']){echo '<div class="alert-box positive">'
      .'Successfully updated server info. There may be a delay for changes to take effect.'
    .'</div>';} ?>
    <!-- Blacklist confirm -->
    <?php if($_POST['del']){echo '<div class="alert-box negative">'
      .'Are you sure you want to delete and blacklist this server? <a href="/server/'.$server['ip'].'/blacklist">Click here to continue.</a>'
    .'</div>';} ?>
    <section class="content main_content">
      <div class="featured_server sub_content">
        <div>
          <div class="<?php if($_GET['tab'] != 'vote'){echo 'server_info';}else{echo 'server_info_vote';} ?>">
            <h4><?php
            if($server['name'] != ''){
              if($_GET['tab'] == 'vote'){echo 'Vote for ';} echo $server['name'];
              }
            else{
              if($_GET['tab'] == 'vote'){echo 'Vote for ';} echo $server['ip'];
            } ?></h4>
            <?php if($server['name'] != ''){ ?>
            <a href="/server/<?php echo $server['ip']; ?>"><?php echo $server['ip']; ?></a>
            <?php } ?>
          </div>
          <div class="<?php if($_GET['tab'] != 'vote'){echo 'server_actions';}else{echo 'server_actions_vote';} ?>" <?php if($server['bannerurl'] != ''){echo 'style="magin bottom: 1em;"';} ?>>
            <?php if($_GET['tab'] != 'vote'){ ?>
            <a href="/server/<?php echo $server['ip']; ?>/vote" class="btn">Vote for this server</a>
            <?php if(time() > $server['sponsorTime'] && ($instock2 || $instock)){ ?>
            <a href="/promote?ip=<?php echo $server['ip']; ?>" class="btn">Sponsor this server</a>
            <?php } ?>
            <?php } else{ ?>
            <a href="/server/<?php echo $server['ip']; ?>" class="btn">Back to server</a>
            <?php } ?>
          </div>
        </div>
        <?php if($server['bannerurl'] != ''){echo '<div class="server_image">'
          .'<img src="'.$server['bannerurl'].'" width="468" height="60" alt="'.$server['name'].' banner" />'
        .'</div>';} ?>
      </div>
      <?php if($_GET['tab'] == 'vote'){ ?><div class="sub_content">
        <div class="wrap">
          <form class="full_width" action="/api" method="get">
            <input type="hidden" name="req" value="m12"/>
            <input type="hidden" name="id" value="<?php echo $server['ID']; ?>"/>
            <input type="text" name="usr" class="inline-left" placeholder="Minecraft username"></input>
            <input type="submit" value="Vote"></input>
          </form>
          <div class="center">
            <script type="text/javascript"><!--
            google_ad_client = "ca-pub-8782622759360356";
            /* CraftStats Vote 468 */
            google_ad_slot = "1192146234";
            google_ad_width = 468;
            google_ad_height = 60;
            //-->
            </script>
            <script type="text/javascript"
            src="//pagead2.googlesyndication.com/pagead/show_ads.js">
            </script>
          </div>
        </div>   
      </div>
      <?php } if($_GET['tab'] != 'vote'){ ?><div class="sub_content">
        <ul class="half_width">
          <li>Server version: <span><?php echo $server['version']; ?></span></li>
          <li>Server type: <span><?php if($server['category'] != ''){echo $server['category'];}else{echo 'None';} ?></span></li>
          <li>Players online: <span><?php echo $server['connPlayers'].'/'.$server['maxPlayers']; ?></span></li>
          <li>Last ping: <span><?php echo ($time - $server['lastUpdate'] > 60 ? round(($time - $server['lastUpdate'])/60).'m' : $time - $server['lastUpdate'].'s'); ?> minutes ago</span>
        </ul>
        <ul class="half_width">
          <li>Server rank: <span><?php echo '#'.$server['ranking']; ?></span></li>
          <li>Votes this month: <span><span><?php echo $server['votes']; ?></span></li>
          <li>Average uptime: <span><span><?php echo $server['uptimeavg'].'%'; ?></span></li>
        </ul>
      </div>
      <?php } if($_GET['tab'] != 'vote'){ if($server['description'] != ''){echo '<div class="sub_content">'
        .'<p>'.$server['description'].'</p>'
      .'</div>' ;} ?>
      <div class="sub_content">
        <div class="wrap">
          <div id="chart_div" style="margin:20px 0px;height:<?php echo(count($dpoints)<2?50:300);?>px;width:555px;text-align:center;">
            <?php if(count($dpoints < 2)){ ?>
              <div style="margin-top:50px;">Currently gathering data for this server.</div>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="sub_content">
        <div class="wrap">
          <div style="width:540px;height:90px"><img width='575' height='96' class="banner bannertarget" style="margin-left:0px;" data-bbase="/banner/<?php echo $server['ip'];?>" src="/banner/<?php echo $server['ip'];?>"/></div>
        </div>
        <ul class="btn-group bannerchange">
          <li style="cursor:pointer"><a>Hills</a></li>
          <li style="cursor:pointer"><a>Rain</a></li>
          <li style="cursor:pointer"><a>Beach</a></li>
          <li style="cursor:pointer"><a>Grass</a></li>
          <li style="cursor:pointer"><a>Shaft</a></li>
          <li style="cursor:pointer"><a>Night</a></li>
          <li style="cursor:pointer"><a>Sunrise</a></li>
          <li style="cursor:pointer"><a>Cottage</a></li>
          <li style="cursor:pointer"><a>Road</a></li>
        </ul>
        <div class="nowrap">
          <h3>Direct</h3>
          <p><a href="/banner/<?php echo $server['ip']; ?>">http://craftstats.com/banner/<?php echo $server['ip']; ?></a></p>
        </div>
        <div class="nowrap">
          <h3>HTML</h3>
          <p class="nowrap">&lt;a href="<?php echo 'http://craftstats.com/server/'.$server['ip'];?>" title="<?php echo $server['ip']; ?>"&gt;&lt;img src="<?php echo 'http://craftstats.com/banner/'.$server['ip'];?><span class="bannerpost"></span>" alt="<?php echo $server['ip']; ?>" /&gt;&lt;/a&gt;</p>
        </div>
        <div class="nowrap">
          <h3>BBCode:</h3>
          <p>[url=<?php echo 'http://craftstats.com/server/'.$server['ip'];?>][img]<?php echo 'http://craftstats.com/banner/'.$server['ip'];?><span class="bannerpost"></span>[/img][/url]</p>
        </div>
      </div>
      <?php if($isowner){ ?>
      <div class="sub_content">
        <div class="wrap"><h3>Server Settings</h3></div>
        <div class="half_width">
          <p>
            Votifier settings: <strong><?php echo ($server['votifierIP'] == '' ? 'Not set.' : $server['votifierIP'].':'.$server['votifierPort'])?></strong>
            <form action="/server/<?php echo $server['ip']; ?>" method="post">
              <input name="votip" type="text" placeholder="Votifier IP address"></input>
              <input name="votport" type="text" placeholder="Votifier port"></input>
              <input name="votkey" type="text" placeholder="Public key"></input>
              <input type="submit" value="Update votifier info"></input>
            </form>
          </p>
        </div>
        <div class="half_width">
        <form action="/server/<?php echo $server['ip']; ?>" method="post">
          <p>
            <form>
              <label>Server Category
                <select name="scat">
          <?php
          $options = array(
          'Creative',
          'CTF',
          'Drug',
          'Economy',
          'Factions',
          'Feed The Beast',
          'Hardcore',
          'Hub',
          'Hunger Games',
          'Mindcrack',
          'Parkour',
          'Prison',
          'PVE',
          'PVP',
          'Roleplaying',
          'Skyblock',
          'Spoutcraft',
          'Survival',
          'Tekkit',
          'Vanilla',
          );
          foreach($options as $o){
            echo '<option '.($scat == $o ? 'selected="selected"' : '').'>'.$o.'</option>';
          }
          ?>
                </select>
              </label>
              <label>Show graph
                <select name="gshow">
                  <option <?php echo ($server['graphshow'] == 1 ? 'selected' : '');?> value="1">Yes</option>
                  <option <?php echo ($server['graphshow'] == 0 ? 'selected' : '');?> value="0">No</option>
                </select>
              </label>
              <label>Banner URL
                <input name="bannerurl" type="text" value="<?php echo $bannerurl; ?>"></input>
              </label>
              <label>Server name
                <input name="sname" type="text" value="<?php echo $sname; ?>"></input>
              </label>
              <label>Server description <strong>(new)</strong>
                <input name="sdesc" type="text" value="<?php echo $server['description']; ?>"></input>
              </label>
              <input type="submit" value="Update server info"></input>
            </form>
            <form action="/server/<?php echo $server['ip']; ?>" method="post">
                <input type="hidden" name="del" value="1">
                <input type="button" value="Blacklist server"></input>
            </form>
          </p>
        </div>
      </form>
      </div><?php }} ?>
    </section>
  </div>


<?php

	//Footer derp yes it's obviously god damnit
	include '../templates/footer.php'

?>
