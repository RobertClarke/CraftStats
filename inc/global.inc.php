<?php session_start();
include '../classes/db.class.php';
include '../classes/template.class.php';
include '../classes/log.class.php';
include '../classes/csapi.class.php';

include '../lib/mcquery.php';

if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
	$_SERVER['REMOTE_ADDR']= $_SERVER["HTTP_CF_CONNECTING_IP"];
}

function displayTree($array) {
     $newline = "<br>";
     foreach($array as $key => $value) {    //cycle through each item in the array as key => value pairs
         if (is_array($value) || is_object($value)) {        //if the VALUE is an array, then
            //call it out as such, surround with brackets, and recursively call displayTree.
             $value = "Array()" . $newline . "(<ul>" . displayTree($value) . "</ul>)" . $newline;
         }
        //if value isn't an array, it must be a string. output its' key and value.
        $output .= "[$key] => " . $value . $newline;
     }
     return $output;
}
//$memcache_disable = true;
if(!$memcache_disable){
	$memcache = new Memcache;
	$memcache->connect('localhost', 11211) or die ("Could not connect");
}

$log = new Log();
$database = new db($memcache,$log);
$log->log('class','load','','db');
$api = new csAPI($database,$log);

if(!isset($_SESSION['id'])){
	$usr = $database->query("SELECT id FROM users WHERE ip = '$_SERVER[REMOTE_ADDR]'",db::GET_ROW);
	if($database->num_rows > 0){
		$_SESSION['id'] = $usr['id'];	
	}else{
		$time = time();
		$database->query("INSERT INTO users VALUES ('','$_SERVER[REMOTE_ADDR]','','','','','','','','$time')");
	}
}

$template = new Template($database,$log);

foreach($_GET as $k => $v){
	$_GET[$k] = mysql_real_escape_string($v); 
}
	  
foreach($_POST as $k => $v){
	$_POST[$k] = mysql_real_escape_string($v); 
}

?>