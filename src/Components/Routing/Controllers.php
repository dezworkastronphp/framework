<?php

namespace Astronphp\Components\Routing;

class Controllers{

    private $controllerPath     =  PATH_ROOT.'src/';
    private $controllerFile;
    private $uriApp;
    private $uriMapping;
    private $uriMethod;
    private $nameController;

    public function __construct(string $uriApp='', string $nameApp=''){
        $this->controllerPath   .= 'Controller/'.$nameApp;
        $this->uriApp            = $uriApp;
        
        $this->setUrlMapping();
        $this->getApplicationPathFiles();
        $this->instanceClass();
    }
    
    private function setUrlMapping(){
        $this->uriMapping = str_replace($this->uriApp ,'', $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    }

    private function getArrayUri(){
        if(empty($this->uriMapping)){
            return array();
        }else{
            return explode('/',$this->uriMapping);
        }
    }
    
    private function setMethodsURI($method){
        $this->uriMethod = $method;
        return $this;
    }

    private function getApplicationPathFiles(){
        $urlArray = $this->getArrayUri();

        $this->controllerFile  = $this->controllerPath;
        do{
            $currentArray =  $this->camelCase(current($urlArray));
            /*
            * If the position of the current array is in the directory as a folder, enter and move to the next level.
            *
             */
            if( (file_exists($this->controllerFile) && in_array($currentArray, scandir($this->controllerFile))) ){
               
                $this->controllerFile       .=  '/'. $currentArray;

                next($urlArray);
                if(isset($urlArray[key($urlArray)])){
                    $this->setMethodsURI($urlArray[key($urlArray)]);
                }
                $loopDir = true;
            }
            /**
             * If the position of the current array is in the file as archi, enter and finish the search.
             */
            else if( file_exists($this->controllerFile) &&  in_array($currentArray.'Controller.php', scandir($this->controllerFile)) ){
                
                $this->nameController        =  $currentArray.'Controller';
                $this->controllerFile       .=  '/'.$this->nameController.'.php';                

                next($urlArray);
                if(isset($urlArray[key($urlArray)])){
                    $this->setMethodsURI($this->camelCase($urlArray[key($urlArray)]));
                }
                $loopDir = false;
            }
            /**
             * If you do not have any folder or file, enter this option that looks for an index file, if it does not exist then call a controller for Error 404
             */
            else{
                
                if(isset($urlArray[key($urlArray)])){
                    $this->setMethodsURI($this->camelCase($urlArray[key($urlArray)]));
                }
                
                if(file_exists($this->controllerFile.'/IndexController.php')){
                    $this->nameController        =  'IndexController';
                    $this->controllerFile       .=  '/'.$this->camelCase($this->nameController).'.php';
                }else{
                    $this->nameController       =  $this->defineError404()->nameController;
                    $this->controllerFile       =  $this->defineError404()->controllerFile;
                }
                $loopDir = false;
            }
        }while($loopDir);

        return $this;
        
    }



    public function instanceClass(){
        if(empty($this->controllerFile) || !file_exists($this->controllerFile) || !is_file($this->controllerFile)){
            if(!class_exists($this->nameController)){
                $this->defineError404();   
            }
            require_once $this->controllerFile;
        }

        require_once $this->controllerFile;

        $controller = new $this->nameController();
        $method = $this->uriMethod;
        
        if(method_exists($controller,$method) && (new \ReflectionMethod($controller, $method))->isPublic() ){
            $controller->$method();
        }else if(method_exists($controller, 'show') && (new \ReflectionMethod($controller, 'show'))->isPublic()){
            $controller->show();
        }
    }
    
    public function defineError404(){
        $this->nameController       =  'Error404Controller';
        $this->controllerFile       =  $this->controllerPath.'/errors/'.$this->nameController.'.php';
        return $this;
    }
    
    public function camelCase(string $text){

        $textSplit = explode('-',$text);
        $text='';
        foreach ($textSplit as $key => $value) {
            $text.= ucfirst($value); 
        }
        return $text;
    }

}