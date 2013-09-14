<?php

class Log
{
	
	private static $_instance;
	private $activated;
	private $flushActivated;
	private $printActivated;
	private $selectActivated;
	private $time;
	
	
   /**
    * Récupère l'instance de la classe
    *
    * @return SingletonClass
    */
	public static function getInstance()
	{
		if( true === is_null( self::$_instance ) )
		{
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
   
	
	private function __construct()
	{
		ob_start();
		$this->time = microtime(true);
	}
	
	
	
	private function t(){
		$time = microtime(true)-$this->time;
		return '<span style="display:inline-block;width:100px">'.round($time,1)." s ".chr(9)." -></span>";
	}
	
	// fonction <pre>print_r(...)</pre>
	public function pr($obj, $class=''){
		if($this->activated && $this->printActivated){
			echo "<pre class='".$class."'>";
			echo $this->t();
			print_r($obj);
			echo "<br></pre>";
		}
	}
	
	// fonction <pre>print_r(...)</pre>
	public function p($obj, $class=''){
		if($this->activated && $this->printActivated){
			echo "<div class='".$class."'>";
			echo $this->t();
			echo $obj."</div>";
		}
	}


	// fonction flush
	public function f($chaine, $class=''){

		if($this->activated && $this->flushActivated){
			$this->p($chaine, $class);
			ob_flush();
			flush();
		}
	}
	
	// fonction flush
	public function fr($chaine, $class=''){
		if($this->activated && $this->flushActivated){
			$this->pr($chaine, $class);
			ob_flush();
			flush();
		}
	}
	
	// fonction Select
	public function s($chaine, $class=''){
		if($this->activated && $this->selectActivated){
			$this->p($chaine, $class);
			ob_flush();
			flush();
		}
	}
	
	
	public function setActivated($bool=true){
		if(is_bool($bool)){
			$this->activated = $bool;
		}
	}
	
	public function setFlushActivated($bool){
		if(is_bool($bool)){
			$this->flushActivated = $bool;
		}
	}
	
	public function setPrintActivated($bool){
		if(is_bool($bool)){
			$this->printActivated = $bool;
		}
	}
	
	public function setSelectActivated($bool){
		if(is_bool($bool)){
			$this->selectActivated = $bool;
		}
	}



	
}
?>