<?php
include '../inc/global.inc.php';
header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
$template->setTitle('404');
$template->show('header');
$template->show('nav');
?>

<h1 style="position:absolute;left:40px;top:50px;font-size:190px;">404</h1>
<h2 style="position:absolute;left:40px;top:310px;font-size:16px;">The page you're looking for cannot be found.</h2>
</div>
<?
$template->show('footer');
?>
