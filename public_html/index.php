<?php
include '../inc/global.inc.php';
$index = true;
$stats = $database->query("SELECT * FROM sitegrowth ORDER BY time DESC LIMIT 10");

if(strtolower($_GET['cat']) == 'new'){
	$_GET['cat'] = 'New';
}
if(strtolower($_GET['cat']) == 'reliable'){
	$_GET['cat'] = 'Reliable';
}
if(strtolower($_GET['cat']) == 'active'){
	$_GET['cat'] = 'Active';
}
if(strtolower($_GET['cat']) == 'creative'){
	$_GET['cat'] = 'Creative';
}
if(strtolower($_GET['cat']) == 'survival'){
	$_GET['cat'] = 'Survival';
}
if(strtolower($_GET['cat']) == 'factions'){
	$_GET['cat'] = 'Factions';
}
if(strtolower($_GET['cat']) == 'creative'){
	$_GET['cat'] = 'Creative';
}
if(strtolower($_GET['cat']) == 'ctf'){
	$_GET['cat'] = 'CTF';
}
if(strtolower($_GET['cat']) == 'drug'){
	$_GET['cat'] = 'Drug';
}
if(strtolower($_GET['cat']) == 'economy'){
	$_GET['cat'] = 'Economy';
}
if(strtolower($_GET['cat']) == 'hardcore'){
	$_GET['cat'] = 'Hardcore';
}
if(strtolower($_GET['cat']) == 'mindcrack'){
	$_GET['cat'] = 'Mindcrack';
}
if(strtolower($_GET['cat']) == 'parkour'){
	$_GET['cat'] = 'Parkour';
}
if(strtolower($_GET['cat']) == 'tekkit'){
	$_GET['cat'] = 'Tekkit';
}
if(strtolower($_GET['cat']) == 'vanilla'){
	$_GET['cat'] = 'Vanilla';
}
if(strtolower($_GET['cat']) == 'hub'){
	$_GET['cat'] = 'Hub';
}
$template->setTitle(($_GET['version'] ? $_GET['version'].' Minecraft Servers' : ($_GET['cat'] ? $_GET['cat'].' Minecraft Servers':'Best Minecraft Servers List')));

$template->setKeys(($_GET['version'] ? $_GET['version'].' Minecraft Servers' : ($_GET['cat'] ? $_GET['cat'].' Minecraft Servers':'Best Minecaft Servers List')));

if($_GET['version']){
	$template->setdesc('A list of the best Minecraft '.$_GET['version'].' servers for you to play on with your friends. These include '.$_GET['version'].' PVP Minecraft servers.');
}
if($_GET['cat']){
	$template->setdesc('A list of the best Minecraft '.$_GET['cat'].' servers for you to play on with your friends. These include 1.6.2 '.$_GET['cat'].' Minecraft servers.');
}

$template->show('header');
$template->show('nav');

$tservers = $database->query("SELECT COUNT(*) AS c FROM servers WHERE blacklisted != 1 $version",db::GET_ROW);
$tservers = floor($tservers['c']/30)-1;
$cpage = ($_GET['p'] != 0 ? $_GET['p'] : 0);
$cpage = max(0,min($cpage,$tservers));
$pagemin = $cpage*30;
$pagemax = 30;
$time = time();
?>

<div class="servers">
	<div class="twelve columns prepromote">
		<div class="row">
			<?php
 echo ($_GET['version'] ? '<h1>'.$_GET['version'].' Minecraft Servers</h2>' : ($_GET['cat'] ? '<h1>'.$_GET['cat'].' Minecraft Servers</h1>':'<h1>Minecraft Servers</h1>')); ?>
		</div>
        <div class="row">
			<?php $sponsoredp = $database->query("SELECT 
  ID as sid, country, category,
  name, ip, advCheck,
  connPlayers AS cp, maxPlayers AS mp, version, motd, lastUpdate, uptimeavg, ranking
FROM servers WHERE sponsorTime > UNIX_TIMESTAMP() AND blacklisted != 1 AND sponsorType = 1 AND game = 'minecraft' ORDER BY sponsorRank DESC, ranking ASC LIMIT 3");
			foreach($sponsoredp as $sp){
?>
			<div class="four columns">
				<div class="twelve columns box prempromo">
					<h5 class="subheader"><strong><a href="/server/<?php echo $sp['ip']; ?>"><?php echo $sp['ip']; ?></a></strong></h5>
					<span class="subtitle"><?php echo $sp['cp'].'/'.$sp['mp'].' Players - '.$sp['uptimeavg'].'% Uptime'; ?></span><br/>
					<?php if($sp['version'] != ''){ ?><a href="/version/<?php echo $sp['version']; ?>" class="button tiny"><?php echo $sp['version']; ?></a><?php } ?>
					<?php if($sp['category'] != ''){ ?><a href="/category/<?php echo $sp['category']; ?>" class="button tiny"><?php echo $sp['category']; ?></a><?php } ?>
				</div>
			</div>
<?php }

	if(count($sponsoredp) == 0){
	?>
	<div class="four columns">
				<div class="twelve columns box" style="text-align:center;color:#999 !important;margin-top:70px;padding-bottom:4px;margin-bottom:-35px;">
					<h5 class="subheader"><a href="/promote" style="color:#999;">Brand new high-traffic banner auction ending in less than 2 days, bid now!</a></h5>
				</div>
			</div>
	<?php	
	}?> 
		</div>
	</div>
	<?php 
	if($cpage == 0 && $new == ''){
	if(is_object($memcache))$ann =$memcache->get(md5('announce'));
	if($ann != ''){
		?>
		<div class="row ">
		<div class="twelve columns">
		<div class="alert-box radius" style="padding:10px;text-align:left;background:#62a7c4;font-weight:normal;">
					<?php echo $ann; ?>
				</div></div>
		</div>
		<?php
	}
	?>

	<?php $sponsoredp = $database->query("SELECT 
  ID as sid, country, category,
  name, ip, advCheck,
  connPlayers AS cp, maxPlayers AS mp, version, motd, lastUpdate, uptimeavg, ranking, bannerurl
FROM servers WHERE bannerpromo > UNIX_TIMESTAMP() AND blacklisted != 1 AND game = 'minecraft' ORDER BY ranking ASC LIMIT 3");
			foreach($sponsoredp as $sp){
?>
	<div class="row">
			<div class="twelve columns centered">
				<div class="twelve columns box">
					
					<div style="float:left;margin:10px 0px;">
					<h5 style="margin:0px;">
					<?php echo ($sp['name'] ? $sp['name'] : $sp['ip'])?>
					</h5>
					
					<span class="subtitle"><?php echo $sp['cp'].'/'.$sp['mp'].' Players - '.$sp['uptimeavg'].'% Uptime'; ?></span><br/>
					<a href="/server/<?php echo $sp['ip']; ?>" class="button tiny" style="margin-top:5px;">Join Now!</a>
			</div>
			<a href="/server/<?php echo $sp['ip']; ?>">
					<img src="<?php echo $sp['bannerurl']; ?>" style="margin:10px auto;display:block;float:right;border-radius:4px;"/>
					</a>
				
				</div>
			</div>
			
			</div>
<?php }

if(count($sponsoredp) < 3){
?>

<div class="row">
			<div class="twelve columns centered">
				<div class="twelve columns box">
					
					<a href="/promote/bid"><h5 style="text-align:center;margin:20px auto;font-size:14px;">Brand new high-traffic banner auction ending in less than 2 days, bid now!</h5></a>
				
				</div>
			</div>
			
			</div>

<?php
}
?>
	<div class="row table">
          <div class="twelve columns">
            <table class="twelve">
              <thead>
                <tr>
                  <th>Sponsored Servers</th>
                  <th>Uptime</th>
                  <th>Players</th>
                </tr>
              </thead>
              <tbody>
	<?php
	$sponsored = $database->query("SELECT 
  ID as sid, country,sponsorTime AS st, sponsorType as sp, category,
  name, ip, advCheck,
  connPlayers AS cp, maxPlayers AS mp, version, motd, lastUpdate, uptimeavg, ranking, uptime
FROM servers WHERE sponsorTime > UNIX_TIMESTAMP() AND sponsortype = 0 AND blacklisted != 1 AND game = 'minecraft' $version ORDER BY sponsorRank DESC, ranking ASC");

	?>
	<?php
			  foreach($sponsored as $server){
	echo '<tr onclick="document.location=\'/server/'.$server['ip'].'\';" class="slink '.($server['uptime'] <= 0 ? 'down':'').'">
	<td>'.($server['st'] > time() ? '<div style="float:left;padding-right:3px;"> &#9733; </div>':'').'<h2 style="font-size:14px;margin:0px;margin-top:3px;padding:0px;float:left;">   '.$server['ip'].' '.($server['version'] != '' ? '</h2><div style="float:right;margin-left:7px;"><a href="/version/'.$server['version'].'"><span class="button tiny">'.$server['version'].'</span></a></div>' : '').' 
	'.($server['category'] != '' ? '<div style="float:right;margin-left:7px;"><a href="/category/'.$server['category'].'"><span class="button tiny">'.$server['category'].'</span></a></div>' : '').'
	'.($server['advCheck'] == 2 ? '<div style="float:right;"><span class="button tiny">DirtBlock</span></div>' : '').'</td><td><span style="padding:3px 0px;display:block;width:50px !important;text-align:center;" class="button tiny '.($server['uptime'] <= 0 ? 'alert' : ($server['uptimeavg'] > 90 ? 'success' : ($server['uptimeavg'] > 70 ? 'secondary' : ($server['uptimeavg'] > 50 ? 'secondary' : 'alert')))).'">'.($server['uptime'] <= 0 ? 'down' : $server['uptimeavg'].'%').'</span></td><td>'.$server['cp'].' / '.$server['mp'].'</td></tr>';
}
			  ?>
	</tbody>
            </table>
			<div style="float:right;font-size:12px;margin-top:-8px;margin-bottom:5px;"><a href="/promote" style="color:#999;">want your server here?</a></div>
          </div>
        </div>
		<?php } ?>
        <div class="row table">
          <div class="twelve columns">
            <table class="twelve">
              <thead>
                <tr>
					<th>Rank</th>
                  <th>Connect to</th>
                  <th>Uptime</th>
                  <th>Players</th>
                </tr>
              </thead>
              <tbody>
			  
			   
                <?php
function FosMerge($arr1, $arr2) {
    $res=array();
    $arr1=array_reverse($arr1);
    $arr2=array_reverse($arr2);

    foreach ($arr1 as $a1) {
        if (count($arr1)==0) {
            break;
        }
        array_push($res, array_pop($arr1));
		$rate = count($arr1)/(count($arr2)==0?1:count($arr2));
        if (count($arr2)!=0 && (rand(0,floor($rate)) == 1 || $rate < 2)) {
            array_push($res, array_pop($arr2));
        }
    }
    return array_merge($res, $arr2);
}



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






$servers = $database->query("SELECT 
  ID as sid, country, 
  name, ip,advCheck,
  connPlayers AS cp,sponsorTime AS st, category, maxPlayers AS mp, version, motd, lastUpdate, uptimeavg, ranking, uptime
FROM servers WHERE sponsorTime < UNIX_TIMESTAMP() AND blacklisted != 1 AND game = 'minecraft' $version ORDER BY $new ranking ASC LIMIT $pagemin, $pagemax");

$time = time();
foreach($servers as $server){
	echo '<tr onclick="document.location=\'/server/'.$server['ip'].'\';" class="slink '.($server['uptime'] <= 0 ? 'down':'').'">
	<td>'.$server['ranking'].'</td>
	<td>'.($server['st'] > time() ? '<div style="float:left;padding-right:3px;"> &#9733; </div>':'').'<h2 style="font-size:14px;margin:0px;margin-top:3px;padding:0px;float:left;">   '.$server['ip'].' '.($server['version'] != '' ? '</h2><div style="float:right;margin-left:7px;"><a href="/version/'.$server['version'].'"><span class="button tiny">'.$server['version'].'</span></a></div>' : '').' 
	'.($server['category'] != '' ? '<div style="float:right;margin-left:7px;"><a href="/category/'.$server['category'].'"><span class="button tiny">'.$server['category'].'</span></a></div>' : '').'
	'.($server['advCheck'] == 2 ? '<div style="float:right;"><span class="button tiny">DirtBlock</span></div>' : '').'</td><td><span style="padding:3px 0px;display:block;width:50px !important;text-align:center;" class="button tiny '.($server['uptime'] <= 0 ? 'alert' : ($server['uptimeavg'] > 90 ? 'success' : ($server['uptimeavg'] > 70 ? 'secondary' : ($server['uptimeavg'] > 50 ? 'secondary' : 'alert')))).'">'.($server['uptime'] <= 0 ? 'down' : $server['uptimeavg'].'%').'</span></td><td>'.$server['cp'].' / '.$server['mp'].'</td></tr>';
}

?>
              </tbody>
            </table>
			
          </div>
        </div>
		<div class="row">
			<div class="six columns centered">
			<?PHP
			$dictionary  = array(
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
		 5                   => 'five'
    );
	for($i = $cpage-1;$i<$cpage+2;$i++){
					if($i >= 0 && $i <= $tservers-1){
						$listout .= '<li><a href="'.$sprefix.'/p/'.$i.'#slist" class="button radius '.($i == $cpage ? 'active':'').'">'.($i+1).'</a></li>';
						$lcount++;
					}
				}
			?>
				<ul class="button-group radius even <?php echo $dictionary[$lcount+2];?>-up">
						<?php
				
				echo '<li><a href="'.$sprefix.'/p/0#slist" class="button radius '.($cpage == 0 ? 'active':'').'">&laquo;</a></li>';
				
				echo $listout;
				
				echo '<li><a href="'.$sprefix.'/p/'.$tservers.'#slist"class="button radius '.($cpage == $tservers ? 'active':'').'">&raquo;</a></li>';
				?>
				</ul>
				
		</div>
      </div>
    </div>
<?php
$template->show('footer');
?>

