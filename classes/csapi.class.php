<?php
class csAPI{
	public $database;
	public $ping;
	public $log;
	public $threads = 16;
	
	public $maxvotes;
	public $mct = array();

	public function __construct($db,$log){
		$this->database = $db;
		$this->log = $log;
		$mv = $this->database->query("SELECT MAX(votes) AS mv FROM servers WHERE game ='minecraft'",db::GET_ROW);
		$this->maxvotes = $mv['mv'];
		$translate = $this->database->query("SELECT * FROM mctranslation");
		foreach($translate as $t){
			$this->mct[$t['minecraft']] = $t['translated'];
		}
	}
	
	public function getUpdateList($thread,$maxthreads,$ip,$pass){
		if($pass != md5('salsa'.$ip)){$this->endCall($this->formatResponse('error','ahhh go away you hacker'));return false;}
		
		$slave = $this->database->query("SELECT id FROM slaves WHERE ip = '$ip'",db::GET_ROW);
		if($this->database->num_rows == 0){
			$this->database->query("INSERT INTO slaves VALUES('','$ip')");
			$slave = $this->database->query("SELECT id FROM slaves WHERE ip = '$ip'",db::GET_ROW);
		}
		
		$time = time();
		$execinterval = 120;
		$prv = $this->database->query("SELECT avgTime FROM serviceinfo ORDER BY time DESC LIMIT 1",db::GET_ROW);
		$maxupdates = round(($execinterval)/(1.3));
		// (update interval) / (estimated update time per server) = # servers that should be processed
		$sr = $this->database->query("SELECT  s2.*, 
        (
        SELECT  COUNT(*)
        FROM    slaves s1
        WHERE   (s1.id, s1.id) >= (s2.id, s2.id)
        ) AS rank
		FROM    slaves s2
		WHERE   id = {$slave[id]}",db::GET_ROW);
		
		$thread = $thread+(($sr['rank']-1)*$this->threads);
		
		$this->database->query("SELECT * FROM slaves",db::GET_ROW);
		$totalthreads = $this->database->num_rows*$this->threads;
		
		$time = time();
		$rand = rand(1,9998);
		$this->database->query("
		UPDATE servers SET updatingBy='$thread-$time-$rand'
		WHERE ID IN (
			SELECT ID FROM (
				SELECT ID FROM servers 
				WHERE (($time > lastUpdate AND updatingBy = '0') OR ($time - lastUpdate > 1200 AND updatingBy != '0') ) AND id % $totalthreads = $thread
				ORDER BY lastUpdate ASC 
				LIMIT 0,$maxupdates
			) tmp
		);
		");
		$return = $this->database->query("SELECT ip,version,advCheck,game FROM servers WHERE updatingBy='$thread-$time-$rand' GROUP BY ip");
		return json_encode($return); 
	}
	
	public function batchProcess(){
		$this->database->query("SELECT * FROM batchqueue WHERE processing = '1'");
		
		if($this->database->num_rows >= 16)return false;
		
		$results = $this->database->query("SELECT * FROM batchqueue WHERE processing = '0' ORDER BY time ASC LIMIT 0,1",db::GET_ROW);
		
		if($this->database->num_rows == 0){
			return false;
		}
		$this->database->query("UPDATE batchqueue SET processing = '1' WHERE pings = '{$results[pings]}'");
		
		
		
		$time = time();
		$results1 = json_decode(base64_decode($results['pings']),true);
		$updsize = count($results1);
		$i = 0;
		foreach($results1 as $result){
			$i++;
			$this->log->timer('upd1');
			$this->updateServerFromPing($result);
			
			$timer += $this->log->timer('upd1');
			echo $i.'/'.$updsize.', avgtime: '.($timer/$i);
			usleep(200000);
		}
		$avgtime = ($timer/$i);
		
		$time = time();
		$otot = $this->database->query("SELECT SUM(connplayers) AS cp FROM servers",db::GET_ROW);
		$oam = $this->database->query("SELECT SUM(connplayers) AS cp FROM servers WHERE continent = 'NA' OR continent = 'SA'",db::GET_ROW);
		$oas = $this->database->query("SELECT SUM(connplayers) AS cp FROM servers WHERE continent = 'AS'",db::GET_ROW);
		$ooc = $this->database->query("SELECT SUM(connplayers) AS cp FROM servers WHERE continent = 'OC'",db::GET_ROW);
		$oeu = $this->database->query("SELECT SUM(connplayers) AS cp FROM servers WHERE continent = 'EU'",db::GET_ROW);
		$this->database->query("INSERT INTO serviceinfo VALUES ('$time','{$results[slaveID]}','{$results[slaveThread]}','{$avgtime}','{$otot[cp]}:{$oam[cp]}:{$oas[cp]}:{$ooc[cp]}:{$oeu[cp]}')");
		$this->updateRanks();
		$this->database->query("DELETE FROM batchqueue WHERE pings = '{$results[pings]}'");
	}
	
	public function storeBatch($ip,$pass,$avg,$thread,$results){
		if($pass != md5('salsa'.$ip)){$this->endCall($this->formatResponse('error','ahhh go away you hacker'));return false;}
		$slave = $this->database->query("SELECT id FROM slaves WHERE ip = '$ip'",db::GET_ROW);
		
		if($this->database->num_rows == 0){
			$this->endCall($this->formatResponse('error','ahhh go away you hacker (I don\'t know who you are)'));return false;
		}
		
		if($results == 'W10=')return false;
		$time = time();
		$this->database->query("INSERT INTO batchqueue VALUES ('$time','$results','$avg','{$slave[id]}','$thread','0')");
	}
	
	public function advCheck($ip){
		$this->database->query("UPDATE servers SET advCheck = 1 WHERE (resolved = '$ip' AND resolved != '') OR ip = '$ip'");
	}
	
	public function registerCSPlus($ip){
		$this->trackServer($ip,true);
		$this->database->query("UPDATE servers SET advCheck = 2 WHERE (resolved = '$ip' AND resolved != '') OR ip = '$ip'");
		echo '1.2';
	}
	
	public function QueryMinecraft( $IP, $Port = 25565, $Timeout = 1 )
	{	
		$Socket = Socket_Create( AF_INET, SOCK_STREAM, SOL_TCP );
	
		socket_set_block($Socket);
		Socket_Set_Option( $Socket, SOL_SOCKET, SO_SNDTIMEO, array( 'sec' => (int)$Timeout, 'usec' => 0 ) );
		socket_set_option($Socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' =>  (int)$Timeout, 'usec' => 0));

		if( $Socket === FALSE || @Socket_Connect( $Socket, $IP, (int)$Port ) === FALSE )
		{
			return FALSE;
		}

		Socket_Send( $Socket, "\xFE\x01", 2, 0 );
		$Len = Socket_Recv( $Socket, $Data, 256, 0 );
		Socket_Close( $Socket );

		if( $Len < 4 || $Data[ 0 ] !== "\xFF" )
		{
			return FALSE;
		}

		$Data = SubStr( $Data, 3 ); // Strip packet header (kick message packet and short length)
		$Data = iconv( 'UTF-16BE', 'UTF-8', $Data );

		// Are we dealing with Minecraft 1.4+ server?
		if( $Data[ 1 ] === "\xA7" && $Data[ 2 ] === "\x31" )
		{
			$Data = Explode( "\x00", $Data );

			return Array(
				'HostName'   => $Data[ 3 ],
				'Players'    => IntVal( $Data[ 4 ] ),
				'MaxPlayers' => IntVal( $Data[ 5 ] ),
				'Protocol'   => IntVal( $Data[ 1 ] ),
				'Version'    => $Data[ 2 ]
			);
		}

		$Data = Explode( "\xA7", $Data );

		return Array(
			'HostName'   => SubStr( $Data[ 0 ], 0, -1 ),
			'Players'    => isset( $Data[ 1 ] ) ? IntVal( $Data[ 1 ] ) : 0,
			'MaxPlayers' => isset( $Data[ 2 ] ) ? IntVal( $Data[ 2 ] ) : 0,
			'Protocol'   => 0,
			'Version'    => '1.3'
		);
	}
	
	public function getUpdateStats($since){
		if($since == 0){$this->endCall($this->formatResponse('error','missing arguments'));return false;}
		$time = time();
		if($time - $since  > 60){
			$this->endCall($this->formatResponse('error','data older than 60s not allowed to be sent'));
			return false;
		}
		
		$final = array();
		
		$slaves = $this->database->query("SELECT  s2.*, 
			(
			SELECT  COUNT(*)
			FROM    slaves s1
			WHERE   (s1.id, s1.id) >= (s2.id, s2.id)
			) AS rank
			FROM    slaves s2");
		$totalthreads = $this->database->num_rows*$this->threads;
		$i = 0;
		foreach($slaves as $slave){
			$thread = (($slave['rank']-1)*$this->threads);
	
			$svcount = $this->database->query("SELECT COUNT(*) AS svcount FROM servers WHERE $time - lastUpdate > 60 AND id % $totalthreads >= $thread AND id % $totalthreads <= $thread + {$this->threads} - 1 ",db::GET_ROW);
			$info = $this->database->query("SELECT COUNT(*) as c FROM servers WHERE game ='minecraft'",db::GET_ROW);
			$tc = $info['c'];
			$servers = round($info['c']/count($slaves));
			array_push($final,array($time,(int)$svcount['svcount'],(int)$servers,0,$i));
			
			$i++;
		}
		$time = time();
		$svcount = $this->database->query("SELECT COUNT(*) AS svcount FROM servers WHERE $time - lastUpdate < 180 AND game='minecraft'",db::GET_ROW);
		$rate = round($svcount['svcount']/3);
		
		$svcount = $this->database->query("SELECT COUNT(*) AS svcount FROM servers WHERE updatingBy != 0",db::GET_ROW);
		$updating = $svcount['svcount'];
		
		$svcount = $this->database->query("SELECT COUNT(*) AS svcount FROM servers WHERE $time - lastUpdate > 600 AND $time - lastUpdate < 60000 ",db::GET_ROW);
		$waiting = $svcount['svcount'];
		
		$updated = $this->database->query("SELECT ip FROM servers WHERE lastUpdate > $since AND game='minecraft' LIMIT 0,50");
		$every = $tc/$rate;
		$this->endCall($this->formatResponse('success','transmitting data',array($final,$rate,$time,$updated,0,round($every),$updating,$waiting)));
	}
	
	public function updateRanks(){
		$this->database->query("UPDATE   servers
		JOIN     (SELECT    s.ID,
                    @curRank := @curRank + 1 AS rank
          FROM      servers s
          JOIN      (SELECT @curRank := 0) r
          ORDER BY  s.score DESC
         ) ranks ON (ranks.ID = servers.ID)
		SET      servers.ranking = ranks.rank;");
	}
	
	public function trackServer($ip,$quiet = false,$info = false){
		//$this->endCall($this->formatResponse('error','no new servers are currently being accepted'));
		//return false;
		$ip = strtolower($ip);
		$ip = str_replace('http://','',$ip); 
		$ip = str_replace('www.','',$ip); 
		$lookupip = $this->validateIP($ip);
		
		$bannedIPs = array('localhost','127.0.0.1');

		$blacklistedIPs = $this->database->query("SELECT ip,resolved FROM servers WHERE blacklisted = '1'");
		foreach($blacklistedIPs as $blacklistedIP){
			array_push($bannedIPs, $blacklistedIP[resolved], $blacklistedIP[ip]);
		}
		
		if(in_array($ip,$bannedIPs)){
			return $this->formatResponse('error','The IP you entered is blacklisted.');
		}
		
		if($lookupip != false){
			$server = $this->database->query("SELECT * FROM servers WHERE resolved = '$lookupip' AND resolved != ''",db::GET_ROW);

			if($this->database->num_rows != 0){
				if($info){
					unset($server['votifierKey']);
					unset($server['plCache']);
					unset($server['advCheck']);
					echo json_encode($server);
				}
				return $this->formatResponse('success','Server found, redirecting',$server['ip']);
			}else{
				$result = $this->pingServer($ip,1);
				
				if($result['fail'] == true){
					return $this->formatResponse('error','Failed to connect to Minecraft Server');
				}
		
				$this->database->query("INSERT INTO servers VALUES ('','minecraft','$ip','$lookupip','',0,0,0,'US','NA',0,0,'','','','','','',0,0,'',0,0,0,0,100,'',0,0,FALSE,0,'0','0')");
				
				$this->updateServerFromPing($result);
				
				$this->updateRanks();
				
				$sid = mysql_insert_id();
				if($info){
					$server = $this->database->query("SELECT * FROM servers WHERE resolved = '$lookupip' AND resolved != ''",db::GET_ROW);
					unset($server['votifierKey']);
					unset($server['plCache']);
					unset($server['advCheck']);
					echo json_encode($server);
				}
				return $this->formatResponse('success','Server added, redirecting',$ip);
			}
		}else{
			return $this->formatResponse('error','Invalid Server Address');
		}
	}
	
	public function addvote($sid,$user){
		if(!isset($_SESSION['id'])){
			return $this->formatResponse('error','User not logged in');
		}
		$time = time();
		$max = $time - 86400;
		$this->database->query("SELECT * FROM uservotes WHERE userID = '$_SESSION[id]' AND serverID = '$sid' AND time > '$max'");
		if($this->database->num_rows == 0){
			$info = $this->database->query("SELECT votifierIP, votifierPort, votifierKey,ip FROM servers WHERE ID = '$sid'",db::GET_ROW);
			if($info['votifierIP'] != ''){
				if(!isset($_SESSION['mcuser'])){
					$this->database->query("UPDATE users SET mcuser = '$user' WHERE id = '$_SESSION[id]'");
				}
				file_get_contents('http://192.119.145.28/api.php?a=2&ip='.$info['votifierIP'].'&user='.$user.'&port='.$info['votifierPort'].'&key='.base64_encode($info['votifierKey']));
			}
			$this->database->query("INSERT INTO uservotes VALUES('$_SESSION[id]','$sid','$time')");
			return $this->formatResponse('success','vote success',$info['ip']);
		}else{
			return $this->formatResponse('error','Already voted');
		}
	}
	
	public function updateServerFromPing($ping){
		if($ping == false){
			return false;
		}
		$this->log->log('generic','action',print_r($ping,true));
		$this->log->timer('update');
		
		$server = $this->database->query("SELECT ID, lastUpdate AS utime, uptime AS uuptime, votes,uptimeavg, score FROM servers WHERE ip = '$ping[ip]'",db::GET_ROW);
		if($this->database->num_rows == 0)return false;
		//if($server[ID] == '2913')$this->log->send = 1;
		$uptime = ($ping['fail'] == '1' ? ($server['uuptime'] > 0 ? 0 : $server['uuptime'] - time() + $server['utime'] ) : ($server['uuptime'] <= 0 ? 1 : $server['uuptime'] + time() - $server['utime']));
		
		$iping = ($ping['fail'] == '1' ? 0 : $ping['ms']);
		$time = time();
		
		$finaluptime = 0;
		
		
		$uptimesince = $time - 1209600;
		
		if(rand(1,40) == 10){
			$dpoints = $this->database->query("SELECT * FROM updates WHERE serverID = '$server[ID]' AND time > $uptimesince ORDER BY time ASC");
			$amt = 0;
			$amt2 = 0;
			$amt3;
			$count = 1;
			
			$playerpenalty = 1;
			
			$maxdiff = 0;
			for($i = 0;$i < $this->database->num_rows;$i++){
				$amt += ($dpoints[$i]['ping'] == 0 ? 0 : 1);
				$amt2 += (($dpoints[$i]['ping'] == 0 || $dpoints[$i]['maxPlayers'] <= 1) ? 0 : ($dpoints[$i]['connPlayers'] / $dpoints[$i]['maxPlayers']));
				/*if($i>2){
					$mdo = $maxdiff;
					$maxdiff = max($maxdiff,abs($dpoints[$i]['connPlayers'] - $dpoints[$i-1]['connPlayers']));
					if($maxdiff >= $dpoints[$i]['maxPlayers']/2){
						$maxdiff = $mdo;
					}
				}*/
				$count++;
			}
			
			//$maxplayers = $dpoints[$this->database->num_rows-1]['maxPlayers'];
			$finaluptime = round(($amt / $count)*100,2);
			/*
			$avgplayers = min(($playerpenalty == 0.1 ? 1 : (0.4+(round($amt2 / $count)*0.6))),1);
			$datafullness = min(max((($time - $dpoints[0]['time'])/2678400),1),0.2);
			
			$votes = $server['votes'];
			$serverscore = $avgplayers*$finaluptime*$datafullness*1500*(0.2+(($votes/$this->maxvotes)*0.8))*$playerpenalty;
			if($maxdiff <= 2)$serverscore = 0;
			$this->log->log('generic','action',"id: $server[ID] amt3: $amt3 md: $maxdiff count: $count pt: $pt p: $avgplayers ping: $avgping df: $datafullness uptime: $finaluptime");*/
		}else{
			//$serverscore = $server['score'];
			$finaluptime = $server['uptimeavg'];
		}
		
		$serverscore = $server['votes'];
		
		
		//no haxx plz
		
		$ping['info']['Players'] = mysql_real_escape_string($ping['info']['Players']);
		$ping['info']['MaxPlayers'] = mysql_real_escape_string($ping['info']['MaxPlayers']);
		$ping['info']['HostName'] = mysql_real_escape_string($ping['info']['HostName']);
		$ping['info']['Version'] = mysql_real_escape_string($ping['info']['Version']);
		
		if($ping['resolvedip'] != ''){
			$this->log->timer('ccup');
			if(rand(1,200) == 4){
				$location = $this->getLocationIP($ping['resolvedip']);
				$lat = $location[2];
				$long = $location[3];
				$continent = $location[1];
				$country = $location[0];
				$this->log->timer('ccup');
			
				$cupd="country = '$country',continent = '$continent',latitude = '$lat',longitude = '$long',";
			}
			$parts = explode(':',$ping['ip']);	
			if($parts[1] == '25565'){
				$ping['ip'] = $parts[0];
			}
		}
		// add new info for latest server ping
		$this->database->query("INSERT INTO updates VALUES ('$server[ID]','$iping','$time', '$uptime', '{$ping[info][Players]}', '{$ping[info][MaxPlayers]}')");
		
		
		$advc = 'advCheck = FALSE,';
		$finaluptime = max($finaluptime,0);
		if($finaluptime == '')$finaluptime = 0;
		if($ping['fail'] != '1'){ //only updates the info it knows (if the server is down / mc info can't be found)
			
			if(($ping['info']['Plugins']))$advc= 'advCheck = 1,';
			if(rand(1,80) == 4)$advc= 'advCheck = 2,';
			if($ping['plusinfo'] != ''){$advc= 'advCheck = 2,';}
			$motd = preg_replace('/\xA7[0-9A-FK-OR]+/i', '', $ping[info][HostName]);
			if(count($ping['players'])>0)$plcache = mysql_real_escape_string(implode('||',array_slice($ping['players'],0,30)));
			$this->database->query("UPDATE servers SET plCache = '$plcache',updatingBy = '0',ip = '$ping[ip]', resolved = '$ping[lookupip]',$cupd motd = '$motd', version = '{$ping[info][Version]}', uptime = $uptime,  uptimeavg = $finaluptime,lastUpdate = '$time', connPlayers = '{$ping[info][Players]}', maxPlayers = '{$ping[info][MaxPlayers]}',$advc score = $serverscore WHERE id = '$server[ID]'");
		}else{
			$this->database->query("UPDATE servers SET plCache = '',updatingBy = '0',resolved = '$ping[lookupip]',$cupd uptime = '$uptime', lastUpdate = '$time',uptimeavg = $finaluptime, connPlayers = '0',score = $serverscore WHERE id = '$server[ID]'");
			//$this->endCall($this->formatResponse('error','Failed to connect to Minecraft Server'));
			$this->log->timer('update');
			return false;
		}
		$first = '';
		$ptime = floor(time()/86400);
		if(isset($ping['plusinfo'])){
			foreach($ping['plusinfo'] as $event => $players){
				foreach($players as $player => $objects){
					foreach($objects as $object => $amount){
						$trans = $this->mct[$object];
						$evq .= $first."('$ptime','$server[ID]','$player','$event','$object','$trans','$amount')";
						$first = ',';
					}
				}
			}
			if($evq != '')$this->database->query("INSERT INTO playerevent VALUES $evq ON DUPLICATE KEY UPDATE amount = amount + VALUES(amount)");
		}
		
		if(!isset($ping['info']['Plugins'])){
			return false;
		}
		
		$this->log->timer('players');
		/*
		if(count($ping['players']) > 0 && is_array($ping['players'])){
		
			$qstr = '';
			$first2 = true;
			foreach($ping['players'] as $player){
				if(!$first2){
					$qstr .= 'OR ';
				}
				$qstr .= 'username = \''.mysql_real_escape_string($player).'\' ';
				$first2 = false;
			}
		
			$dbpl = $this->database->query("SELECT username, ID FROM players WHERE $qstr"); // list of players found on server and in DB
		
			if(count($dbpl) > 0 && is_array($dbpl)){
				$qstr = '';
				$first2 = true;
				foreach($dbpl as $row){
					if(!$first2){
						$qstr .= 'OR ';
					}
					$qstr .= 'playerID = \''.$row['ID'].'\' ';
					$first2 = false;
				}
			
				$dbpls = $this->database->query("SELECT playerID FROM serverplayers WHERE serverID = '$server[ID]' AND ($qstr)");
			}
		
			$dps1 = array();
			$dps2 = array();
			$dpsc4 = array();
			$dps4 = array();
		
			foreach($dbpl as $p){
				$dps1[$p['ID']] = $p['username'];
				$dpsc4[$p['username']] = $p['ID'];
				// convert into a comparable format (id => username of players on server and in database, only values and not keys are compared in array_intersect, so if we put the unknown value for $ping[players] (ID) in the key, we can find the ID of matched players)
			}
			foreach($dbpls as $p){
				array_push($dps2,$p['playerID']);
				array_push($dps4,$p['playerID']);
				//DPS2: players that are currently on the server, already in the database, and have been found on this server before
			}
			
			$dps3 = array_diff($ping['players'],$dps1);
			$this->log->log('generic','action',print_r($dps3,true));
			//DPS3: players that are currently on the server, but not in the database
			
			$dps4 = array_diff($dpsc4,$dps4);
			//DPS4: players in the database, haven't been seen on server before
		
		
			$toAdd = '';
			$toAdd2 = '';
			$spvalues = '';
			$firstfound = true;
			$firstfound2 = true;
			$first = true;
			
			foreach($dps1 as $id => $user){ // formatting query to set current server of players
				if($firstfound == false){
					$toAdd .= ' OR ';
				}
					$toAdd .= 'ID = \''.$id.'\'';
				$firstfound = false;
			}
				
			foreach($dps2 as $id){ // formatting query to add 1 to # times player has been found on the server
				if($firstfound2 == false){
					$toAdd2 .= ' OR ';
				}			
				$toAdd2 .= 'playerID = '.$id;
				$firstfound2 = false;
			}
		
			foreach($dps3 as $user){ // inserting new players and formatting query to create server-player relationships
				$user = mysql_real_escape_string($user);
				$this->database->query("INSERT INTO players VALUES ('','$user','$server[ID]',0)");
				if($first == false){
					$spvalues .= ' ,';
				}
				$spvalues .= '('.mysql_insert_id().',\''.$server['ID'].'\',\'1\',\'0\')';
				$first = false;
			}
			
			foreach($dps4 as $user){
				if($user != 0){
					if($first == false){
						$spvalues .= ' ,';
					}
					$spvalues .= '('.$user.',\''.$server['ID'].'\',\'1\',\'0\')';
					$first = false;
				}
			}
			
			//$this->database->query("UPDATE players SET currentServer = '0' WHERE currentServer = '$server[ID]'");
		
			//if($firstfound == false){ // setting current server of players
			//	$this->database->query("UPDATE players SET currentServer = '$server[ID]' WHERE $toAdd");
			//}
			
			if($firstfound2 == false){ // adding 1 to # times player has been found on this server
				$this->database->query("UPDATE serverplayers SET found = found + 1 WHERE serverID = '$server[ID]' AND ($toAdd2)");
			}
		
			if($spvalues != ''){ // creating server-player relationships
				$this->database->query("INSERT INTO serverplayers VALUES $spvalues");
			}
		
		}*/
		
		
		$this->log->timer('players');
		
		$this->log->timer('update');
	}
	
	private function getLocationIP($ip){
		$cc = $this->database->query("SELECT glc.country AS cc, glc.latitude AS lat, glc.longitude AS longit
		FROM geoip_blocks gbl 
		JOIN geoip_locations glc 
		ON glc.locid = gbl.gbl_glc_id 
		WHERE gbl_block_start <= INET_ATON('$ip')
		ORDER BY gbl_block_start DESC LIMIT 1",db::GET_ROW);
		$c2con = array(
	'AD' => 'EU','AE' => 'AS','AF' => 'AS','AG' => 'NA','AI' => 'NA','AL' => 'EU','AM' => 'AS','AN' => 'NA','AO' => 'AF','AP' => 'AS','AQ' => 'AN','AR' => 'SA','AS' => 'OC','AT' => 'EU','AU' => 'OC','AW' => 'NA','AX' => 'EU','AZ' => 'AS','BA' => 'EU','BB' => 'NA','BD' => 'AS','BE' => 'EU','BF' => 'AF','BG' => 'EU','BH' => 'AS','BI' => 'AF','BJ' => 'AF','BL' => 'NA','BM' => 'NA','BN' => 'AS','BO' => 'SA','BR' => 'SA','BS' => 'NA','BT' => 'AS','BV' => 'AN','BW' => 'AF','BY' => 'EU','BZ' => 'NA','CA' => 'NA','CC' => 'AS','CD' => 'AF','CF' => 'AF','CG' => 'AF','CH' => 'EU','CI' => 'AF','CK' => 'OC','CL' => 'SA','CM' => 'AF','CN' => 'AS','CO' => 'SA','CR' => 'NA','CU' => 'NA','CV' => 'AF','CX' => 'AS','CY' => 'AS','CZ' => 'EU','DE' => 'EU','DJ' => 'AF','DK' => 'EU','DM' => 'NA','DO' => 'NA','DZ' => 'AF','EC' => 'SA','EE' => 'EU','EG' => 'AF','EH' => 'AF','ER' => 'AF','ES' => 'EU','ET' => 'AF','EU' => 'EU','FI' => 'EU','FJ' => 'OC','FK' => 'SA','FM' => 'OC','FO' => 'EU','FR' => 'EU','FX' => 'EU','GA' => 'AF','GB' => 'EU','GD' => 'NA','GE' => 'AS','GF' => 'SA','GG' => 'EU','GH' => 'AF','GI' => 'EU','GL' => 'NA','GM' => 'AF','GN' => 'AF','GP' => 'NA','GQ' => 'AF','GR' => 'EU','GS' => 'AN','GT' => 'NA','GU' => 'OC','GW' => 'AF','GY' => 'SA','HK' => 'AS','HM' => 'AN','HN' => 'NA','HR' => 'EU','HT' => 'NA','HU' => 'EU','ID' => 'AS','IE' => 'EU','IL' => 'AS','IM' => 'EU','IN' => 'AS','IO' => 'AS','IQ' => 'AS','IR' => 'AS','IS' => 'EU','IT' => 'EU','JE' => 'EU','JM' => 'NA','JO' => 'AS','JP' => 'AS','KE' => 'AF','KG' => 'AS','KH' => 'AS','KI' => 'OC','KM' => 'AF','KN' => 'NA','KP' => 'AS','KR' => 'AS','KW' => 'AS','KY' => 'NA','KZ' => 'AS','LA' => 'AS','LB' => 'AS','LC' => 'NA','LI' => 'EU','LK' => 'AS','LR' => 'AF','LS' => 'AF','LT' => 'EU','LU' => 'EU','LV' => 'EU','LY' => 'AF','MA' => 'AF','MC' => 'EU','MD' => 'EU','ME' => 'EU','MF' => 'NA','MG' => 'AF','MH' => 'OC','MK' => 'EU','ML' => 'AF','MM' => 'AS','MN' => 'AS','MO' => 'AS','MP' => 'OC','MQ' => 'NA','MR' => 'AF','MS' => 'NA','MT' => 'EU','MU' => 'AF','MV' => 'AS','MW' => 'AF','MX' => 'NA','MY' => 'AS','MZ' => 'AF','NA' => 'AF','NC' => 'OC','NE' => 'AF','NF' => 'OC','NG' => 'AF','NI' => 'NA','NL' => 'EU','NO' => 'EU','NP' => 'AS','NR' => 'OC','NU' => 'OC','NZ' => 'OC','OM' => 'AS','PA' => 'NA','PE' => 'SA','PF' => 'OC','PG' => 'OC','PH' => 'AS','PK' => 'AS','PL' => 'EU','PM' => 'NA','PN' => 'OC','PR' => 'NA','PS' => 'AS','PT' => 'EU','PW' => 'OC','PY' => 'SA','QA' => 'AS','RE' => 'AF','RO' => 'EU','RS' => 'EU','RU' => 'EU','RW' => 'AF','SA' => 'AS','SB' => 'OC','SC' => 'AF','SD' => 'AF','SE' => 'EU','SG' => 'AS','SH' => 'AF','SI' => 'EU','SJ' => 'EU','SK' => 'EU','SL' => 'AF','SM' => 'EU','SN' => 'AF','SO' => 'AF','SR' => 'SA','ST' => 'AF','SV' => 'NA','SY' => 'AS','SZ' => 'AF','TC' => 'NA','TD' => 'AF','TF' => 'AN','TG' => 'AF','TH' => 'AS','TJ' => 'AS','TK' => 'OC','TL' => 'AS','TM' => 'AS','TN' => 'AF','TO' => 'OC','TR' => 'EU','TT' => 'NA','TV' => 'OC','TW' => 'AS','TZ' => 'AF','UA' => 'EU','UG' => 'AF','UM' => 'OC','US' => 'NA','UY' => 'SA','UZ' => 'AS','VA' => 'EU','VC' => 'NA','VE' => 'SA','VG' => 'NA','VI' => 'NA','VN' => 'AS','VU' => 'OC','WF' => 'OC','WS' => 'OC','YE' => 'AS','YT' => 'AF','ZA' => 'AF','ZM' => 'AF','ZW' => 'AF',
	);
	$ccode=(($cc['cc'] == '' || $cc['cc'] == 'A1' || $cc['cc'] == 'JE')?'US':$cc['cc']);
		return array($ccode,$c2con[$ccode],$cc['lat'],$cc['longit']);
	}
	
	public function updateServerFromIP($ip,$level){
		$this->log->timer('supdate');
		$ping = $this->pingServer($ip,$level);
		$this->updateServerFromPing($ping);
		return $ping;
		$this->log->timer('supdate');
	}
	
	public function validateIP($ip){
		$parts = explode(':',$ip);
		if(filter_var($ip, FILTER_VALIDATE_IP)){
			return $ip;
		}else{
			$addr = $this->getAddrByHost($parts[0]);
			$adrp = explode(':',$addr);
			if($adrp[1]>0){
				return $adrp[0].':'.$adrp[1];
			}
			return ($addr == false ? (filter_var($parts[0], FILTER_VALIDATE_IP) ? $parts[0].':'.$parts[1] : false) : $addr.($parts[1] != '' ? ':'.$parts[1]:''));	
		}
	}
	
	function isDomain($domain_name){
    
	return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) //valid chars check
            && preg_match("/^.{1,253}$/", $domain_name) //overall length check
            && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name)   ); //length of each label
	}
	
	private function getAddrByHost($host, $timeout = 1) {
		if($this->isDomain($host)){
			$query = `nslookup -timeout=$timeout -retry=1 $host`;
			if(preg_match('/\nAddress: (.*)\n/', $query, $matches))
				return trim($matches[1]);
				
			$query = `nslookup -type=SRV -timeout=$timeout -retry=1 _minecraft._tcp.$host`;
			if(preg_match('/service = (\d+) (\d+) (\d+) (.*)./', $query, $matches))
				return trim($matches[4].':'.$matches[3]);
		}
		return false;
	}
	
	public function serverStatus($ip){
		$server = $this->database->query("SELECT * FROM servers WHERE (resolved = '$ip' AND resolved != '') OR ip = '$ip'",db::GET_ROW);
		return array(
			'ip'=>$server['ip'],
			'resolved'=>$server['resolved'],
			'category'=>$server['category'],
			'motd'=>$server['motd'],
			'version'=>$server['version'],
			'uptime'=>$server['uptime'],
			'uptimeavg'=>$server['uptimeavg'],
			'ranking'=>$server['ranking'],
			'onlinePlayers'=>$server['connPlayers'],
			'maxPlayers'=>$server['maxPlayers'],
			'lastUpdate'=>$server['lastUpdate']
		);
	}
	
	public function pingServer($ip,$level){
	
		/*$fail = false;
		
		$lookupip = $this->validateIP($ip);
		
		if(!$lookupip){
			return array('ip'=>$ip,'fail'=>true);
		}
		
		$parts = explode(':',$ip);
		
		$result = array();
		
		if($level == 2){
			try{
				$PORT = 22565; //the port on which we are connecting to the "remote" machine
				$HOST = $parts[0]; //the ip of the remote machine

				$sock = socket_create(AF_INET, SOCK_STREAM, 0); //Creating a TCP socket
				$succ = socket_connect($sock, $HOST, $PORT); //Connecting to to server using that socket
				$text = "GoForIt"; //the text we want to send to the server

				socket_write($sock, $text . "\n", strlen($text) + 1); //Writing the text to the socket

				$reply = socket_read($sock, 1000, PHP_NORMAL_READ); //Reading the reply from socket
			  
				$results['plusinfo'] = json_decode($reply,true);
			}catch(Exception $e){
				$result['fail'] = true;
			}
		}
		
		if($level == 1 || $result['fail'] == true){
			$mcp = new MinecraftQuery();
		
			try{
				$this->log->timer('ping1');
				$mcp->connect($parts[0],($parts[1] != '' ? $parts[1]: 25565));
				$ping = $this->log->timer('ping1')*1000;
				$result['ms'] = round($ping);
				$result['fail'] = false;
			}catch(Exception $e){
				$result['fail'] = true;
				$this->log->timer('ping1');
			}
	
			
			$result['info'] = $mcp->getinfo();
			$result['players'] = $mcp->getplayers();
		}
		
		
		
		$result['ip'] = $ip;
		
		$parts2 = explode(':',$lookupip);
		$result['resolvedip'] = $parts2[0];
		$result['lookupip'] = $lookupip;
				
		if($level == 0 || $result['fail'] == true){
			$this->log->timer('ping2');
			$info = $this->QueryMinecraft($parts[0],($parts[1] != '' ? $parts[1]: 25565));
			
			if($info == false){
				$result['fail'] = true;
				$this->log->timer('ping2');
			}else{
				$result['info'] = $info;
				$result['fail'] = false;
				$ping = $this->log->timer('ping2')*1000;
				$result['ms'] = round($ping);
			}
		}
		
		if($result['fail'] == true){
			sleep(1);
			$this->log->timer('ping2');
			$info = $this->QueryMinecraft($parts[0],($parts[1] != '' ? $parts[1]: 25565));
			
			if($info == false){
				$result['fail'] = true;
				$this->log->timer('ping2');
			}else{
				$result['info'] = $info;
				$result['fail'] = false;
				$ping = $this->log->timer('ping2')*1000;
				$result['ms'] = round($ping);
			}
		}
		$result['info']['HostName'] = preg_replace('/\xA7[0-9A-FK-OR]/i', '', $result['info']['HostName']);
		$this->log->log('generic','action',print_r($result,true));
		
		return $result;*/
		return json_decode(file_get_contents('http://192.119.145.28/api.php?a=1&ip='.$ip.'&l='.$level),true);
	}
	
	private function formatResponse($status,$info,$extra = ''){
		$response = array('status'=>$status, 'info'=>$info,'extra'=>$extra);
		return $response;
	}
	
	private function endCall($response){
		$this->log->log('generic','action',print_r($response,true));
		echo json_encode($response);
	}
	
	public function getDirtBlocks(){
		$return = array();
		$events = array('Item Crafted'=>'#icrafted','Block Break'=>'#bbroken','Kill'=>'#mkilled','Bow Shot'=>'#ashot','Block Place'=>'#bplaced');
		foreach($events as $e => $v){
			$eq .= $ef." event = '$e' ";
			$ef = " OR ";
		}

		$es2 = $this->database->query("SELECT SUM(amount) AS amount, event FROM playerevent WHERE $eq GROUP BY event");

		foreach($es2 as $e){
			$return[$events[$e['event']]]['value'] = (float)$e['amount'];
		}
		return $return;
	}
}
?>