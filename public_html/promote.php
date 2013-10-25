<?php
include '../inc/global.inc.php';
$svvalid = false;

if($_POST['buyi'] > 0){
        if($_POST['promoip']){
        
                $sv = $database->query("SELECT * FROM servers WHERE ((resolved = '$_POST[promoip]' AND resolved != '') OR ip = '$_POST[promoip]') AND game = 'minecraft'",db::GET_ROW);
                if($database->num_rows == 1){
                        $svvalid = true;
                        $svid = $sv['ID'];
                }
        
        }elseif($_POST['promohost']){
                $sv = $database->query("SELECT * FROM hosts WHERE ID = '$_POST[promohost]'",db::GET_ROW);
                if($database->num_rows == 1){
                        $svvalid = true;
                        $svid = $sv['ID'];
                }
        }
}
if($_POST['buyi'] > 0 && $svvalid){
        require_once( '../lib/httprequest.php' );
        require_once( '../lib/paypal.php' );
        $r = new PayPal(true);
        $prices = array(1=>20,2=>35,3=>100,4=>180,5=>20,6=>35);
        $week = array(1=>1,2=>2,3=>1,4=>2,5=>1,6=>2);
        $type = ($_POST['buyi'] > 4 ? 2 :($_POST['buyi']>2 ? 1:0));
        
        if($_POST['promohost']){
                $msg = $week[$_POST['buyi']].' Week promotion for host '.$sv['name'];
        }else{
                $msg = $week[$_POST['buyi']].' Week '.($type == 1 ? 'Premium' : 'Standard').' Promotion for server '.$_POST['promoip']. ' on craftstats.com';
        }
        $ret = $r->doExpressCheckout($prices[$_POST['buyi']], $msg);

        if ($ret['ACK'] == 'Success') {
                $token = $ret['TOKEN'];
                $cost = $prices[$_POST['buyi']].'.00';
                $mf=$week[$_POST['buyi']];
                $database->query("INSERT INTO promo_order VALUES ('$token','$svid','$mf','$type','$cost','0',0,'','','')");
                exit;
        }
}

if($_GET['paypal'] == 'paid' || $_GET['ttoken']){
        require_once( '../lib/httprequest.php' );
        require_once( '../lib/paypal.php' );
        include_once '../lib/twitteroauth.php';
        $r = new PayPal(true);
        
        if(!$_GET['ttoken'])$final = $r->doPayment();
        
        if ($final['ACK'] == 'Success' || $_GET['ttoken']) {
                $token = $final['TOKEN'];
                if($_GET['ttoken'])$token = $_GET['ttoken'];
                $order = $database->query("SELECT * FROM promo_order WHERE token = '$token'",db::GET_ROW);
                if(($order['paid'] == 0 && $database->num_rows == 1) || $_GET['ttoken']){
                        if($order['type'] == 2){
                                $sv = $database->query("SELECT * FROM hosts        WHERE ID = '$order[serverID]'",db::GET_ROW);
                                $responses = array(
                                'Check out '.$sv['name'].' for awesome minecraft hosting http://'.$sv['url'],
                                'Want to start your own minecraft server? Get your own from '.$sv['name'].'. http://'.$sv['url']);
                        }else{
                                $sv = $database->query("SELECT * FROM servers WHERE game = 'minecraft' AND ID = '$order[serverID]'",db::GET_ROW);
                                $responses = array(
                                'Check out this awesome Minecraft server! http://craftstats.com/server/'.$sv['ip'],
                                'Congrats to these guys for becoming sponsored on our site! http://craftstats.com/server/'.$sv['ip'],
                                'Looking for a new Minecraft server to play on? Look no further! http://craftstats.com/server/'.$sv['ip'],
                                'Awesome new promoted server be sure to check these guys out! http://craftstats.com/server/'.$sv['ip'],
                                'We\'ve got another fantastic Minecraft server for you to try out http://craftstats.com/server/'.$sv['ip'],
                                'This minecraft server looks pretty awesome: http://craftstats.com/server/'.$sv['ip'],
                                'This is a great minecraft server http://craftstats.com/server/'.$sv['ip']);
                        }
                        $tmhOAuth = new tmhOAuth(array(
                                        'consumer_key'    => 'LikmqUGSLAAWgZ8zCVC2A',
                                        'consumer_secret' => '6PlWlXC6ugcwpY0SrlZ48uvc9KNHCPpVhpGjH6O6U',
                                        'user_token'      => '822604988-MrKWIjH8xH3eb5TvI6d0XIowqnkV3FE1YLE6u2zq',
                                        'user_secret'     => 'J9TiF64znmZaR3I4zxFAyB0HeNJbvlU8mQCuXbNnd78',
                                ));

                                $code = $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update'), array(
                                        'status' => $responses[array_rand($responses)],
                                ));
                        $stime = ($order['length']*60*60*24*7) + max($sv['sponsorTime'],time());
                        
                        if(!$_GET['ttoken'])$database->query("UPDATE promo_order SET paid = '1', expire = '$stime', first='{$r->details[FIRSTNAME]}',last='{$r->details[LASTNAME]}',email='{$r->details[EMAIL]}' WHERE token = '$token'");
                        if($order['type'] == 2){
                                $database->query("UPDATE hosts SET sponsorTime = '$stime' WHERE ID = '$order[serverID]'");
                        }else{
                                $database->query("UPDATE servers SET sponsorTime = '$stime', sponsorType = '$order[type]' WHERE ID = '$order[serverID]'");
                        }
                }
        }
}

$template->setHeadScripts('<script> 
                if (window != top) { 
                  top.location.replace(document.location); 
                } 
           </script> ');
$template->setKeys('minecraft advertising, more minecraft players ');
$template->setTitle('Promote a Minecraft Server');
$template->setMainH1('Promote');
$template->setDesc('Looking to get more players on your Minecraft server? Minecraft servers has a variety of different ways to promote your minecraft server.');
$template->show('header');

$database->query("SELECT * FROM servers WHERE sponsorTime > UNIX_TIMESTAMP() && sponsorType = 0"); 
//Standard promotion stock
if($database->num_rows < 12){
        $instock = true;
}else{
        $instock = false;
}

//Premium promotion stock
$a = $database->query("SELECT * FROM servers WHERE sponsorTime > UNIX_TIMESTAMP() AND blacklisted != 1 AND sponsorType = 1 AND game = 'minecraft'"); 
if($database->num_rows >= 1){
        $instock2 = false;
}else{
        $instock2 = true;
}

//host promotion
$a = $database->query("SELECT * FROM hosts WHERE sponsorTime > UNIX_TIMESTAMP()"); 
if($database->num_rows >= 2){
        $instock3 = false;
}else{
        $instock3 = true;
}
?>
    <?php
    if(!$svvalid && $_POST['buyi'] > 0){echo '<div class="alert-box negative">'
      .($_POST['promohost'] ? 'Invalid host.' : 'We\'re not currently tracking that server! Make sure you entered the IP address correctly and try again.')
    .'</div>';} ?>
    <?php
    if ($final['ACK'] == 'Success') {echo '<div class="alert-box positive">'
      .'Thank you for your payment, your '.($order['type'] > 4 ? 'host':'server').' is now being promoted!'
    .'</div>';} ?>
    <section class="content main_content">
      <div class="sub_content">
        <h3><?php echo ($_GET['ip'] ? 'Sponsor '.$_GET['ip'] : 'Promote a Minecraft Server'); ?></h3>
        <div class="promote_server">
        <p>Thousands of minecraft players visit our site to find servers, you can purchase a slice of that traffic for <?php echo ($_GET['ip'] ? $_GET['ip'] : 'your own server'); ?>!</p>
        <h3>What you'll get</h3>
        <p>- Featured listing on the front page</p>
        <p>- Minecraft server banners around the website</p>
        <p>- Tweet from our twitter account</p>
        </div>
      </div>
      <div class="sub_content">
        <div class="wrap" style="margin-bottom: 1em">
          <h3>Sponsored Server</h3>
        </div>
        <div class="wrap">
          <form class="quater_width" <?php echo ($instock?'action="/promote" method="post"':''); ?>>
          	<input type="hidden" name="buyi" value="1">
            <span>One Week:<span>$20</span> <br>
            <input type="text" name="promoip" placeholder="Server IP" value="<?php if($_POST['buym'] == 1)echo $_POST['promoip']; if($_GET['ip'])echo $_GET['ip']; ?>"></input>
            </span>
            <input type="submit" value="Buy Now"></input>
          </form>
          <div class="center"><img src='/assets/img/promote/sponsored.png' width='300px' height='250px' /></div>
          <form class="quater_width" <?php echo ($instock?'action="/promote" method="post"':''); ?>>
          	<input type="hidden" name="buyi" value="2">
            <span>Two Weeks:<span>$35</span> <br>
            <input type="text" name="promoip" placeholder="Server IP" value="<?php if($_POST['buym'] == 2)echo $_POST['promoip']; if($_GET['ip'])echo $_GET['ip']; ?>"></input>
            </span>
            <input type="submit" value="Buy Now"></input>
          </form>
        </div>
      </div>
      <div class="sub_content">
        <div class="wrap" style="margin-bottom: 1em">
          <h3>Premium Promotion</h3>
        </div>
        <div class="wrap">
          <form class="quater_width" <?php echo ($instock?'action="/promote" method="post"':''); ?>>
          	<input type="hidden" name="buyi" value="3">
            <span>One Week:<span>$100</span> <br>
            <input type="text" name="promoip" placeholder="Server IP" value="<?php if($_POST['buym'] == 3)echo $_POST['promoip']; if($_GET['ip'])echo $_GET['ip']; ?>"></input>
            </span>
            <input type="submit" value="Buy Now"></input>
          </form>
          <div class="center"><img src='/assets/img/promote/premium.png' width='300px' height='250px' /></div>
          <form class="quater_width" <?php echo ($instock?'action="/promote" method="post"':''); ?>>
          	<input type="hidden" name="buyi" value="4">
            <span>Two Weeks:<span>$180</span> <br>
            <input type="text" name="promoip" placeholder="Server IP" value="<?php if($_POST['buym'] == 4)echo $_POST['promoip']; if($_GET['ip'])echo $_GET['ip']; ?>"></input>
            </span>
            <input type="submit" value="Buy Now"></input>
          </form>
        </div>
      </div>
      <div class="sub_content">
        <div class="wrap" style="margin-bottom: 1em">
          <h3>Banner Promotion</h3>
        </div>
        <div class="wrap">
          <form class="quater_width">
            <span>Auction:<span>Bidding system</span> <br>
            </span>
          </form>
          <div class="center"><img src='/assets/img/promote/banner.png' width='300px' height='250px' /></div>
          <form class="quater_width">
            <a href="/promote/bid"><input type="submit" value="Bid Now"></input></a>
          </form>
        </div>
      </div>
    </section>
  </div>
<?php
$template->show('footer');
?>