<?php
include '../inc/global.inc.php';
$template->setTitle('Submit');
$template->setMainH1('Submit');
$template->show('header');
?>
  <?php
  if(count($errors) > 0){
  ?>
   <div class="alert-box negative">
        <?php foreach($errors as $e){
          echo $e.'<br/>';
        }
      ?>
    </div>
    <?php 
    }
  ?>
    <?php if($_GET['se']){ ?>
    <div class="alert-box negative">
      <?php echo $_GET['se']; ?>
    </div>
    <?php } ?>
    <section class="content main_content">
      <div class="sub_content">
        <div class="wrap" style="margin-bottom: 1em">
          <h3>Submit a server</h3>
        </div>
        <div class="half_width">
          <form action="/api" method="get">
          	<input type="hidden" name="req" value="m11"/>
            <input type="text" name="ip" placeholder="Server address"></input>
            <input type="text" name="email" placeholder="Email (optional)" <?php echo($_GET['ev']?'value="'.$_GET['ev'].'"':''); ?>></input>
            <input type="submit"></input>
          </form>
        </div>
        <div class="half_width">
          <p>Adding your server to our site means we'll track uptime, player activity and other stats depending on your setup.</p>
          <p>If you want to increase exposure to your server on the site, get players to vote for it! Send them to your server vote page, and when they vote you'll be pushed closer to the front page of CraftStats.com.</p>
        </div>
      </div>
    </section>
  </div>

<?php
$template->show('footer');
?>