<?php
include '../inc/global.inc.php'; 
$template->setTitle('404');
$template->show('header');
$template->show('nav');
?>

<h1 style="position:absolute;left:40px;top:50px;font-size:190px;">404</h1>
<h2 style="position:absolute;left:40px;top:310px;font-size:16px;">Maybe you could try looking <a href="/" style="color:#3A87AD;">somewhere else</a>?</h2>
</div>
<?
$template->show('footer');
?>