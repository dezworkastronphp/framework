<?php

namespace Astronphp\Components\Applications\ManagerApp;

class EnvironmentDataBase{

	public $engine;
	public $host;
	public $username;
	public $password;
	public $dbname;
	public $port;
	public $charset;

	function __construct(array $conf){

		$this->engine		=	$conf['engine'] 		??		null;
		$this->host			=	$conf['host'] 			??		null;
		$this->username		=	$conf['username'] 		??		null;
		$this->password		=	$conf['password'] 		??		null;
		$this->dbname		=	$conf['dbname'] 		??		null;
		$this->port			=	$conf['port'] 			??		null;
		$this->charset		=	$conf['charset'] 		??		null;

	}
}