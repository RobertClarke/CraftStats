<?php

  class Template{
    
    public $templatename;
    public $title;
	public $desc;
    public $scripts;
    public $onload;
    public $meta;
    public $db;
    public $starttime;
    public $logger;
    public $lstart;
    public $onloadscript;
    public $headscripts;
    public $content;
    private $assets;
  
    public function __construct($db,$logger){

      $this->db = $db;
      $this->starttime = $timer;
      $this->logger = $logger;
	  
	  $time = microtime(); 
	  $time = explode(" ", $time); 
	  $time = $time[1] + $time[0]; 
	  $this->lstart = $time; 
	
	}
    
    public function setTitle($title){
		if(strlen($title) < 1){ 
			$this->title = 'CraftStats.com';  
		}else{
			$this->title = $title.' | CraftStats.com';  
		}
		$this->logger->log('template', 'title', $this->title, 'template');
      
    }
	
	public function setDesc($desc){
		if(strlen($desc) < 1){ 
			$this->desc = 'Find the best Minecraft servers with our minecraft server multiplayer list. Browse detailed information on each server and vote for your favourite server.';  
		}else{
			$this->desc = $desc;  
		}
    }
  
    public function setOnload($onload){
      
      $this->onloadscript = $onload;  
      $this->logger->log('template', 'onload', $this->onloadscript, 'template');  
    
    }
  
    public function setHeadScripts($head){
      
      $this->headscripts .= $head;  
      $this->logger->log('template', 'headscripts', $this->headscripts, 'template');  
    
    }
    
    public function show($name,$custvar = 0){
		if($this->title == ''){
			$this->setTitle('');
		}
		if($this->desc == ''){
			$this->setDesc('');
		}
		$namef = strtoupper($name);
		$this->modulecount++;
      $database = $this->db;
	  $user = $this->user;
	  
	  $time = microtime(); 
	  $time = explode(" ", $time); 
	  $time = $time[1] + $time[0]; 
	  $start = $time; 
	
      if(include '../templates/'.$name.'.php'){  
        $this->logger->log('template','load','../templates/'.$name.'.php','template');
      }else{
        $this->logger->log('template','error','../templates/'.$name.'.php','template');
      }
	  
	  $time = microtime(); 
	  $time = explode(" ", $time); 
	  $time = $time[1] + $time[0]; 
	  $finish = $time; 
	  $totaltime = ($finish - $start); 
	  $this->loadtime += $totaltime;
	  echo '
<!-- End '.$namef.' - Processed '.($totaltime < 0.0001 ? 'lightning fast': 'in '.round($totaltime,5).' seconds').' -->
';

		if($namef == 'FOOTER'){
			$time = microtime(); 
			  $time = explode(" ", $time); 
			  $time = $time[1] + $time[0]; 
			  $finish = $time; 
			  $totaltime = ($finish - $this->lstart); 
			echo '<!-- '.$this->modulecount.' modules loaded in '.round($totaltime,6).' seconds -->';
		}
    }
    
    
    
  }
  
?>