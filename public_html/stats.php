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
$template->show('header');
/*$data = array();
$dpoints = $database->query("SELECT * FROM (SELECT * FROM serviceinfo ORDER BY time DESC LIMIT 0,200) AS u ORDER BY u.time ASC");
$i = 0;
foreach($dpoints as $update){
array_push($data,array('new Date('.($update['time']*1000).')',(($dpoints[$i]['avgUpdate']+$dpoints[$i-1]['avgUpdate']+$dpoints[$i-2]['avgUpdate']+$dpoints[$i-3]['avgUpdate'])/(min(4,$i+1))),$update['load'],$update['queueSize']));
$i++;
}

foreach($data as $row){
	$rows .= "[$row[0], $row[1], $row[2],$row[3]],";
}
$template->setHeadScripts('<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
		var data = new google.visualization.DataTable();
        data.addColumn("datetime", "Time");
        data.addColumn("number", "Average Server Processing Time (ms)");
		data.addColumn("number", "Server Load %");
		data.addColumn("number", "Servers Remaining in Processing Queue");
		data.addRows([
          '.$rows.'
        ]);

        var options = {
          title: \'CraftStats Server Activity\',
		  focusTarget: \'category\',
		  curveType: "function",
		  interpolateNulls: false,
		  backgroundColor: \'#EAEAEA\',
		  vAxis: {viewWindow:{min:0}},
        };

        var chart = new google.visualization.LineChart(document.getElementById(\'chart_div\'));
        chart.draw(data, options);
      }
    </script>');
$template->show('header');
$template->show('nav');
$template->show('logo');
?>
</div>
<div id="container">

<div class="box">
<div id="chart_div" style="width: 943px; height: 500px;"></div>


</div>
<?php
$template->show('footer');
?>*/

?>
<style type="text/css">
*{
	font-size:14px !important;
}
</style>
<h1 style="position:absolute;top:20px;left:20px;">CraftStats Server Activity</h1> <h3 style="position:absolute;top:30px;left:400px;">currently updating at a rate of <span class="srate">0</span> servers per minute over <span class="ptrate">0</span> threads</h3>


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