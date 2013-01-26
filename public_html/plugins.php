<?php
include '../inc/global.inc.php';
$data = array();
$dpoints = $database->query("SELECT defaultName AS dn,servers, version FROM plugins WHERE defaultName = '$_GET[name]' GROUP BY version");


foreach($dpoints as $update){
array_push($data,array('"'.$update['version'].'"',$update['servers']));
}

foreach($data as $row){
	$rows .= "[$row[0], $row[1]],
			";
}
$template->setTitle($dpoints[0][dn].' Plugin');
$template->setHeadScripts('<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
		var data = google.visualization.arrayToDataTable([
			["Version Name","Used By"],
			'.$rows.'
        ]);

        var options = {
          title: \''.$dpoints[0][dn].' Usage by Version\',
		  backgroundColor: \'#EAEAEA\',
		  pieSliceText: \'label\',
		  is3D:true,
        };

        var chart = new google.visualization.PieChart(document.getElementById(\'chart_div\'));
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
?>