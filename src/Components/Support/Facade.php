<?php

namespace Astronphp\Components\Support;
use Exception;

class Facade
{   
    public static $instance = [];
 
    private function __construct()
    {
        
    }
 
    public static function getInstance($object=null,$parms=null)
    {   
        if(is_string($object)){
            if($object=='Doctrine' && self::returnInstance($object)->connect()!=null){
                return self::returnInstance($object)->connect();
            }
            return self::returnInstance($object);
        }
        
        $object['key']      =  $object[0];
        $object['class']    =  (isset($object[1])?$object[1]:$object[0]);

        
        if(isset($object) && is_array($object) && class_exists($object['class'])){
            if (!isset(self::$instance[$object['key']])) {
                self::$instance[$object['key']] = new $object['class']($parms);
            }
            return self::returnInstance($object['key']);
        }
        throw new \Exception('Facade Instance <b>'.$object['class'].'</b> not found.');
    }

    public static function unsetInstance($object='')
    {   
        if (isset(self::$instance[$object])) {
            unset(self::$instance[$object]);
            return true;
        }
        throw new \Exception('Facade unsetInstance <b>'.$object.'</b> not found.');
    }

    private static function returnInstance($stringKey=null){

            if (isset(self::$instance[$stringKey])) {
                return self::$instance[$stringKey];
            }else{
                throw new \Exception('Facade returnInstance <b>'.$stringKey.'</b> not found.');
            }
    }
}
