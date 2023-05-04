<?php

namespace Astronphp\Components\Http;

use Exeption;
use Astronphp\Components\Traits\Getters;
use Astronphp\Components\Traits\Setters;
use Astronphp\Components\Http\Response;


class RequestLimiter{

	use Getters,Setters;

	public $ip						=	'';
	public $amountAccess			=	0;
	public $timeSeconds				=	3;
	public $maxRequestIp			=	30;
	public $timeSecondsBanned		=	10;

	public function __construct($conf){

		
		$this->timeSeconds			=	(isset($conf['timeSeconds']) && !empty((int)$conf['timeSeconds'])? $conf['timeSeconds'] : $this->timeSeconds);
		$this->maxRequestIp			=	(isset($conf['maxRequestIp']) && !empty((int)$conf['maxRequestIp'])? $conf['maxRequestIp'] : $this->maxRequestIp);
		$this->timeSecondsBanned	=	(isset($conf['timeSecondsBanned']) && !empty((int)$conf['timeSecondsBanned'])? $conf['timeSecondsBanned'] : $this->timeSecondsBanned);

		if(isset($_SESSION['TIME_FORBIDDEN'])) {
			$this->timeSeconds = $_SESSION['TIME_FORBIDDEN'];
		}
		$this->ip 		= $_SERVER['REMOTE_ADDR'];
		$this->Calculate();
		
	}

	/**
	 * This function is intended to limit the number of simultaneous requests of the same ip in the application
	 */
	private function Calculate()
	{
		$requests=(isset($_SESSION['REQUEST_LIMITER'])?$_SESSION['REQUEST_LIMITER']:array());

		$_SESSION['REQUEST_LIMITER'] = array();
		foreach($requests as $key => $request) {
			if ($request["ip"] == $_SERVER['REMOTE_ADDR'] && $request["time"] >= time() - $this->timeSeconds) {
				$this->amountAccess++;
				$_SESSION['REQUEST_LIMITER'][] = $request;
			}
		}
		if ($this->amountAccess >= $this->maxRequestIp) {
			$_SESSION['TIME_FORBIDDEN'] = $this->timeSecondsBanned;
			new Response(429); 
			exit;
		}
		$_SESSION['TIME_FORBIDDEN'] 	= $this->timeSeconds;
		$_SESSION['REQUEST_LIMITER'][] 	= $requests[] = ["time" => time(), "ip" => $_SERVER['REMOTE_ADDR']];
	}

	protected function setTimeSeconds($value){
		$this->timeSeconds=$value;
	}
	protected function setMaxRequestIp($value){
		$this->maxRequestIp=$value;
	}
	protected function setTimeSecondsBanned($value){
		$this->timeSecondsBanned=$value;
	}


}
