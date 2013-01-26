<?php if($_SERVER['SCRIPT_NAME'] == '/index.php'){ ?><div id="logowrap">
<a href="/" alt="home"><div id="logo"></div></a>
<div style="text-align:center;font-size:12px;color:#ADADAD;margin-top:13px;"><?php 

echo 'currently tracking <div class="spinner0 spinner"></div> players on <div class="spinner1 spinner"></div> servers with <div class="spinner2 spinner"></div> plugins'; ?></div>
</div>
<div class="box boxmiddle clearfix" style="width:338px;height:270px;float:right;margin-top:5px;">

<a href="/refer.php?url=bit.ly/SowYaa"><img src="/images/mclayerad.png" style="margin:10px auto;display:block;width:300px;height:250px;" /></a>
</div>
<?php }else{ ?>
<a href="/" alt="home"><div id="logo" style="float:left;margin:20px 20px 30px 20px;background:url(/images/cstatssmall.png) no-repeat;width:399px;height:58px;"></div></a>
<div style="float:right;margin:20px 20px 30px 20px;width:468px;height:60px;">
	<script type="text/javascript"><!--
	google_ad_client = "ca-pub-8782622759360356";
	/* CraftStats */
	google_ad_slot = "0756285012";
	google_ad_width = 468;
	google_ad_height = 60;
	//-->
	</script>
	<script type="text/javascript"
	src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
	</script>
</div>	
<?php } ?>