<?php

namespace Astronphp\Components\Applications;
use Astronphp\Components\Routing\Controllers;

class instanceApplication{

	public $nameApplication	= '';
	public $environment 	= '';
	public $active		 	= '';
	public $addressUri	 	= '';
	public $forceHttps	 	= '';
	public $forceWww	 	= '';
	public $dataBase	 	= '';

	public function __construct($conf){
		$this->defineInstanceConfig($conf->nameApplication, $conf->environmentApp);
	}
	
	public function defineInstanceConfig(string $name, array $conf){
		$this->nameApplication = $name;
		$conf = current($conf);
		$this->environment	=	$conf->environment;	
		$this->active		=	$conf->active;
		$this->addressUri	=	$conf->addressUri;
		$this->forceHttps	=	$conf->forceHttps;
		$this->forceWww		=	$conf->forceWww;
		$this->dataBase		=	$conf->dataBase;
		return $this;
	}
	

	public function instanceController(){
		new Controllers($this->addressUri, $this->nameApplication);
	}
}
