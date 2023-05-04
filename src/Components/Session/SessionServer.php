<?php

namespace Astronphp\Components\Session;

class SessionServer{

	
	public function __construct($objectCache=false){

		$this->CacheLimiter(
			(isset($objectCache['sessionCacheLimiter'])?$objectCache['sessionCacheLimiter']:'')
		);
		$this->CacheExpire(
			(isset($objectCache['sessionCacheExpire'])?$objectCache['sessionCacheExpire']:'')
		);
		$this->SavePath(
			(isset($objectCache['sessionSavePath'])?$objectCache['sessionSavePath']:'')
		);
		
		$this->ChaceNavegation(
			(isset($objectCache['cacheNavegation'])?$objectCache['cacheNavegation']: date('Y-m-d H:i:s'))
		);
		
		return $this;

	}

	
	//Private Class
	private function CacheLimiter($cacheLimiter=''){
		if($cacheLimiter != 'nochace' && $cacheLimiter != 'private' && $cacheLimiter != 'private_no_expire' && $cacheLimiter != 'public'){
			$cacheLimiter='private';
		}
		session_cache_limiter($cacheLimiter);
	}

	private function CacheExpire($cacheExpire=''){
		if(empty((int)$cacheExpire)){
			$cacheExpire=10080;
		}
		session_cache_expire($cacheExpire);
	}
	
	private function SavePath($savePath=''){
		
		if(!file_exists(PATH_ROOT.$savePath)){
			mkdir(PATH_ROOT.$savePath,2777,true);
		}
		
		if(!empty($savePath) && !is_dir(PATH_ROOT.$savePath)){
			echo PATH_ROOT.$savePath; exit;
			$savePath='storage/framework/sessions/';
		}

		session_save_path(PATH_ROOT.$savePath);
		session_start();

	}
	
	private function ChaceNavegation($date=''){
		if($date=='now'){
			$date = new \DateTime();
		}else{
			$date = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
		}

		header('Expires: '.$date->format('D, d M Y H:i:s \G\M\T'));
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-checkcheck=0, pre-check=0', FALSE);
		header('Pragma: no-cache');
	}
}
