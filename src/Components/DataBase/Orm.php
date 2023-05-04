<?php

namespace Astronphp\Components\DataBase;
		
class Orm{
	public $engine;
	public $host;
	public $username;
	public $password;
	public $dbname;
	public $port;
	public $charset;
	public $isDevMode;

  	public function __construct(\Astronphp\Components\Applications\ManagerApp\Applications $conf){
		
		$this->dirEntity		=	PATH_ROOT.'src/Entity/'.ucfirst($conf->nameApplication);
		$this->entityNamespace	=	ucfirst($conf->nameApplication);
		
		$conf = current($conf->environmentApp);
		
		$this->engine		=	$conf->dataBase->engine		??	'pdo_mysql';
		$this->host			=	$conf->dataBase->host		??	null;
		$this->username		=	$conf->dataBase->username	??	null;
		$this->password		=	$conf->dataBase->password	??	null;
		$this->dbname		=	$conf->dataBase->dbname		??	null;
		$this->port			=	$conf->dataBase->port		??	'3306';
		$this->charset		=	$conf->dataBase->charset 	??	'utf8';
		$this->isDevMode	=	($conf->environment=='production'?false:true);

		if($conf->dataBase!=false && (
				!isset($conf->dataBase->host) ||
				!isset($conf->dataBase->username) ||
				!isset($conf->dataBase->password) ||
				!isset($conf->dataBase->dbname)
			)
		){
			throw new \Exception("Invalid database information in astronphp.json. Is required [host,username,password,dbname]");
		}
	}
	
	public function doctrine(){
		if(	!empty($this->host) &&
			!empty($this->username) &&
			!empty($this->password) &&
			!empty($this->dbname)
		){
			if(!\file_exists($this->dirEntity)){
				throw new \Exception("Create a folder for entity on ".$this->dirEntity);
			}
			if(class_exists(\Astronphp\Orm\ConnectDoctrine::class)){
					return \Orm::getInstance(
						[
							'Doctrine',
							\Astronphp\Orm\ConnectDoctrine::class
						]
					);
			}else{
				throw new \Exception("Use composer require astronphp/orm");
			}
		}
	}
}