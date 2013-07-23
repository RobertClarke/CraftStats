<?php
include 'inc/global.inc.php';
if($_GET['qkw3eosa7ac89cc'] != '23c98ja0c890'){
echo 'fail';
exit;
}
echo 'set '.stripslashes(urldecode($_GET['ann']));
$memcache->set(md5('announce'),stripslashes(urldecode($_GET['ann'])),MEMCACHE_COMPRESSED,60*60*$_GET['time']);
?>