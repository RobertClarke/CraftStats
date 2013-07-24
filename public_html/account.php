<?php
include '../inc/global.inc.php';
if($_SESSION['username'] == ''){
	header("Location: /login");
}
header("Location: /myservers");exit;
if($_SESSION['mcuser'] != ''){
	header('Location: /player/'.$_SESSION['mcuser']);
}

if(isset($_POST['user']) && isset($_POST['pass'])){
	$auth = file_get_contents('https://login.minecraft.net/?user='.$_POST['user'].'&password='.$_POST['pass'].'&version=999');
}

if($auth != '' && stristr($auth,':')){
		$auth = explode(':',$auth);
		$database->query("UPDATE users SET mcuser = '$auth[2]' WHERE username = '$_SESSION[username]'");
		$_SESSION['mcuser'] = $auth[2];
		
		
		$pn = mysql_real_escape_string($auth[2]);
		
		$player = $database->query("SELECT * FROM players WHERE username = '$pn'",db::GET_ROW);
		if($player['username'] == ''){
			$database->query("INSERT INTO players VALUES('','$pn','0',0)");
		}
		
		header("Location: /player/{$auth[2]}");
	}
$template->setTitle('Your Account');
$template->show('header');
$template->show('nav');
?>
<div class="row">
	<div class="twelve columns">
	<?php
	if($auth != '' && !stristr($auth,':')){
		echo '<div class="alert-box alert" style="margin-top:20px;">'.$auth.'</div>';
	}
?>
		<div class="twelve columns box">
<h3><?php echo $_SESSION['username']; ?></h3>


<?php if($_SESSION['mcuser'] == ''){ ?>
Please link your minecraft account, this page is very boring without it!<br/> Authentication is done directly via mojang's servers.<br/><br/>
<div class="row">
<div class="four columns">
<form action="/account.php" method="post">
<input name="user" type="text" placeholder="minecraft username"/>
<input name="pass" type="password" placeholder="minecraft password"/>
<button class="button">Link Account</button>
</form>
</div>
</div>
<?php } ?>
</div>
</div>
</div>
<?php
$template->show('footer');
?>