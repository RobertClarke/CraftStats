<?php
   
   class Log{
     
    public $send;
    private $actions;
    private $classtotal = 0;
    private $output;
    private $classes = array();
    private $final;
	private $title;
	private $ecount;
	private $lstart;
	private $timers = array();
    
    public function __construct($send = 0){
    
      $this->send = $send;
        
      $this->add(' <b>MillerDebugInfo</b> #', 'output');
      $this->add(' Generated on '.date("l jS \of F Y h:i:s A").' #', 'output');  
	  
	  $this->title = 'MinecraftServers.com debug info';
		$time = microtime(); 
	  $time = explode(" ", $time); 
	  $time = $time[1] + $time[0]; 
	  $this->lstart = $time; 
    }
	
	public function clearErrors(){
	
	  $this->send = 0;
	  $this->title = 'MinecraftServers.com debug info';
	  $this->ecount = 0;
	  	
	}
    
    private function add($msg,$target = 'actions',$classname = ''){
      
      if($target == 'class' && is_array($this->classes[$classname])){
        array_push($this->classes[$classname],'# '.$msg." <br/>");
      }else{
        $this->$target .= '# '.$msg." <br/>";  
      }
      
    }
	
     private function error($msg,$target = 'actions',$classname = ''){
      
	  $this->ecount++;
	  $this->title = 'MinecraftServers.com debug info: '.$this->ecount.' errors!';
	  $this->send = 1;
	  
	  if($this->ecount == 1){
	  	echo '<h3 style="color:red !important;margin:0px 0px;">There has been an error processing this page.</h3>';
	  }
	  
      if($target == 'class'){
        array_push($this->classes[$classname],'### <span style="color:red;"><b>'.$msg."</b></span> <br/>");
      }else{
        $this->$target .= '### <span style="color:red;"><b>'.$msg."</b></span> <br/>";  
      }
      
    }
    
    public function log($type,$action,$info = '',$name = ''){
       
      switch($type){
                 
        case 'class':
        
          switch($action){
          
            case 'load':
              $this->classtotal++;;
              $this->classes[$name] = array();
              $this->add('\''.$name.'\' class loaded','class',$name);
            break;
            case 'error':
              $this->error('Error: '.$info,'class',$name);
            break;
			case 'action':
			  $this->add('Performed action: '.$info,'class',$name);
			break;
			case 'info':
			  $this->add('Info: '.$info, 'class',$name);
			break;
            
          }
        
        break;  
             
        case 'mysql':
        
          switch($action){
          
            case 'query':
              $this->add('MySQL query performed: '.$info, 'class', $name);
            break;
            
            case 'error':
              $this->error('MySQL error! '.$info,'class',$name);
            break;
              
            case 'connectdb':
              $this->add('Connected to \''.$info.'\' database','class',$name);
            break;
			
			case 'connectserver':
			  $this->add('Connected to \''.$info.'\'','class',$name);
            break;  
			  
          }
          
        break;
        
        case 'generic':
        
          switch($action){
          
            case 'error':
              $this->error('Error! '.$info);
            break;
            
            case 'action':
              $this->add('Action performed: '.$info);
            break;
              
            case 'info':
              $this->add('Info: '.$info);
            break;
			
			case 'phperror':
			  $this->error('PHP runtime error: '.$info);
            break;
          }
          
        break;
          
        case 'template':
          
          switch($action){
          
            case 'load':
              $this->add('Loaded template file \''.$info.'\'','class',$name);
            break;
              
            case 'error':
              $this->error('Failed to load template file: \''.$info.'\'', 'class',$name);
            break;
              
            case 'title':
              $this->add('Set page title to \''.$info.'\'','class',$name);
            break;
			
			case 'onload':
			  $this->add('Set onload to '.$info,'class',$name);
            break;
			
			case 'headscripts':
			  $this->add('Set extra header scripts to '.$info,'class',$name);
            break;
              
          }
          
        break;
               
      }
    
    }
    
    public function __destruct(){
      
      if($this->send == 1){
	  
		$time = microtime(); 
		$time = explode(" ", $time); 
		$time = $time[1] + $time[0]; 
		$finish = $time; 
		$totaltime = ($finish - $this->lstart); 
      
        $this->add($this->classtotal.' classes loaded.', 'output');
      
        $this->add($this->output,'final');
		
		$this->add('####<b> Request info </b>','final');
        $this->add('Server Address: '.$_SERVER['SERVER_ADDR'],'final');
		$this->add('Client Address: '.$_SERVER['REMOTE_ADDR'],'final');
		$this->add('Script Name: '.$_SERVER['SCRIPT_NAME'],'final');
		$this->add('Requested URL: '.$_SERVER['REQUEST_URI'],'final');
		$this->add('User Agent: '.$_SERVER['HTTP_USER_AGENT'].' <br/>','final');
		$this->add('Excecution Time: '.round($totaltime,6),'final');
		$this->add('','final');
		
		$qcount = 0;
		
        foreach($this->classes as $k => $v){
          
          $this->add('<h3 style="margin-bottom:5px !important;"> Class info for \''.$k.'\'</h3>','final');
          
          foreach($v as $v2){
            
            $this->add($v2,'final');  
			if(stristr($v2,'query')){
				$qcount++;
			}
            
          }
		  
		  if($k == 'db'){
		   $this->add($qcount.' total queries performed','final');  
		  }
		  
          
        }
        
        $this->add('####<b> Other:</b> ','final');
        if($this->actions){
          $this->add($this->actions,'final');
        }else{
          $this->add('None.','final');
        }
		
		if(!stristr($_SERVER['PHP_SELF'], 'announce')){
        	mail('alexanderm781@gmail.com',$this->title, wordwrap($this->final), "Content-type: text/html; charset=iso-8859-1");
		}
      }
      
    }
	
	
	public function timer($name) {
		if($this->timers[$name] == 0){ 
			$this->timers[$name] = microtime(true);
		}else{
			$total = (string)(microtime(true)-$this->timers[$name]);
			$this->timers[$name] = 0;
			if($name != 'query')$this->log('generic','info','Timed \''.$name.'\', total '.round($total,5).'s');
			return $total;
		}
	}

      
     
  }
   
   ?>