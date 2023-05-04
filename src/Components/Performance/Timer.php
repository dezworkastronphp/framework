<?php

namespace Astronphp\Components\Performance;

class Timer{
	public $log			=	Array();

	public function __construct(){

	}

  	public function register($name,$time=null,$description=null){
		$register = $this->getOpen($name);
		
		if(is_bool($register)){
			$this->open($name,$time,$description);
		}else{
			$key=$register;
			$this->close($name,$key,$time);
		}
		return $this;
	}

	public function open($name,$time=null,$description=null){
		$this->log[$name][] = array(
			'description' 	=> $description,
			'start' 		=> (is_null($time)?microtime(true):$time),
			'end' 			=> null,
			'time' 			=> null,
		);
	}
	public function close($name,$key,$time=null){
		if(is_null($time)){
			$time = microtime(true);
		}
		$this->log[$name][$key]['end'] = $time;
		$this->log[$name][$key]['time'] = round(($time-$this->log[$name][$key]['start'])*1000,0);
	}
	
	public function getOpen($name){
		if(isset($this->log[$name])){
			foreach($this->log[$name] as $key => $value){
				if($value['end'] == null){
					return $key; 
				}
			}
		}
		return false;
		
	}
	
	public function get($name){
		if(isset($this->log[$name])){
			return $this->log[$name];
		}else{
			return null;
		}
	}

}