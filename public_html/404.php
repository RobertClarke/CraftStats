<?php
include '../inc/global.inc.php';
header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
$template->setTitle('404');
$template->setMainH1('404');
$template->show('header');
?>
    <section class="content main_content">
      <div class="sub_content">
        <div class="wrap">
      		<h1>404 Not found</h1>
      		<p>The page you're looking for cannot be found</p>
        </div>
      </div>
    </section>
  </div>
<?php
$template->show('footer');
?>