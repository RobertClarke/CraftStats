</div>
<div id="footer">
<div style="float:left;">
Powered by <a href="http://twitter.com/redream_">@redream_</a> and <a href="http://twitter.com/robertjfclarke">@RobertJFClarke</a>

<br/>
<span style="color:#888;">hosted by <a href="http://rjfc.net/vpshosting" style="color:#666;">RamNode</a> & <a href="http://roberthost.com" style="color:#666;">RobertHost</a></span><br/>
<span style="color:#888;">inspired by <a href="http://mcservers.org" style="color:#666;">mcservers.org</a></span><br/>
<br/>
<a href="https://twitter.com/craftstats_" class="twitter-follow-button" data-show-count="true" data-lang="en">Follow @craftstats_</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</div>

<div style="float:right;position:relative;bottom:16px;">
<?php $sponsored = $database->query("SELECT ip FROM servers WHERE sponsorTime > UNIX_TIMESTAMP() ORDER BY RAND(NOW()) ASC LIMIT 1",db::GET_ROW);

echo '<a href="/server/'.$sponsored['ip'].'"><img class="banner" style="border-radius:3px;" src="/banner/'.$sponsored['ip'].'/'.rand(1,10).'"/></a><br/>'; ?>
<!-- this has absolutely nothing to do with bundlebyte or simplenode. Just to prove my point, here's a velociraptor:

                                       O_
                                      /  >
                                      -  >   ^\
                                     /   >  ^ /   
                                    (O)  > ^ /   / / /  
       _____                        |       |    \\|//
      /  __ \                      _/      /     / _/
     /  /  | |                    /       /     / /
   _/  |___/ /                  _/      ------_/ / 
 ==_|  \____/                 _/       /  ______/
     \   \                 __/           |\
      |   \_          ____/              / \      _                    
       \    \________/                  |\  \----/_V
        \_         rawr craftstats.com  / \_______ V
          \__                /       \ /          V
             \               \        \
              \______         \_       \
                     \__________\_      \ 
                        /    /    \_    | 
                       |   _/       \   |
                      /  _/          \  |
                     |  /            |  |
                     \  \__          |   \__
                     /\____=\       /\_____=\ -->
</div>
<script type="text/javascript">
  var uvOptions = {};
  (function() {
    var uv = document.createElement('script'); uv.type = 'text/javascript'; uv.async = true;
    uv.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'widget.uservoice.com/V2G9EmZgVc1orOgkMcNcg.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(uv, s);
  })();
</script>

</body>
</html>

<!--
            _               _                         _                   
           | |             | |                       | |                  
  _ __ ___ | |__   ___ _ __| |_  __      ____ _ ___  | |__   ___ _ __ ___ 
 | '__/ _ \| '_ \ / _ \ '__| __| \ \ /\ / / _` / __| | '_ \ / _ \ '__/ _ \
 | | | (_) | |_) |  __/ |  | |_   \ V  V / (_| \__ \ | | | |  __/ | |  __/
 |_|  \___/|_.__/ \___|_|   \__|   \_/\_/ \__,_|___/ |_| |_|\___|_|  \___|
-->