<?php

namespace Astronphp\Components\Traits;

trait Setters {
    
    public function __set($name,$value){
		if(method_exists($this,'set'.$name)){
			$n = 'set'.$name;
			$this->$n($value);
			return $this;
		}else if(property_exists($this,lcfirst($name))){
			$name=lcfirst($name);
			$this->$name=$value;
			return $this;
		}
	}
}
