<?php

namespace Astronphp\Components\Region;

class Timezone{

	private $locale;
	private $dateTimezone;

	public function __construct(array $conf){
		$this->locale	        =	(isset($conf['locale'])?$conf['locale']:$this->locale);
		$this->dateTimezone		=	(isset($conf['date_timezone'])?$conf['date_timezone']:$this->dateTimezone);

		$this->Locale($this->locale);
		$this->DateTimezone($this->dateTimezone);
		return $this; 
	}
	
	private function Locale($v){
        if(isset($v) && !empty($v)){
            ini_set('intl.default_locale',$v);
        }
	}
	private function DateTimezone($v){
        if(isset($v) && !empty($v)){
            date_default_timezone_set($v);
        }
	}
}