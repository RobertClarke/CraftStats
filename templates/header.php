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
  <?php echo $this->headscripts; ?>
</head>