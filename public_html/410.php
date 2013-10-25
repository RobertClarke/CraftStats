<?php
include '../inc/global.inc.php';
header($_SERVER["SERVER_PROTOCOL"]." 410 Removed");
$template->setTitle('410');
$template->setMainH1('410');
$template->show('header');
?>
    <section class="content main_content">
      <div class="sub_content">
        <div class="wrap">
      		<h1>410 Removed</h1>
      		<p>The page you're looking has been removed.</p>
        </div>
      </div>
    </section>
  </div>
<?php
$template->show('footer');
?>