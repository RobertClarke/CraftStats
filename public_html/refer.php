<?php
include '../inc/global.inc.php';

if($_GET['url'] == ''){
 header('Location: http://craftstats.com/');
 exit;
}

//Database stuff goes here!
$time = time();
$database->prepare("INSERT INTO referrals VALUES (?,?,?,?,?)");
$database->execute(array($_GET['url'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_REFERER'], $SESSION['id'], $time));

header('Location: http://'.$_GET['url']);
?>
