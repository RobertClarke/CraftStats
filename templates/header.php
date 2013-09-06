<!DOCTYPE html>

<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8" />

  <!-- Set the viewport width to device width for mobile -->
  <meta name="viewport" content="width=device-width" />
  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta http-equiv="Lang" content="en">
  <meta name="description" content="<?php echo $this->desc; ?>">
  <meta name="keywords" content="<?php echo $this->keys; ?>">
  <title><?php echo $this->title; ?></title>

  <meta name="wot-verification" content="22beee5a47b34aec6495"/>
  <!-- Included CSS Files (Compressed) -->
  <link rel="stylesheet" href="/stylesheets/foundation.min.css">
  <link rel="stylesheet" href="/stylesheets/app.css">
  <!--[if !IE 7]>
  <style type="text/css">
    #wrap {display:table;height:100%}
  </style>
<![endif]-->
	  <!-- Included JS Files (Compressed) -->
  <script src="/javascripts/jquery.js"></script>
  <script src="/javascripts/foundation.min.js"></script>
  
  <!-- Initialize JS Plugins -->
  <script src="/javascripts/app.js"></script>
  <script src="/javascripts/modernizr.foundation.js"></script>
  <script type="text/javascript">
  var GoSquared = {};
  GoSquared.acct = "GSN-830142-V";
  (function(w){
    function gs(){
      w._gstc_lt = +new Date;
      var d = document, g = d.createElement("script");
      g.type = "text/javascript";
      g.src = "//d1l6p2sc9645hc.cloudfront.net/tracker.js";
      var s = d.getElementsByTagName("script")[0];
      s.parentNode.insertBefore(g, s);
    }
    w.addEventListener ?
      w.addEventListener("load", gs, false) :
      w.attachEvent("onload", gs);
  })(window);
</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-8521263-30', 'craftstats.com');
  ga('send', 'pageview');

</script>
<script type="text/javascript">
	$(document).ready(function(){
		$(".togglelogos").click(function(){
			 $('.box.logo').toggleClass("open",100);
		});
	});
  </script>
<link rel="shortcut icon" href="http://minecraftservers.com/images/favicon.ico" type="image/x-icon" />
  <?php echo $this->headscripts; ?>
</head>
