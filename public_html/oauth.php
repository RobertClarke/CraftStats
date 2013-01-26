<?php
include_once '../inc/global.inc.php';
include_once '../lib/twitteroauth.php';

if($_GET['denied'] != ''){
	header('Location: /');
}
if($_GET['logout']){
	session_destroy();
	header('Location: /');exit;
}
if($_GET['login'] == 'twitter'){
	// The TwitterOAuth instance  
	$twitteroauth = new TwitterOAuth('HyI8Rfv5NwhU2pP3pZ3TA', 'nKVSmnejMIgRBWZT2ZSOJAHTzslBo2ZmHhqxvG7otM');  
	// Requesting authentication tokens, the parameter is the URL we will be redirected to  
	$request_token = $twitteroauth->getRequestToken('http://craftstats.org/oauth.php?process=twitter');  
	
	// Saving them into the session  
	$_SESSION['oauth_token'] = $request_token['oauth_token'];  
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];  
  
	// If everything goes well..  
	if($twitteroauth->http_code==200){  
		// Let's generate the URL and redirect  
		$url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']); 
		header('Location: '. $url); 
	} else { 
		// It's a bad idea to kill the script, but we've got to know when there's an error.  
		die('Something wrong happened.');  
	}	  
}
if($_GET['process'] == 'twitter'){

	if(!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])){  
		// TwitterOAuth instance, with two new parameters we got in twitter_login.php  
	
		$twitteroauth = new TwitterOAuth('HyI8Rfv5NwhU2pP3pZ3TA', 'nKVSmnejMIgRBWZT2ZSOJAHTzslBo2ZmHhqxvG7otM', $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);  
		// Let's request the access token  
		$access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']); 
		// Save it in a session var 
		$_SESSION['access_token'] = $access_token; 
		// Let's get the user's info 
		$user_info = $twitteroauth->get('account/verify_credentials'); 
		//print_r($user_info);exit;
		
		$log->log('generic','action',print_r($user_info,true));
		
		if(isset($user_info->error) || $access_token == ''){  
			// Something's wrong, go back to square 1 
				echo '1';exit;
			header('Location: index.php'); 
		} else { 
			// Let's find the user by its ID  
			$database->query("SELECT * FROM users WHERE oauth_provider = 'twitter' AND oauth_uid = ". $user_info->id);  
  
  
			// If not, let's add it to the database  
			if($database->num_rows == 0){  
			//echo '3';
				$time= time();
				$database->query("INSERT INTO users (ip, oauth_provider, oauth_uid, username, oauth_token, oauth_secret, created) VALUES ('{$_SERVER[REMOTE_ADDR]}','twitter', {$user_info->id}, '{$user_info->screen_name}', '{$access_token['oauth_token']}', '{$access_token['oauth_token_secret']}','$time')");  
				$userinfo = $database->query("SELECT * FROM users WHERE id = " . mysql_insert_id(),db::GET_ROW);  	
				$insert = true;
			} else {  
			//echo '2';
				// Update the tokens  
				$database->query("UPDATE users SET ip= '{$_SERVER[REMOTE_ADDR]}', oauth_token = '{$access_token['oauth_token']}', oauth_secret = '{$access_token['oauth_token_secret']}' WHERE oauth_provider = 'twitter' AND oauth_uid = {$user_info->id}");  
				$userinfo = $database->query("SELECT * FROM users WHERE oauth_provider = 'twitter' AND oauth_uid = ". $user_info->id,db::GET_ROW);  
				
				
				if($userinfo['mcuser'] != ''){
					$_SESSION['mcuser'] = $userinfo['mcuser'];
				}
			}  
			
			$cstats = new TwitterOAuth('HyI8Rfv5NwhU2pP3pZ3TA', 'nKVSmnejMIgRBWZT2ZSOJAHTzslBo2ZmHhqxvG7otM','822604988-0XI1o1HZIt1qAYliqgohLlDx7eghL89jEQOWDjWN','5CwbdSxnjnZPDjIXO8ZXW71UIMOopt3c2wlUBUQ5o78');
			
			$isfollowing = $cstats->get('friendships/exists', array('screen_name_a'=>'craftstats_','screen_name_b'=>$user_info->screen_name));

			if(!$isfollowing){  
				$cstats->post('friendships/create', array('screen_name' => $user_info->screen_name));  
			} 		
			$_SESSION['id'] = $userinfo['id']; 
			$_SESSION['username'] = $userinfo['username']; 
			$_SESSION['oauth_uid'] = $userinfo['oauth_uid']; 
			$_SESSION['oauth_provider'] = $userinfo['oauth_provider']; 
			$_SESSION['oauth_token'] = $userinfo['oauth_token']; 
			$_SESSION['oauth_secret'] = $userinfo['oauth_secret']; 
 
			if($insert || $_SERVER['HTTP_REFERER'] == ''){
			//echo '5';exit;
				header('Location: /account.php');  
			}else{
			//echo '4';exit;
				header('Location: '.$_SERVER['HTTP_REFERER']);
			}
		}  
	} else {  
		// Something's missing, go back to square 1  
		header('Location: /');  
	}  
}
?>