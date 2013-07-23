<?php
	set_include_path('/var/www/cstats/');
	$memcache_disable = true;
	include 'inc/global.inc.php';
	include 'lib/MCAPI.class.php';
	
	//API Key - see http://admin.mailchimp.com/account/api
	$apikey = '9497bf352a406d19391fb64f8d2d06c5-us7';
	// A List Id to run examples against. use lists() to view all
	// Also, login to MC account, go to List, then List Tools, and look for the List ID entry
	$listId = 'fa37327fda';
	
	$api = new MCAPI($apikey);
	
	$emails = $database->query("SELECT email FROM users WHERE email != ''");
	foreach($emails as $e){
		$batch[] = array('EMAIL'=>$e['email']);
	}
	$emails = $database->query("SELECT email FROM promo_order WHERE email != ''");
	foreach($emails as $e){
		$batch[] = array('EMAIL'=>$e['email']);
	}
	
	 
	$optin = false; //yes, send optin emails
	$up_exist = false; // yes, update currently subscribed users
	$replace_int = false; // no, add interest, don't replace
	 
	$vals = $api->listBatchSubscribe($listId,$batch,$optin, $up_exist, $replace_int);
	 
	if ($api->errorCode){
		echo "Batch Subscribe failed!\n";
		echo "code:".$api->errorCode."\n";
		echo "msg :".$api->errorMessage."\n";
	} else {
		echo "added:   ".$vals['add_count']."\n";
		echo "updated: ".$vals['update_count']."\n";
		echo "errors:  ".$vals['error_count']."\n";
		foreach($vals['errors'] as $val){
			echo $val['email_address']. " failed\n";
			echo "code:".$val['code']."\n";
			echo "msg :".$val['message']."\n";
		}
	}
?>