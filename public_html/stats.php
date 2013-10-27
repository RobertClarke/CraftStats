<?php
include '../inc/global.inc.php';
$template->setTitle("Statistics");
$template->setHeadScripts('
<script type="text/javascript">
window.setInterval(updateStats, 2000);
var last = '.(time()).';
function updateStats(){
	
	$.ajax({					
			type: "POST",
			url: "/api.php",
			data: "req=m02&since="+last,
			async: true,
			cache: false,
			dataType: "json",
			
			success: function(data){
				last = data.extra[2];
				
				$(\'.srate\').html(data.extra[1]);
				$(\'.drate\').html(data.extra[4]);
				$(\'.ptrate\').html(data.extra[5]);
				$(\'.updating\').html(data.extra[6]);
				$(\'.waiting\').html(data.extra[7]);
				$(data.extra[0]).each(function(){
					$(\'.batchprog\').eq(this[4]).animate({height: Math.max(Math.min(((this[1]/this[2])*100),100),4)+"%"}, 1200);
					$(\'.batchprog2\').eq(this[4]).animate({bottom: (Math.max(Math.min(((this[1]/this[2])*100),100),4)-1)+"%"}, 1200);
					$(\'.batchprogupd\').eq(this[4]).html(Math.floor(Math.min(((this[1]/this[2])*100),100))+"%");
					$(\'.threshold\').eq(this[4]).animate({bottom: Math.floor((this[3]/this[2])*100)+"%"},1200);
				});
				
				$(data.extra[3]).each(function(){
					var s = $(document.createElement("h3")).html(this.ip).css("position","absolute").css("top","-40%").css("right",Math.random()*300);
					
					$(\'.updlist\').append(s);
					window.setTimeout(function(){
					$(s).animate({top: "105%"},{
						duration:  7000+Math.floor(Math.random()*5000),
						specialEasing: {
							top: "swing",
						},
						complete: function() {
							$(this).remove();
						}
					});
					},(Math.random()*2000));
				});
			}
			
		});
}

</script>
');
$template->show('header'); ?>
    <section class="content main_content">
      <div class="sub_content">
        <div class="wrap">
                      Minecraft Server Activity</h1> 
                      <h3>currently averaging <span class="srate">0</span> servers/min (each server updated every <span class="ptrate">0</span> minutes)</h3>
                      <h3><span class="updating">0</span> servers currently being updated</h3>
                      <h3><span class="waiting">0</span> servers have not been updated for 10 minutes</h3>
        </div>
      </div>
    </section>
  </div>
<?php 

$slaves = $database->query("SELECT * FROM slaves");

foreach($slaves as $s){

?>
<div style="position:absolute;top:12%;left:<?php echo ($i*100)+24;?>px;height:86%;width:60px;background:#DDD;">
<h3 style="position:absolute;top:6px;left:<?php echo 12;?>px;"><?php echo $s['ip']; ?></h3>
<div style="position:relative;width:100%;height:100%;">
<div style="position:absolute;bottom:0px;background:#AEDDDD;height:40%;width:100%;" class="batchprog">
<h3 style="position:absolute;top:6px;left:<?php echo 12;?>px;z-index:10;" class="batchprogupd">loading</h3>

</div>
<div style="height:3px;width:100%;background:#FFF;position:absolute;bottom:40%;" class="threshold"></div>
</div>
</div>
<?php
$i++;
}
?><div style="position:absolute;right:24px;width:500px;top:0px;height:100%;overflow:hidden;" class="updlist">

</div>
</div>

<?php $template->show('footer'); ?>
