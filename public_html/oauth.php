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
$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => 'LikmqUGSLAAWgZ8zCVC2A',
  'consumer_secret' => '6PlWlXC6ugcwpY0SrlZ48uvc9KNHCPpVhpGjH6O6U',
));

session_start();

function outputError($tmhOAuth) {
  echo 'There was an error: ' . $tmhOAuth->response['response'] . PHP_EOL;
}

function wipe() {
  session_destroy();
  header('Location: ' . tmhUtilities::php_self());
}


// Step 1: Request a temporary token
function request_token($tmhOAuth) {
  $code = $tmhOAuth->request(
    'POST',
    $tmhOAuth->url('oauth/request_token', ''),
    array(
      'oauth_callback' => tmhUtilities::php_self()
    )
  );

  if ($code == 200) {
    $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
    authorize($tmhOAuth);
  } else {
	print_r($tmhOAuth);
    outputError($tmhOAuth);
  }
}


// Step 2: Direct the user to the authorize web page
function authorize($tmhOAuth) {
  $authurl = $tmhOAuth->url("oauth/authorize", '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}";
  header("Location: {$authurl}");

  // in case the redirect doesn't fire
  echo '<p>To complete the OAuth flow please visit URL: <a href="'. $authurl . '">' . $authurl . '</a></p>';
}


// Step 3: This is the code that runs when Twitter redirects the user to the callback. Exchange the temporary token for a permanent access token
function access_token($tmhOAuth) {
  $tmhOAuth->config['user_token']  = $_SESSION['oauth']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];

  $code = $tmhOAuth->request(
    'POST',
    $tmhOAuth->url('oauth/access_token', ''),
    array(
      'oauth_verifier' => $_REQUEST['oauth_verifier']
    )
  );

  if ($code == 200) {
    return $tmhOAuth->extract_params($tmhOAuth->response['response']);
	}else{
		return false;
	}
}

function verify_credentials($tmhOAuth) {
  $tmhOAuth->config['user_token']  = $_SESSION['access_token']['oauth_token'];
  $tmhOAuth->config['user_secret'] = $_SESSION['access_token']['oauth_token_secret'];

  $code = $tmhOAuth->request(
    'GET',
    $tmhOAuth->url('1/account/verify_credentials')
  );

  if ($code == 200) {
    return json_decode($tmhOAuth->response['response']);
  } else {
    outputError($tmhOAuth);
  }
}

if($_GET['login'] == 'twitter'){
	request_token($tmhOAuth);
}
if (isset($_REQUEST['oauth_verifier'])){

	$access_token = access_token($tmhOAuth);
	// Let's find the user by its ID  
	$database->query("SELECT * FROM users WHERE oauth_provider = 'twitter' AND oauth_uid = ". $access_token['user_id']);  


	// If not, let's add it to the database  
	if($database->num_rows == 0){  
	//echo '3';
		$time= time();
		$database->query("INSERT INTO users (ip, oauth_provider, oauth_uid, username, oauth_token, oauth_secret, created) VALUES ('{$_SERVER[REMOTE_ADDR]}','twitter', {$access_token['user_id']}, '{$access_token['screen_name']}', '{$access_token['oauth_token']}', '{$access_token['oauth_token_secret']}','$time')");  
		$userinfo = $database->query("SELECT * FROM users WHERE id = " . mysql_insert_id(),db::GET_ROW);  	
		$insert = true;
	} else {  
	//echo '2';
		// Update the tokens  
		$database->query("UPDATE users SET ip= '{$_SERVER[REMOTE_ADDR]}', oauth_token = '{$access_token['oauth_token']}', oauth_secret = '{$access_token['oauth_token_secret']}' WHERE oauth_provider = 'twitter' AND oauth_uid = {$access_token['user_id']}");  
		$userinfo = $database->query("SELECT * FROM users WHERE oauth_provider = 'twitter' AND oauth_uid = ". $access_token['user_id'],db::GET_ROW);
		
		if($userinfo['mcuser'] != ''){
			$_SESSION['mcuser'] = $userinfo['mcuser'];
		}
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
?>