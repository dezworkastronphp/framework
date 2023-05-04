<?php

namespace Astronphp\Components\Config;

class UpdateConfigFile{

	public $configurations 		= 	array();
	private $directoryConfig 	= 	'astronphp.json';

	function __construct(){

	}


	public function openFile(){
		//Load the file
		$contents = file_get_contents(PATH_ROOT.$this->directoryConfig);
		//Decode the JSON data into a PHP array.
		$contentsDecoded = json_decode($contents, true);
		return $contentsDecoded;
	}
	public function closeFile($contentsDecoded){
		//Encode the array back into a JSON string.
		$json = json_encode($contentsDecoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		//Save the file.
		file_put_contents(PATH_ROOT.'astronphp.json', $json);
	}
	public function setConfigUriDev(){
		$contentsDecoded = $this->openFile();
		//Modify the counter variable.
		$contentsDecoded['Applications']['main']['development']['addressUri'] = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		
		$this->closeFile($contentsDecoded);

	}

}
