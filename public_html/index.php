<?php
include '../inc/global.inc.php';
$index = 'true';
$stats = $database->query("SELECT * FROM sitegrowth ORDER BY time DESC LIMIT 10");

$template->setTitle(($_GET['version'] ? $_GET['version'].' Minecraft Servers' : ($_GET['cat'] ? $_GET['cat'].' Minecraft Servers':'')));
$template->setKeys(($_GET['version'] ? $_GET['version'].' Minecraft Servers' : ($_GET['cat'] ? $_GET['cat'].' Minecraft Servers':'')));
$template->setMainH1('Minecraft Servers');

if($_GET['version']){
  $template->setdesc('A list of the best Minecraft '.$_GET['version'].' servers for you to play on with your friends. These include '.$_GET['version'].' PVP Minecraft servers.');
}
if($_GET['cat']){
  $template->setdesc('A list of the best Minecraft '.$_GET['cat'].' servers for you to play on with your friends. These include 1.6.2 '.$_GET['cat'].' Minecraft servers.');
}

$tservers = $database->query("SELECT COUNT(*) AS c FROM servers WHERE blacklisted != 1 $version",db::GET_ROW);
$tservers = floor($tservers['c']/30)-1;
$cpage = ($_GET['p'] != 0 ? $_GET['p'] : 0);
$cpage = max(0,min($cpage,$tservers));
$pagemin = $cpage*30;
$pagemax = 30;
$time = time();

if($_GET['version']){
  $version = 'AND version = \''.mysql_real_escape_string($_GET['version']).'\'';
  $sprefix = '/version/'.$_GET['version'];
}
if($_GET['cat']){
  if($_GET['cat'] != 'New' && $_GET['cat'] != 'Reliable' && $_GET['cat'] != 'Active')$version = 'AND category = \''.mysql_real_escape_string($_GET['cat']).'\'';
  $sprefix = '/category/'.$_GET['cat'];
}

if(strtolower($_GET['cat']) == 'new'){
  $new = 'ID DESC,';
}
if(strtolower($_GET['cat']) == 'reliable'){
  $new = 'uptimeavg DESC,';
}
if(strtolower($_GET['cat']) == 'active'){
  $new = 'connPlayers DESC,';
}

$sponsoredp = $database->query("SELECT ID as sid, country, category, name, ip, name as title, advCheck, connPlayers AS cp, maxPlayers AS mp, version, motd, lastUpdate, uptimeavg, sponsorRank, bannerurl FROM servers WHERE bannerpromo > UNIX_TIMESTAMP() AND blacklisted != 1 AND game = 'minecraft' ORDER BY sponsorRank ASC LIMIT 3");
$sponsored = $database->query("SELECT ID as sid, country,sponsorTime AS st, sponsorType as sp, category, name, ip, advCheck, connPlayers AS cp, maxPlayers AS mp, version, motd, lastUpdate, uptimeavg, ranking, uptime FROM servers WHERE sponsorTime > UNIX_TIMESTAMP() AND sponsortype = 0 AND blacklisted != 1 AND game = 'minecraft' $version ORDER BY sponsorRank DESC, ranking ASC");
$servers = $database->query("SELECT ID as sid, country, name, ip,advCheck, connPlayers AS cp,sponsorTime AS st, category, maxPlayers AS mp, version, motd, lastUpdate, uptimeavg, ranking, uptime FROM servers WHERE sponsorTime < UNIX_TIMESTAMP() AND blacklisted != 1 AND game = 'minecraft' $version ORDER BY $new ranking ASC LIMIT $pagemin, $pagemax");

$notfoundError = false;
$removedError = false;

$nextButton = true;

if($database->num_rows != 30){
        $nextButton = false;
}

if($database->num_rows == 0){
        $notfoundError = true;
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

$template->show('header');
?>
    <div class="alert-box positive">
      The new 1.7 update, along with new site changes are causing issues with the site. We're working on them!
    </div>
    <section class="content main_content">
      <ul class="server_list_large"><?php 
      foreach($sponsoredp as $sp){?>
        <li>
          <div class="server_info">
            <a href="/server/<?php echo $sp['ip']; ?>"><?php echo $sp['title']; ?></a>
            <p><?php echo $sp['cp'].'/'.$sp['mp'].' Players'; ?></p>
            <a href="/server/<?php echo $sp['ip']; ?>" class="btn">Join now!</a>
          </div>
          <div class="server_image">
            <a href="/server/<?php echo $sp['ip']; ?>">
              <img src='<?php echo $sp['bannerurl']; ?>' width='468' height='60' alt='<?php echo $sp['title']; ?> Minecraft Server' />
            </a>
          </div>
        </li>
<?php } ?>
      </ul>
      <table>
        <tr>
          <th>Sponsored Minecraft servers</th>
          <th>Uptime</th>
          <th>Players</th>
        </tr>
<?php foreach($sponsored as $server){?>
        <tr>
          <td><a href="/server/<?php echo $server['ip']; ?>"><?php echo $server['ip']; ?></a><div class="tags"><?php if($server['category'] != ''){echo '<a href="/category/'.$server['category'].'">'.$server['category'].'</a>';} ?><a href="/version/<?php echo $server['version']; ?>"><?php echo $server['version']; ?></a></div></td>
          <td class="uptime"><span class="<?php echo($server['uptime'] <= 0 ? 'negative' : ($server['uptimeavg'] > 90 ? 'positive' : ($server['uptimeavg'] > 70 ? 'mid' : ($server['uptimeavg'] > 50 ? 'secondary' : 'negative')))); ?>"><?php echo $server['uptimeavg']; ?>%</span></td>
          <td><?php echo $server['cp'].'/'.$server['mp']; ?></td>
        </tr>
<?php } ?>
      </table>

      <table>
        <tr>
          <th>Rank</th>
          <th style="width: 50%">Connect</th>
          <th>Uptime</th>
          <th>Players</th>
        </tr>
        <?php 
        foreach($servers as $server){ ?>
        <tr>
          <td class="rank"><?php echo $server['ranking']; ?></td>
          <td>
            <a href="/server/<?php echo $server['ip']; ?>"><?php echo $server['ip']; ?></a>
              <div class="tags">
                <?php if($server['category'] != ''){echo '<a href="/category/'.$server['category'].'">'.$server['category'].'</a>';} ?><a href="/version/<?php echo $server['version']; ?>"><?php echo $server['version']; ?></a>
              </div></td>
          <td class="uptime">
            <span class="<?php echo($server['uptime'] <= 0 ? 'negative' : ($server['uptimeavg'] > 90 ? 'positive' : ($server['uptimeavg'] > 70 ? 'mid' : ($server['uptimeavg'] > 50 ? 'mid' : 'negative')))); ?>"><?php if($server['uptime'] >= '0'){echo $server['uptimeavg'].'%';} else{echo 'Down';} ?></span>
          </td>
          <td><?php echo $server['cp'].'/'.$server['mp']; ?></td>
        </tr>
<?php } ?>
      </table>
      <div class="pagination">
        <?PHP
        $dictionary  = array(
          2                   => 'two',
          3                   => 'three',
          4                   => 'four',
          5                   => 'five');       
          for($i = $cpage-1;$i<$cpage+2;$i++){
            if($i >= 0 && $i <= $tservers-1){
              $listout .= '<li><a href="'.$sprefix.'/p/'.$i.'" style="'.($i == $cpage ? 'color:#2c3e50;':'').'">'.($i+1).'</a></li>';
              $lcount++;
            }
          }
        ?>
        <?php 
          if($cpage > 0){
            if($cpage == 1){
              echo '<a href="'.$sprefix.'" class="prev_page btn">Previous</a>';
            }
            else {
              echo '<a href="'.$sprefix.'/p/'.($cpage-1).'" class="prev_page btn">Previous</a>';
            }
          }
        ?>
        <ul>
          <?php
            if($cpage != 0){
              echo '<li><a href="'.$sprefix.'/">&laquo;</a></li>';
            }
            echo $listout;
            //echo '<li><a href="'.$sprefix.'/p/'.$tservers.'">&raquo;</a></li>';
          ?>
        </ul>
        <?php 
          if($nextButton == true){
            if($servers != 0){
              echo '<a href="'.$sprefix.'/p/'.($cpage+1).'" class="next_page btn">Next</a>';
            }
          }
        ?>
      </div>
    </div>
    </section>
  </div>
<?php

  //Footer derp yes it's obviously god damnit
  include '../templates/footer.php'

?>
