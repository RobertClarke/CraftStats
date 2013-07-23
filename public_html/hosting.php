<?php
/*if(isset($_POST['review']))*/$memcache_disable = true;
include '../inc/global.inc.php';

if($_GET['slug']){ 
$h = $database->query("SELECT * FROM hosts WHERE slug = '$_GET[slug]'",db::GET_ROW);
	if($database->num_rows == 0){
		header("Location: /hosting");
	}else{
		$template->setDesc($h['name'].' is a Minecraft server hosting provider featured on CraftStats, you can use this hosting provider to create your own minecraft server');
	}
}



$template->setTitle(($h['name'] != '' ? $h['name']: 'Minecraft Server Hosting Suggestions'));
$template->setHeadScripts('
	<script type="text/javascript">
	$(document).ready(function() {
		$(window).load(function(){
			!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
			(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=151420601618450";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));
		});
	});
</script>');
$template->show('header');
$template->show('nav');
?>
<div class="row">
	<div class="twelve columns">
		<div class="twelve columns box">
<?php if($_GET['slug']){ 
?>

<h3 style="margin-left:20px;margin-top:30px;text-align:left;"><a href="/hosting" style="color:#069;">&laquo;</a><a href="http://<?php echo $h['url']; ?>" style="margin-left:30px;"><?php echo $h['name']; ?></a></h3>


	<div style="position:absolute;left:50px;top:71px;font-size:12px;color:#444;padding-right:90px;">
	<a href="https://twitter.com/share" class="twitter-share-button" data-text="I just found an awesome minecraft host!" data-via="craftstats_" data-url="http://craftstats.com/host/<?php echo $h['slug']; ?>" data-count="none" data-lang="en">Tweet</a>

	<div class="fb-like" style="float:right;position:absolute;right:-8px;top:0px;margin-right:15px;" data-href="http://craftstats.com/host/<?php echo $h['slug']; ?>" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false" data-font="arial"></div>
	<div id="fb-root"></div>




	</div>

	<div class="clearfix" style="position:relative;top:30px;">
	<style type="text/css">
	
.hostdesc ul{
	list-style:disc inside;
	margin-left:30px;
}

.hostdesc h2{
	font-size:16px;
	margin:10px 0px;
}

.hostdesc p{
	margin-bottom:20px;
}
	</style>
		<div style="margin: 0px 30px;font-size:14px;width:530px;" class="hostdesc"><?php echo $h['longdesc']; ?>
		<?php
		$database->query("SELECT * FROM hostreview WHERE hostID = '$h[ID]' AND positive = 1",db::GET_ROW);
		$rpos = $database->num_rows;
		$database->query("SELECT * FROM hostreview WHERE hostID = '$h[ID]' AND positive = 0",db::GET_ROW);
		$rneg = $database->num_rows;
		?>
			<b><?php echo $h['name']; ?> has received <?php echo $rpos; ?> positive and <?php echo $rneg; ?> negative reviews</b></div>
		<!--<img style="float:right;border:2px solid #ccc; margin:0px 40px;width:280px;height:180px;" src="<?php echo 'http://api.webthumbnail.org?width=280&height=200&format=png&screen=1024&url='.$h['domain']?>"/>
		-->
	</div>

	

<div class="box clearfix" style="padding:30px;margin:20px 0px;">
<h3 style="text-align:center;" >Hosting Plans</h3>
<div  class="row">
<?php $pr = $database->query("SELECT * FROM hostproduct WHERE hostID = '$h[ID]' ORDER BY ppm ASC");

foreach($pr as $p){ ?>
<div class="three columns">

<h3 style="font-size:16px;text-align:center;margin-bottom:10px;"><?php echo $p['name'];?></h3>
<style type="text/css">
	table tbody tr td{
		font-size:12px;
	}
</style>
<table class="table table-striped" style="border-top:1px solid #ccc;font-size:11px;width:100%;">
 <tr><td><b>RAM</b></td><td><?php echo $p['ram'];?></td></tr>
 <tr><td><b>Disk</b></td><td><?php echo $p['hdd'];?></td></tr>
 <tr><td><b>Slots</b></td><td><?php echo $p['recslots'];?></td></tr>
</table>
<a href="http://<?php echo $h['url']; ?>" style="font-weight:normal;width:100%;" class="button expand" type="button">$<?php echo $p['ppm'];?>/month</a>
</div>
<?php } ?>
</div>
</div>

<div class="box clearfix" style="padding:30px 0px;margin:20px 0px;">

	<div class="row">
		<h3 style="margin-bottom:10px;margin-left:30px;">Recent Reviews</h3>
		
		<?php
			$reviews = $database->query("SELECT * FROM hostreview hr LEFT JOIN users u ON u.ID = hr.userID WHERE hr.hostID = '$h[ID]' ORDER BY hr.time ASC");
			foreach($reviews as $r){
				echo '<blockquote style="width:80%;margin:0 auto;border-left: 5px solid '.($r['positive'] == 0 ? '#b13c33':'#4fa54a').';">
			<p>'.stripslashes($r['text']).'</p>
			<small>'.($r['username'] == '' ? 'Anonymous' : '<a href="http://twitter.com/'.$r['username'].'" target="_new" style="font-weight:normal;font-size:16px;color:#3A87AD;">@'.$r['username'].'</a>').' '.($r['username'] == $_SESSION['username'] && $r['username'] != '' ? ' <br/> <br/><form action="/host/'.$_GET['slug'].'" method="POST"><input type="hidden" name="remove" value="'.$r['ID'].'"><button class="btn btn-small btn-inverse" type="submit">Remove</button></form>' : '').'</small>
		</blockquote>';
			}
			
			if($database->num_rows == 0){
				echo '<i style="margin-left:30px;">no reviews yet! :(</i>';
			}
		?>
		
		
	</div>
	<div style="padding-left:30px;" class="row">
	<h3 style="margin-bottom:10px;margin-top:30px;">Write a Review</h3>
	<?php if(!isset($_SESSION['username'])){
	?>
	<a href="/oauth.php?login=twitter"><img style="width:151px;height:24px;margin-top:10px;" src="/images/signintwitter.png"></a>
	<?php
	}else{ 
	if(isset($_POST['review'])){
		$prev = $database->query("SELECT * FROM hostreview WHERE userID = '$_SESSION[id]' AND hostiD = '$h[ID]'",db::GET_ROW);
		$update = 0;
		if($database->num_rows > 0)$update = $prev['ID'];
		$positive = 0;
		if(isset($_POST['positive']))$positive = 1;
		
		$time=time();
		if($update != 0){
			$database->query("UPDATE hostreview SET positive = '$positive', text = '$_POST[review]', time = '$time' WHERE ID = '$update'");
		}else{
			$database->query("INSERT INTO hostreview VALUES('','$h[ID]','$_SESSION[id]','$time','$positive','$_POST[review]')");
		}
		
	}
	
	if(isset($_POST['remove'])){
		$database->query("DELETE FROM hostreview WHERE userID = '$_SESSION[id]' AND ID = '$_POST[remove]'");
	}
	?>
	
	<form action="/host/<?php echo $_GET['slug']; ?>" method="POST">
	<textarea rows="3" style="width:400px;" placeholder="Write a review on <?php echo $h['name']; ?> here.." name="review"></textarea>
	
	<button class="btn btn-success" name="positive" type="submit"><i class="icon-thumbs-up icon-white"></i> This is a positive review</button>
	<button class="btn btn-danger" name="negative" type="submit"><i class="icon-thumbs-down icon-white"></i> This is a negative review</button>
	
	</form>
	<?php } ?>
	</div>
</div>
<?php if($h['demoIP'] != '' ){ ?>
<div class="box clearfix" style="padding:30px;margin:20px 0px;">
<h3 style="margin-bottom:20px;margin-top:-10px;">Demo Server</h3>
<a href="/server/<?php echo $h['demoIP']; ?>"><img style="border-radius:3px;" src="/banner/<?php echo $h['demoIP']; ?>"/></a>
</div>
<?php } ?>
</div>

<?php }else{ ?>
<div class="row">
<h3 style="float:left;padding-left:20px;margin-bottom:20px;">Minecraft Server Hosts</h3>
</div>
<div class="row" style="margin-bottom:30px;">
	<?php $hosts = $database->query("SELECT * FROM hosts");
	
	foreach($hosts as $h){?>
	<div class="box inset" style="float:left;margin-left:20px;width:280px;margin-bottom:20px;height:80px;">
		<img style="float:left;margin:3px;border-radius:8px;width:75px;" src="/images/hosts/<?php echo $h['ID']; ?>.png"/>
		<div style="float:right;margin-left:5px;width:190px;line-height:1.2;">
			<h3 style="font-size:16px;"><a href="/host/<?php echo $h['slug'];?>"><?php echo $h['name'];?></a></h3>
			<span style="color:#555;font-size:11px;font-weight:bold;"><?php echo $h['shortdesc']; ?></span>
		</div>
	</div>
	<?php } ?>
	
	<div style="text-align:center;color:#777;font-size:12px;float:left;position:absolute;bottom:15px;left:25px;">If you're a minecraft host, you can <a href="https://docs.google.com/spreadsheet/viewform?formkey=dC1kQ3FhX1lsUkFqN0R6TFd0c0gtb0E6MQ#gid=0">apply here</a></div>
</div> 
<?php
}
?>
</div>
</div>
</div>
<?php
$template->show('footer');
?>