<?php
include '../inc/global.inc.php';
$memcache_disable = true;

if($_SESSION['username'] == ''){
        header("Location: /login?post=/promote/bid");
}

$auctionend = mktime(0,0,0,date("n"),24);
$auctionid=date('n').'-'.date('Y');

if(time() > $auctionend){
        $running = false;
}else{
        $running = true;
}

if($_POST['ip']){
        $sv = $database->query("SELECT * FROM servers WHERE ((resolved = '$_POST[ip]' AND resolved != '') OR ip = '$_POST[ip]') AND game = 'minecraft'",db::GET_ROW);
        if($database->num_rows == 1){
                $svvalid = true;
                $svid = $sv['ID'];
        }
}
$bids = $database->query("SELECT * FROM promo_bids WHERE auctionID = '$auctionid' ORDER BY amount DESC LIMIT 3");

$startbid = 100;
foreach($bids as $b){
        $startbid = max($startbid,($b['amount']+max(10,(floor($b['amount']/10)))));
}

if($_POST['bid']){
        if(!is_numeric($_POST['bid'])){
                $badbid=true;
        }
        
        if($_POST['bid'] < $startbid){
                $invalidbid = true;
        }else{
        
                if($_POST['ip'] && $svvalid){
                        $time = time();
                        $database->query("DELETE FROM promo_bids WHERE auctionID = '$auctionid' AND serverIP = '$_POST[ip]'");
                        $database->query("INSERT INTO promo_bids VALUES ('','$auctionid','$time','$_SESSION[id]','$_POST[ip]','$_POST[bid]','','','','')");
                        $bids = $database->query("SELECT * FROM promo_bids WHERE auctionID = '$auctionid' ORDER BY amount DESC LIMIT 3");

                        foreach($bids as $b){
                                $startbid = max($startbid,($b['amount']+max(10,(floor($b['amount']/10)))));
                        }
                }
        }
}

if($_GET['pay']){
        $bid = $database->query("SELECT * FROM promo_bids WHERE won = '1' AND id = '$_GET[pay]' AND auctionID = '$auctionid'",db::GET_ROW);
        if($database->num_rows == 0){
                header('Location: /promote/bid');
        }
        include '../lib/httprequest.php';
        include '../lib/paypal.php';
        

        $r = new PayPal(true);
        $r->pp_return = 'http://craftstats.com/bid?pp=paid';
        $r->pp_cancel = 'http://craftstats.com/promote/bid';
        $ret = $r->doExpressCheckout($bid['amount'], '30 day banner promotion for '.$bid['serverIP']);

        if($ret['ACK'] == 'Success'){
                $token = $ret['TOKEN'];
                $database->query("UPDATE promo_bids SET token = '$token' WHERE id = '$bid[id]'");
        }
}

if($_GET['pp'] == 'paid' && $_GET['ttoken']){
        require_once( '../lib/httprequest.php' );
        require_once( '../lib/paypal.php' );
        $r = new PayPal(true);
        if(!$_GET['ttoken'])$final = $r->doPayment();
        if ($final['ACK'] == 'Success' || $_GET['ttoken']) {
                $token = $final['TOKEN'];
                if($_GET['ttoken'])$token = $_GET['ttoken'];
                $database->query("UPDATE promo_bids SET paid = 1 WHERE token = '$token'",db::GET_ROW);
                $stime = (60*60*24*30) + time();
                $haspaid=true;
        }
}
$template->setHeadScripts('<script> 
                if (window != top) { 
                  top.location.replace(document.location); 
                } 
           </script> ');

$template->setTitle('Banner Auction');
$template->setMainH1('Banner Auction');
$template->show('header');

$database->query("SELECT * FROM users WHERE id = '$_SESSION[id]' AND blacklisted = 1");
if($database->num_rows == 1){
        echo '</br><strong>You\'re blacklisted from participating in CraftStats.</strong>';exit;
}
?>
<?php if($invalidbid){ ?>
    <div class="alert-box negative">
      That bid was lower than the minimum bid of $<?php echo $startbid; ?>.
    </div>
<?php } ?>
<?php if($_POST['ip'] && !$svvalid){ ?>
    <div class="alert-box negative">
      We're not currently tracking that server! Make sure you entered the IP address correctly and try again.
    </div>
<?php } ?>
<?php if($badbid){ ?>
    <div class="alert-box negative">
      Invalid Bid.
    </div>
<?php } ?>

    <section class="content main_content">
      <div class="sub_content banner_auction">
          <div class="banner_auction">
          <h3>Banner Auction</h3><div class="wrap"><span>This auction ends in <span><?php 
$diff=$auctionend-time();
$days=floor($diff/(60*60*24));
$hours=round(($diff-$days*60*60*24)/(60*60));
echo $days.' day'.($days != 1 ? 's':'').', '.$hours.' hour'.($hours != 1 ? 's':'').'.'; ?></span></span></div>
            <p>
              <p>You are bidding for a full month of a front-page, large banner ad on CraftStats.com. This auction will end on <?php echo date('F jS, g:ia T',$auctionend);?>. The top three bids will have exactly 72 hours to pay their bids. In the event that a bid is not paid, the bidder will be restricted from bidding in any other auctions. The <strong>top 3 bidders'</strong> ads will be featured on our front page after payment. Obviously, the 3 top bidders will be the only users paying after the auction ends. Good luck!</p>
            </p>
          </div>
      </div>
      <div class="sub_content">
          <h3>Place a bid (minimum $<?php echo $startbid; ?>)</h3>
          <form class="split_width" action="/promote/bid" method="post">
            <span>Bid amount<br>
            <input type="text" name="bid" placeholder="<?php echo $startbid; ?>" value="<?php echo $_POST['bid']; ?>"></input>
            </span>
            <span>Server IP<br>
            <input type="text" name="ip" placeholder="Server IP"></input>
            </span>
            <input type="submit"></input>
          </form>
      </div>
      <table>
        <tr>
          <th>#</th>
          <th>Server</th>
          <th>Bid Amount</th>
        </tr>
<?php if($bids == ''){ ?>
        <tr>
          <td></td>
          <td><div class="server">No bids have been placed.</div></td>
          <td></td>
        </tr>
<?php }else{
$i=1;
foreach($bids as $b){echo
        '<tr>
          <td>'.$i.'</td>
          <td><div class="server">'.$b['serverIP'].'</div></td>
          <td>$'.$b['amount'].'</td>
        </tr>';
$i++; } } ?>
        </tr>
      </table>
    </section>
  </div>
<?php
$template->show('footer');
?>