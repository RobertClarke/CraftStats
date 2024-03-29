<?php
include '../inc/global.inc.php';

if($_GET['post']){
	$_SESSION['loginredirect'] = $_GET['post'];
}

if($_GET['u']){
	$upgrade=1;
}

if($_GET['r']){
	$register=1;
}

if($_GET['fp']){
	$reset=1;
}

if($_GET['fpc']){
	$resetconfirm=1;
}

$errors = array();

if($_POST['action'] == 'register'){
	$register=1;
}

if($_POST['action'] == 'upgrade'){
	$upgrade=1;
}

if($_POST['action'] == 'reset'){
	$reset=1;
}

if($_POST['action'] == 'upgrade' || $_POST['action'] == 'register'){
	if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))array_push($errors,'Invalid Email: '.$_POST['email']);
	$database->query("SELECT * FROM users WHERE username = '$_POST[user]'");
	if($database->num_rows>0)array_push($errors,'Username \''.$_POST['user'].'\' already taken');
	$database->query("SELECT * FROM users WHERE email = '$_POST[email]'");
	if($database->num_rows>0)array_push($errors,'Email \''.$_POST['email'].'\' already taken');
	if($_POST['pass'] != $_POST['pass2'])array_push($errors,'Passwords do not match');
}

if($_POST['action'] == 'login'){
	$pass = hashPass($_POST['pass']);
	$user = $database->query("SELECT * FROM users WHERE email = '$_POST[email]' AND pass_hash = '$pass'",db::GET_ROW);
	if($database->num_rows == 0)array_push($errors,'Incorrect username or password');
}

if($_POST['action'] == 'reset'){
	if($user == ''){
		$user = $database->query("SELECT * FROM users WHERE username = '$_POST[user]' OR email = '$_POST[user]'");
		if($database->num_rows==0)array_push($errors,'User does not exist');
	}else{
		array_push($errors,'User does not exist');
	}
}

if($_POST['action'] == 'resetconfirm' && $_GET['code']){
	$user = $database->query("SELECT * FROM users WHERE resetcode = '$_GET[code]'",db::GET_ROW);
	if($database->num_rows == 0 || $user['resetexpire'] < time())array_push($errors,'That password reset link has expired.');
	$resetconfirm = 0;
	$_POST['action'] = '';
}

if($_POST['action'] == 'resetconfirm' && $_POST['code']){
	if($_POST['pass']){
		if($_POST['pass'] != $_POST['pass2'])array_push($errors,'Passwords do not match');
	}
	$user = $database->query("SELECT * FROM users WHERE resetcode = '$_POST[code]'",db::GET_ROW);
	if($database->num_rows == 0 || $user['resetexpire'] < time())array_push($errors,'That password reset link has expired.');
	$resetconfirm = 0;
	$_POST['action'] = '';
	$doreset = true;
}

if($doreset && $_POST['code'] && count($errors) == 0){
	$pass = hashPass($_POST['pass']);
	$database->query("UPDATE users SET pass_hash = '$pass',resetexpire = 0,resetcode='' WHERE id = $user[id]");
	array_push($errors,'Password successfully reset');
	$resetconfirm = 0;
	$_POST['action'] = '';
}

if($_POST['action'] == 'reset' && count($errors) == 0){
	$code = md5(time().$_POST['user']);
	$expire = time() + 60*60*24;
	$database->query("UPDATE users SET resetcode = '$code',resetexpire = $expire WHERE username = '$_POST[user]'");
	$user = $database->query("SELECT * FROM users WHERE username = '$_POST[user]'",db::GET_ROW);
	$to      = $user['email'];
	$subject = 'CraftStats Password Reset';
	$message = 'Click this link to reset your password, link will work for the next 24 hours: <a href="http://craftstats.com/login?fpc=1&code='.$code.'">Reset Password</a>';
	$headers = 'From: noreply@craftstats.com'."\r\n";
	$headers  .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	mail($to,$subject,$message,$headers);
	array_push($errors,'Email sent to '.substr($to, 0,3).'*****'.substr($to, -3).'. Reset link will expire in 24 hours.');
}

if($_POST['action'] && $_POST['action'] != 'reset' && count($errors) == 0){
	$pass = hashPass($_POST['pass']);
	if($_POST['action'] !='login')$database->query("UPDATE users SET pass_hash = '$pass',email='$_POST[email]',username='$_POST[user]',requiresupgrade = 0 WHERE id = '$_SESSION[id]'");
	if($_POST['action'] == 'upgrade' || $_POST['action'] == 'register'){
		$_SESSION['username'] = $_POST['user']; 
	}
	if($_POST['action'] == 'login'){
		$_SESSION['id'] = $user['id'];
		$_SESSION['username'] = $user['username'];
		$_SESSION['mcuser'] = $user['mcuser'];
	}
	header('Location: '.($_SESSION['loginredirect'] ? $_SESSION['loginredirect'] : '/'));
}

$template->setTitle(($reset? 'Reset Password' : ($_GET['r'] ? 'Register':($_GET['u'] ? 'Upgrade Account' : 'Login'))));
$template->setMainH1(($reset? 'Reset Password' : ($_GET['r'] ? 'Register':($_GET['u'] ? 'Upgrade Account' : 'Login'))));

$template->show('header');
?>
	<?php
	if(count($errors) > 0){
	?>
   <div class="alert-box negative">
				<?php foreach($errors as $e){
					echo $e.'<br/>';
				}
			?>
    </div>
    <?php 
		}
	?>
    <section class="content main_content">
      <div class="sub_content">
        <div class="wrap" style="margin-bottom: 1em">
          <h3><?php echo (($resetconfirm || $reset) ? 'Reset Password' : ($register ? 'Sign Up':($upgrade?'Upgrade Account':'Login'))); ?></h3>
        </div>
        <div class="half_width">
          <form action="/login" method="post">
			<?php
				if($resetconfirm){
					echo '<input type="hidden" name="code" value="'.($_GET['code']?$_GET['code']:$_POST['code']).'"/>';
				}
			?>
			<input type="hidden" name="action" value="<?php echo ($resetconfirm ? 'resetconfirm' : ($reset ? 'reset' : ($register ? 'register':($upgrade? 'upgrade' : 'login')))); ?>" />
            <input name="email" type="text" placeholder="Email"></input>
            <input name="pass" type="password" placeholder="Password"></input>
            <input type="submit"></input>
          </form>
        </div>
        <div class="half_width">
          <a href="/login?r=1" class="btn btn-block">Login with email</a>
          <a href="/oauth?login=twitter" class="btn btn-block">Login with Twitter</a>
        </div>
      </div>
    </section>
  </div>
<?php
$template->show('footer');
?>