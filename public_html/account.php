<?php
include '../inc/global.inc.php';
if($_SESSION['username'] == ''){
	header("Location: /");
}
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
$template->show('logo');
?>
</div>
<div id="container">
<div class="box boxtop clearfix" style="padding-left:20px;"> 
<h2>@<?php echo $_SESSION['username']; ?></h2>
</div>
<div class="box boxbottom clearfix" style="padding:30px;font-size:12px;font-weight:bold;"> 
<?php
	if($auth != '' && !stristr($auth,':')){
		echo '<div class="alert alert-error">'.$auth.'</div>';
	}
?>

<?php if($_SESSION['mcuser'] == ''){ ?>
Please link your minecraft account, this page is very boring without it!<br/><br/>

<form action="/account.php" method="post">
<input name="user" type="text" class="mcreg" placeholder="minecraft username"/>
<input name="pass" type="password" class="mcreg" placeholder="minecraft password"/>
<button class="mcreg">Link Account</button>
</form>

<?php } ?>
</div>
<?php
$template->show('footer');
?>