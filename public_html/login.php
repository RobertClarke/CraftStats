<?php
include '../inc/global.inc.php';



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
	$user = $database->query("SELECT * FROM users WHERE username = '$_POST[user]'");
	if($database->num_rows==1)array_push($errors,'User \''.$_POST['user'].'\' does not exist');
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
}

if($_POST['action'] == 'resetconfirm' && $_POST['code'] && count($errors) == 0){
	$pass = hashPass($_POST['pass']);
	$database->query("UPDATE users SET pass_hash = '$pass' WHERE id = $user[id]");
	array_push($errors,'Password successfully reset');
	$resetconfirm = 0;
	$_POST['action'] = '';
}

if($_POST['action'] == 'reset' && count($errors) == 0){
	$code = md5(time().$_POST['user']);
	$expire = time() + 60*60*24;
	$database->query("UPDATE users SET resetcode = '$code',resetexpire = $expire");
	$to      = $user['email'];
	$subject = 'CraftStats Password Reset';
	$message = 'Click this link to reset your password, link will work for the next 24 hours: <a href="/login?fpc=1&code='.$code.'">Reset Password</a>';
	$headers = 'From: noreply@craftstats.com'."\r\n";
	$headers  .= 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	array_push($errors,'Email sent. Reset link will expire in 24 hours.');
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
	header('Location: /account');
}

$template->setTitle(($_GET['r'] ? 'Register':($_GET['u'] ? 'Upgrade Account' : 'Login')));
$template->show('header');
$template->show('nav');

?>
<div class="row">
	<div class="twelve columns">
		<?php
		if(count($errors) > 0){
		?>
		<div class="alert-box" style="margin-top:20px;">
			<?php
				foreach($errors as $e){
					echo $e.'<br/>';
				}
			?>
		</div>
		<?php 
		}
		?>
		<div class="twelve columns box">
			<div class="row">
				<div class="twelve columns">
					<h5><?php echo (($resetconfirm || $reset) ? 'Reset Password' : ($register ? 'Sign Up':($upgrade?'Upgrade Account':'Login to CraftStats'))); ?></h5>
				</div>
			</div>
			<div class="row">
				<div class="six columns">
					<?php if($_GET['se']){ ?>
					<div class="alert-box alert">
					<?php echo $_GET['se']; ?>
					</div>
					<?php } ?>
					<form action="/login" method="post">
					<?php
						if($resetconfirm){
							echo '<input type="hidden" name="code" value="'.($_GET['code']?$_GET['code']:$_POST['code']).'"/>';
						}
					?>
					<input type="hidden" name="action" value="<?php echo ($resetconfirm ? 'resetconfirm' : ($reset ? 'reset' : ($register ? 'register':($upgrade? 'upgrade' : 'login')))); ?>" />
						<?php if(!$reset && !$resetconfirm){ ?><div class="row collapse">
								<div class="eight columns">
									<input type="text" name="email" placeholder="Email" />
								</div>
						</div>
						<?php } ?>
						<?php if($upgrade || $register || $reset){ ?>
						<div class="row collapse">
								<div class="eight columns">
								  <input type="text" name="user" placeholder="Username" />
								</div>
								<?php
									if($reset){
									?>
									<div class="four mobile-one columns">
								  <button class="button expand postfix" style="padding:0px;">Submit</button>
								</div>
									<?php
									}
								?>
						</div>
								<?php } ?>
								<?php if(!$reset){ ?>
						<div class="row collapse">
								<div class="eight mobile-three columns">
								  <input type="password" name="pass" placeholder="Password" />
								</div>
								<?php if($upgrade || $register ||  $resetconfirm){ ?>
								<div class="eight mobile-three columns">
								  <input type="password" name="pass2" placeholder="Confirm Password" />
								</div>
								<?php }  ?>
								<div class="four mobile-one columns">
								  <button class="button expand postfix" style="padding:0px;">Submit</button>
								</div>
								<?php if(!$upgrade && !$register && !$resetconfirm){ ?>
								<div class="twelve columns">
									<a href="/login?fp=1" style="font-size:12px;">Forgot your password?</a>
								</div>
								<?php } }?>
						</div>
					</form>
				</div>
				<?php if(!$upgrade && !$register && !$reset && !$resetconfirm){ ?>
				<div class="six columns">
					<a href="/login?r=1" class="button expand secondary" >Register with Email</a>
					<a href="/oauth?login=twitter" class="button expand secondary" style="margin-top:5px;">Sign in with Twitter</a>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<?php
$template->show('footer');
?>