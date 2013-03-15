<!DOCTYPE html>

<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8" />

  <!-- Set the viewport width to device width for mobile -->
  <meta name="viewport" content="width=device-width" />

  <title><?php echo $this->title; ?></title>

  
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

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-17509155-7']);
    _gaq.push(['_setSiteSpeedSampleRate', 10]);
  _gaq.push(['_trackPageview']);


  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<link rel="shortcut icon" href="http://dev.craftstats.com/images/favicon.ico" type="image/x-icon" />
  <?php echo $this->headscripts; ?>
</head>