<?php
include '../inc/global.inc.php';
$memcache_disable = true;

if($_GET['slug']){
$h = $database->query("SELECT * FROM hosts WHERE slug = '$_GET[slug]'",db::GET_ROW);
        if($database->num_rows == 0){
                header("Location: /hosting");
        }else{
                $template->setDesc($h['name'].' is a Minecraft server hosting provider featured on Minecraft Servers. This includes reviews and plans.');
        }
}else{
        $template->setDesc('Find the best Minecraft host on our minecraft server host list. Look at reviews for the best hosting companies out there.');
}
$template->setHeadScripts('
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

$template->setTitle(($h['name'] != '' ? $h['name']: 'Minecraft Hosting'));
$template->setMainH1(($h['name'] != '' ? $h['name']: 'Minecraft Hosting'));
$template->show('header');
?>
<?php if($_GET['slug']){ 
?>
<?php
    $database->query("SELECT * FROM hostreview WHERE hostID = '$h[ID]' AND positive = 1",db::GET_ROW);
    $rpos = $database->num_rows;
    $database->query("SELECT * FROM hostreview WHERE hostID = '$h[ID]' AND positive = 0",db::GET_ROW);
    $rneg = $database->num_rows;
?>
    <section class="content main_content">
      <div class="featured_server sub_content">
        <div>
          <div class="server_info">
            <h4><?php echo $h['name']; ?></h4>
            <a href="http://<?php echo $h['url']; ?>"><?php echo $h['domain']; ?></a>
          </div>
          <div class="back_hosting">
            <a href="/hosting" class="btn">Back to hosting page</a>
          </div>
            <p><?php echo $h['longdesc']; ?></p>
        </div>
      </div>
      <div class="sub_content">
      <h3>Hosting Plans</h3>
<?php $pr = $database->query("SELECT * FROM hostproduct WHERE hostID = '$h[ID]' ORDER BY ppm ASC");

foreach($pr as $p){ ?>
        <ul class="quater_width">
          <li><span><?php echo $p['name'];?></span></li>
          <li>RAM: <?php echo $p['ram'];?></li>
          <li>Disk: <?php echo $p['hdd'];?></li>
          <li>Slots: <?php echo $p['recslots'];?></li>
          <a href="http://<?php echo $h['url']; ?>" class="btn">$<?php echo $p['ppm'];?>/Month</a>
        </ul>
<?php } ?>
      </div>
      <div class="sub_content">
        <div class="review_info">
          <h3><?php echo $h['name']; ?> Reviews</h3>
          <span class="positive">1 positive review</span><span class="negative">1 negative review</span>
        </div>
        <div class="write_review">
          <a href="#" class="btn">Write a review</a>
        </div>
<?php $reviews = $database->query("SELECT * FROM hostreview hr LEFT JOIN users u ON u.ID = hr.userID WHERE hr.hostID = '$h[ID]' ORDER BY hr.time ASC");

foreach($reviews as $r){ ?>
        <p class="review <?php if($r['positive'] == 0){echo 'bad';}else{echo 'good';} ?>">
          <?php echo (stripslashes($r['text'])); ?>
        </p>
        <p class="comment_author review">-<strong><?php if($r['username'] == ''){echo 'Ananymous';}else{echo $r['username'];} ?></strong></p>
<?php } ?>
      </div>
    </section>
<?php }else{ ?>
    <section class="content main_content">
      <table>
        <tr>
          <th>Server Host</th>
          <th>Positive Review</th>
          <th>Negative Review</th>
          <th>Description</th>
        </tr>
<?php
        $hosts = $database->query("SELECT * FROM hosts WHERE sponsorTime < UNIX_TIMESTAMP() ");
        $hosts2 = array();
        foreach($hosts as $i => $h){
                $database->query("SELECT * FROM hostreview WHERE hostID = '$h[ID]' AND positive = 1",db::GET_ROW);
                $rpos = $database->num_rows;
                $database->query("SELECT * FROM hostreview WHERE hostID = '$h[ID]' AND positive = 0",db::GET_ROW);
                $rneg = $database->num_rows;
                $h['score'] = $rpos-$rneg;
                $h['pos'] = $rpos;
                $h['neg'] = $rneg;
                $hosts2[$i]=$h;
                }
usort($hosts2, function($a, $b) {
    return $b['score'] - $a['score'];
});

        foreach($hosts2 as $h){
        $h['shortdesc'] = mb_convert_encoding($h['shortdesc'], "ISO-8859-1", "UTF-8");
?>
        <tr>
          <td><a href="/host/<?php echo $h['slug'];?>"><?php echo $h['name'];?></a></td> <td> <div class="t_positive_reviews"><?php echo $h['pos']; ?></div></td>
          <td><div class="t_negative_reviews"><?php echo $h['neg']; ?></div></td> <td><?php echo $h['shortdesc']; ?></td>
        </tr>
<?php } ?>
      </table>
<?php } ?>
    </section>
  </div>
<?php
$template->show('footer');
?>