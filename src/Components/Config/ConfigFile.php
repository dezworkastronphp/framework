<?php

namespace Astronphp\Components\Config;

class ConfigFile{

	public $configurations 		= 	array();
	private $directoryConfig 	= 	'/astronphp.json';

	function __construct(){
		$this->setConfigByJson();
		$this->getConfigByJson();
		return $this;
	}


	public function setConfigByJson(){
		if(file_exists(PATH_ROOT.$this->directoryConfig)){
			try {
				$this->configurations = file_get_contents(PATH_ROOT.$this->directoryConfig);
				$this->configurations = json_decode($this->configurations,true);
			}catch(\Exception $e) {
			    throw new \Exception("astronphp.json file corrupted or with syntax error");
			}
		}else{
			throw new \Exception("astronphp.json file not found in project root");
		}
		return $this;
	}
	public function getConfigByJson(){
		
		if(!empty($this->configurations) && count($this->configurations)>0){
			foreach ($this->configurations as $key => $value) {
				$this->$key=$value;
			}
		}
		return $this;
	}

	public function __set($name, $value){
		if(isset($this->configurations[$name])){
			$this->configurations[$name]=$value;
		}
		return $this;
	}
	public function __get($name){
		if(isset($this->configurations[$name])){
			return $this->configurations[$name];
		}
	}

}
