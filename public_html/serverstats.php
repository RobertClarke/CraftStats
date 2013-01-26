<?php
include '../inc/global.inc.php';
 
$countries = $database->query("SELECT latitude,longitude, ip, country FROM servers");
$first = true;
foreach($countries as $c){
	if($c['latitude'] == 0 && $c['longitude'] == 0)continue;
	$hmd .= ($first ? '' : ',').'{location: new google.maps.LatLng('.$c['latitude'].', '.$c['longitude'].'),title: \''.$c['ip'].'\'}';
	$first = false;
}

$template->setHeadScripts('
<script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCnyUch-nINZBmsxZjuQc67_fGn3M1dYqI&sensor=false&libraries=visualization">
    </script>
    <script type="text/javascript">
		$(document).ready(function() {
			initialize();
		});
      function initialize() {
	  var heatmapData = [
			'.$hmd.'
		];
        var mapOptions = {
		center: new google.maps.LatLng(37.06250000, -95.67706800),
          zoom: 2,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("map_canvas"),
            mapOptions);
			
		var heatmap = new google.maps.visualization.HeatmapLayer({
  data: heatmapData,
  dissipating:false,
  radius:10
});
heatmap.setMap(map);

/*for (var i = 0; i < heatmapData.length; i++) {
    var loc = heatmapData[i];
    var marker = new google.maps.Marker({
        position: loc.location,
		title:loc.title,
        map: map,
    });
  }*/
      }
    </script>');
$template->setTitle('Minecraft Server Locations');
$template->show('header');
$template->show('nav');
$template->show('logo');
?>
</div>
<div id="container" class="clearfix">
<div class="box boxtop clearfix" style="padding-left:30px;padding-top:10px;">
<h2 style="float:left;">Server Locations</h2>
</div>
<div class="box boxbottom clearfix" style="padding-left:30px;padding-top:10px;">
<div id="map_canvas" style="width:900px;height:400px;"></div>
</div> 
<?php
$template->show('footer');
?>