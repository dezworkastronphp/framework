<?php

namespace Astronphp\Components\Applications\ManagerApp;
use Astronphp\Components\Applications\ManagerApp\Applications;

class GeneratorApps{
	public $apps;
	public $currentApplication 	= null;
	public $serverUri 			= '';

	public function __construct($objectJson){
		$this->serverUri =  $this->removeLastBackslash($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		foreach ($objectJson as $nameApp => $environment) {
			$this->apps[] = new Applications($nameApp, $environment);
		}
		if(is_null($this->apps)){
			throw new \Exception('You need to set up an app on astronphp.json.');
		}else{
			return $this;
		}
	}

	public function getCurrentApplication(){
		if(!is_null($this->currentApplication)){
			return $this->currentApplication;
		};
		$equals=$this->getCurrentApplicationEquals();
		
		if(is_null($equals)){
			$like=$this->getCurrentApplicationLike();
		}

		return $this->currentApplication;

	}

	private function getCurrentApplicationEquals(){
		$this->currentApplication=null;
		
		foreach ($this->apps as $app) {
			foreach ($app->environmentApp as $configApp) {
				if(isset($configApp->addressUri)){
					if(is_array($configApp->addressUri)){
						$arrayUri = $configApp->addressUri;
					}else{
						$arrayUri=[];
						$arrayUri[]= $configApp->addressUri;
					}

					foreach ($arrayUri as $uri) {
						$appUri = $this->removeLastBackslash($uri);

						if(!empty($uri) && $this->serverUri==$appUri){ 
								$configApp->active = true;
								$app->active = true;
								$configApp->addressUri	=	$uri;
								
								$this->currentApplication = $app;
								$this->currentApplication->environmentApp = array();
								$this->currentApplication->environmentApp[$configApp->environment]=$configApp;
								break;
						}
					}
				}
			}
		}
		
		return $this->currentApplication;
		
	}

	private function getCurrentApplicationLike(){

		$this->currentApplication=null;
		$score=0;
		foreach ($this->apps as $app) {
			foreach ($app->environmentApp as $configApp) {
				if(isset($configApp->addressUri)){

					if(is_array($configApp->addressUri)){
						$arrayUri = $configApp->addressUri;
					}else{
						$arrayUri=[];
						$arrayUri[]= $configApp->addressUri;
					}
					foreach ($arrayUri as $uri) {
						
						$appUri = $this->removeLastBackslash($uri);
						if(!empty($uri) && strpos($this->serverUri,$appUri)!==false && $score<strlen($appUri)){
							$configApp->active = true;
							$app->active = true;
							$configApp->addressUri	=	$uri;
							$this->currentApplication=null;
							$this->currentApplication = $app;
							$this->currentApplication->environmentApp = array();
							$this->currentApplication->environmentApp[$configApp->environment]=$configApp;
							$score = strlen($appUri);
						}
					}
				}
			}
		}
		return $this->currentApplication;
	}

	private function removeLastBackslash($v){
		return (substr($v,-1)=='/'?substr($v, 0,-1):$v);
	}
}