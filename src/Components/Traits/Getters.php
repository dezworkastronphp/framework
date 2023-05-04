<?php

namespace Astronphp\Components\Traits;

trait Getters {
    
    public function __get($name){
		if(method_exists($this,'get'.$name)){
			$n = 'get'.$name;
			return $this->$n();
		}else if(property_exists($this,$name)){
			return $this->$name;
		}
	}
    
}
