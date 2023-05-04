<?php

namespace Astronphp\Components\ErrorReporting;

use Exeption;
use Astronphp\Components\ErrorReporting\ErrorReportingParser;

class ErrorsDefine{

	
	private $displayErrors			=	true;
	private $logErrors				=	false;
	private $errorLogFile			=	"/tmp/log/error/php.log";
	private $errorSqlFile			=	"/tmp/log/error/sql.log";
	private $errorReporting			=	0;

	private $notifySlackKey 		=	"";

	public function __construct(array $conf){
		
		$this->displayErrors	=	(isset($conf['DisplayErrors'])?$conf['DisplayErrors']:$this->displayErrors);
		$this->logErrors		=	(isset($conf['LogErrors'])?$conf['LogErrors']:$this->logErrors);
		$this->errorLogFile		=	(isset($conf['ErrorLogFile'])?$conf['ErrorLogFile']:$this->errorLogFile);
		$this->errorSqlFile		=	(isset($conf['ErrorSqlFile'])?$conf['ErrorSqlFile']:$this->errorSqlFile);
		$this->errorReporting	=	(isset($conf['ErrorReporting'])?$conf['ErrorReporting']:$this->errorReporting);
		$this->notifySlackKey	=	(isset($conf['NotifySlackKey'])?$conf['NotifySlackKey']:$this->notifySlackKey);	

		$this->ErrorLogFile($this->errorLogFile);
		$this->LogErrors($this->logErrors);
		$this->DisplayErrors($this->displayErrors);
		$this->ErrorReporting($this->errorReporting);
		return $this; 
	}
	
	private function DisplayErrors($status=''){
		$status = ($status==true?1:0);
		
		ini_set( 'display_errors', false);
		if ($status== ini_get('display_errors')){
			return true;
		}
	}
	private function LogErrors($status=''){
		$status = ($status==true?1:0);
		ini_set( 'log_errors', $status);
		if($status==ini_get('log_errors')){
			return true;
		}
	}
	
	private function ErrorLogFile($filepath=''){
		if(empty($filepath)){
			$filepath=PATH_ROOT.'/tmp/log/error/php.log';
		}else{
			$filepath=PATH_ROOT.$filepath;
		}
		//Set path for errors
		if(!file_exists($filepath)){
			$difLog = explode('/',$filepath);
			array_pop($difLog);
			$difLog = implode('/', $difLog).'/';
			if(!file_exists($difLog) || $difLog==''){
				mkdir($difLog,2777, true);
			}
			$file = fopen($filepath,"w+") or die("Arquivo de log não pode ser aberto!");
			chmod($filepath,2777);
			$txt = "Created: ".date('d-m-Y H:m:i')."\n";
			fwrite($file, $txt);
			fclose($file);
		}
		ini_set('error_log', $filepath);
	}

	private function ErrorReporting($codeError){
		
		error_reporting(ErrorReportingParser::calculate($codeError));
		
		set_error_handler(
			function ($code , $text , $file , $line , $cont){
				$this->errorHandler([
						'type'=>$code,
						'file'=>$file,
						'line'=>$line,
						'message'=>$text
				]);
			},
			ErrorReportingParser::calculate($codeError)
		);
		
	}	
	

	public function errorHandler($error){
		
		if(ErrorReportingParser::getCodeErrorReporting() & $error['type']){
			\Errors::getInstance('ErrorView')->setType(
					'Compiler'
				)->setTitle(
					$this->FriendlyErrorType($error['type'])
				)->setError(
					str_replace(PATH_ROOT,'/',$error['file']).' : '.$error['line'].' '.$error['message']
			);
		}
	}
	

	private function FriendlyErrorType($type) { 
		switch($type) 
		{ 
			case E_ERROR: 				return 'E_ERROR';  // 1 // 
			case E_WARNING: 			return 'E_WARNING';  // 2 // 
			case E_PARSE: 				return 'E_PARSE';  // 4 // 
			case E_NOTICE: 				return 'E_NOTICE';  // 8 // 
			case E_CORE_ERROR: 			return 'E_CORE_ERROR';  // 16 // 
			case E_CORE_WARNING: 		return 'E_CORE_WARNING';  // 32 // 
			case E_COMPILE_ERROR: 		return 'E_COMPILE_ERROR';  // 64 // 
			case E_COMPILE_WARNING: 	return 'E_COMPILE_WARNING';  // 128 // 
			case E_USER_ERROR: 			return 'E_USER_ERROR';  // 256 // 
			case E_USER_WARNING: 		return 'E_USER_WARNING';  // 512 // 
			case E_USER_NOTICE: 		return 'E_USER_NOTICE';  // 1024 // 
			case E_STRICT: 				return 'E_STRICT';  // 2048 // 
			case E_RECOVERABLE_ERROR: 	return 'E_RECOVERABLE_ERROR';  // 4096 // 
			case E_DEPRECATED: 			return 'E_DEPRECATED';  // 8192 // 
			case E_USER_DEPRECATED: 	return 'E_USER_DEPRECATED';  // 16384 // 
		} 
		return ""; 
	} 
}