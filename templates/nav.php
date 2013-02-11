<body>
  <div class="navigation">
    
  </div>

  <div id="wrap">
    <div class="row" id="main">
    
    <div class="three columns">
      <div class="row"> 
        <a href="/"><div class="twelve columns box logo">
        </div></a>
      </div>
      <div class="row"> 
        <div class="twelve columns box">
          <h6 class="subheader">Find a server</h6>
          <div class="row collapse">
            <div class="eight mobile-three columns">
              <input type="text" />
            </div>
            <div class="four mobile-one columns">
              <a class="button expand postfix">Search</a>
            </div>
          </div>
          <h6 class="subheader">Find a player</h6>
          <div class="row collapse">
            <div class="eight mobile-three columns">
              <input type="text" />
            </div>
            <div class="four mobile-one columns">
              <a class="button expand postfix">Search</a>
            </div>
          </div>
          <h6 class="subheader">Server Categories</h6>
          <div class="row collapse tags">
		   <a href="/" class="button tiny secondary" >Top Ranked</a>
	<a href="/category/new" class="button tiny secondary" >New</a>
	<a href="/category/reliable" class="button tiny secondary" >Uptime</a>
	<a href="/category/active" class="button tiny secondary" >Activity</a>
	
  <?php $vs = array_reverse($database->query("SELECT version FROM versions ORDER BY time DESC, percent DESC LIMIT 5")); 
  foreach($vs as $vb){
	echo '<a href="/version/'.$vb['version'].'" class="button tiny secondary">'.$vb['version'].'</a>';
  }
  ?>
  <?php $vs = array_reverse($database->query("SELECT category FROM servers WHERE category != '' GROUP BY category ORDER BY COUNT(category) DESC LIMIT 5")); 
  foreach($vs as $vb){
	echo '<a href="/category/'.$vb['category'].'" class="button tiny secondary" >'.$vb['category'].'</a>';
  }
  ?>
          </div>
        
          
          <div class="adsection hide-for-small">
            <div class="row collapse ad">
             <a href="#"><img src="http://placehold.it/200x125"/></a>
            </div>
            <div class="row collapse ad">
            <a href="#"><img src="http://placehold.it/200x125"/></a>
            </div>
          </div>
        </div>
      </div>
    </div>
	<div class="nine columns content">
      <div class="row">
       <div class="twelve columns">
          <nav class="top-bar">
            <ul>
              <li class="toggle-topbar"><a href="#"></a></li>
            </ul>

            <section>
              <!-- Left Nav Section -->
              <ul class="left">
                <li>
                  <a class="active" href="#">get more players</a>
                </li>
                <li>
                  <a href="#">downtime alerts</a>
                </li>
                <li>
                  <a href="#">stats</a>
                </li>
                <li>
                  <a href="#">hosting reviews</a>
                </li>
              </ul>

              <!-- Right Nav Section -->
              <ul class="right">
                <li>
                  <a href="#">login</a>
                </li>
              </ul>
            </section>
          </nav>
       </div>
      </div>