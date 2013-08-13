<?php
include '../inc/global.inc.php';

if($_GET['url'] == ''){
	header('Location: http://minecraftservers.com/');
	exit;
}

//Database stuff goes here!
$time = time();
$database->query("INSERT INTO referrals VALUES ('$_GET[url]', '$_SERVER[REMOTE_ADDR]', '$_SERVER[HTTP_REFERER]', '$_SESSION[id]' ,'$time')");

header('Location: http://'.$_GET['url']);
?>
