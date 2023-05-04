<?php

namespace Astronphp\Components\ErrorReporting;

class ErrorView{

	public $environment;
	public $hasError	 			= false;
	public $TittleFriendly	 		= 'Sorry, It’s not you. it’s us.';
	public $descriptionFriendly	 	= 'That’s an error and we’re working towards creating something better. We won’t be long.';
	public $title	 				= '';
	public $type	 				= '';
	public $error 					= '';
	public $showError 				= '';
	public $exeption;

	function __construct(){
		return $this;
	}

	public function showError(){
		header("Access-Control-Allow-Origin:*");
		header("Access-Control-Allow-Headers:Content-Type");
		header('Content-type: text/html');

		try {
			$this->environment ='';
			$this->environment = \App::getInstance('App')->environment;
		} catch (\Exception $e) {
			//$this->setExeption($e);
		}
		$this->showError='';
			
		$this->showError.='<html>';
		$this->showError.='	<head>';
		$this->showError.='	<title>'.$this->TittleFriendly.'</title>';
		$this->showError.='	<style>';
		$this->showError.='		*{margin: 0px; padding:0px;}';
		$this->showError.='		html{background:#24292e; font-family: Arial,sans-serif; color:#f5f5f5;}';
		$this->showError.='		body{     margin: 0px auto; background-color: #24292e; background-image: url(https://astronphp.github.io/assets/media/bg.svg); background-repeat: no-repeat; background-size: 100% auto;background-position: center 50% ,0 0,0 0; }';
		$this->showError.='		.contents{ margin: 0px auto; padding: 5% 5% 1% 5%; max-width: 1280px;}';
		$this->showError.='		a{ font-weight: 700; text-decoration: none; background: #ffffff; padding: 4px 7px; border-radius: 2px; color: #252f34;}';
		$this->showError.='		table { font-size: 12px;line-height: 19px; }';
		$this->showError.='		table tr:nth-child(even) {background: #6f6f6f} table tr:nth-child(odd) {background: #464646;}';
		$this->showError.='		code{ font-size: 11px; display: table; float: left; margin-top: 19px; width: 100%; word-break: break-all;}';
		$this->showError.='		#code{ word-break: break-all; }';
		$this->showError.='		.date{ float:right; }';
		$this->showError.='		.error{ margin: 8px 0px; font-size: 12px; background: #444; color: #DDD; font-family: monospace; padding: 10px; border-radius: 4px;}';
		$this->showError.='	</style>';
		$this->showError.='	</head>';
		$this->showError.='	<body>';
		$this->showError.='		<div class="contents first">';
		$this->showError.='			<h1>'.$this->TittleFriendly.'</h1><br/>';
		$this->showError.='			<h3>'.$this->descriptionFriendly.'</h3><br/>';
		
		$details='			<h4>';
		if(!empty($this->getType())){
			$details.='['.$this->getType().'] ';
		}
		if(!empty($this->getTitle())){
			$details.=$this->getTitle();
		}
		$details.='<b class="date">k'.\Kernel::getInstance('Kernel')->version().' | v'.$environment = \App::getInstance('App')->version().' | '.date('Y-m-d H:i:s').'</b></h4>';
		if(!empty($this->getError())){
			$details.= '<p class="error">'.nl2br(strip_tags($this->getError())).'</p>';
		}
		$details.= $this->getExeptionHtml();
		$details.= '<br/>URL:'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		if($this->environment!='production'){
			$this->showError.= '<code>'.$details.'</code>';
		}else{
			$this->showError.= '<br/><br/><br/><br/><a href="#" onclick="document.getElementById(\'code\').style.display=(document.getElementById
			(\'code\').style.display==\'none\'?\'block\':\'none\');">Code &#x276F;</a><br/>';
			$this->showError.= '<code id="code" style="display:none">'.base64_encode($details).'</code>';
		}

		$this->showError.='		</div>';

		
		$this->showError.='	</body>';
		$this->showError.='</html>';

		if (strpos($_SERVER['HTTP_ACCEPT'], 'htm') === false) {
			echo nl2br(strip_tags($this->getError())); exit;
		}else{
			echo $this->showError; exit;
		}
	
	}
	
	
	public function setError($v=''){
		$this->hasError=true;
		error_log(strip_tags($v)."\n-----------------------", 0);
		$this->error = $v;
		return $this;
	}
	public function getError(){
		return $this->error;
	}

	public function setTitle($v=''){
		$this->title = $v;
		return $this;
	}
	public function getTitle(){
		return $this->title;
	}
	
	public function setType($v=''){
		$this->type = $v;
		return $this;
	}
	public function getType(){
		return $this->type;
	}
	
	public function setExeption(\Exception $v){
		$this->exeption = $v;
		$this->hasError=true;
		error_log(strip_tags($this->getExeption()->getMessage()), 0);
	
		return $this;
	}

	public function getExeption(){
		return $this->exeption;
	}


	public function getExeptionHtml(){
		if(!empty($this->getExeption())){
			$e='				<p class="error">'.strip_tags($this->getExeption()->getMessage()).'</p>';
			$e.='				<table width="100%" cellpadding="0" cellspacing="0">';
			$e.='				<tr>';
			$e.='					<td>File</td>';
			$e.='					<td>Line</td>';
			$e.='					<td>Function</td>';
			$e.='				</tr>';
			foreach($this->getExeption()->getTrace() as $k => $v){
			
				$e.='				<tr>';
				$e.='					<td>'.(isset($v['file'])?str_replace(PATH_ROOT,'./',$v['file']):'').'</td>';
				$e.='					<td>'.(isset($v['line'])?$v['line']:'').'</td>';
				$e.='					<td>'.(isset($v['function'])?$v['function']:'').'</td>';
				$e.='				</tr>';
			}
			$e.='				</table>';
		}
		return (isset($e)?$e:'');
	}

}