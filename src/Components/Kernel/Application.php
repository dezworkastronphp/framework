<?php

namespace Astronphp\Components\Kernel;

use Exception;

class Application
{   
    
    public $version;
    public $nameApplication = null;
    public $environment     = null;
    public $addressUri      = null;
    public $addressFullUri  = null;
    /**
     * Create a new Illuminate application instance.
     *
     * @param  string|null  $basePath
     * @return void
     */
    public function __construct()
    {
        return $this; 
    }

    public function generatorApps(){
        $generatorApps = \Http::getInstance(
            [
                'GeneratorApps',
                \Astronphp\Components\Applications\ManagerApp\GeneratorApps::class
            ],
            \kernel::getInstance('Kernel')->getConfigurations('Applications')
        );
        return $generatorApps;
    }
    public function generatorApp()
    {

        $generatorApps=$this->generatorApps();

        if( 
            is_null($generatorApps->getCurrentApplication()) &&
            isset(\kernel::getInstance('Kernel')->getConfigurations('Applications')['main']['development']['addressUri']) &&
            empty(\kernel::getInstance('Kernel')->getConfigurations('Applications')['main']['development']['addressUri'])
        )
        { 
            // if dont found link, write on json the actual address
            $writeonJson = new \Astronphp\Components\Config\UpdateConfigFile();
            $writeonJson->setConfigUriDev();

            // claen GeneratorApps and File of config
            \Http::unsetInstance('GeneratorApps');
            \Config::unsetInstance('ConfigFile');

            // reset config json and register \kernel::getInstance('Kernel')
            \kernel::getInstance('Kernel')->getConfigurations('Applications');

            $generatorApps=$this->generatorApps();
        }

        if(is_null($generatorApps->getCurrentApplication()))
        { 
            throw new \Exception('Application:'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].' not found in Astronphp.json');
        }

        $this->nameApplication  = $generatorApps->getCurrentApplication()->nameApplication;
        $this->environment      = key($generatorApps->getCurrentApplication()->environmentApp);
        $this->version          = \Config::getInstance('ConfigFile')->Version;
               
        \Http::getInstance(
            [
                'LocationBroker',
                \Astronphp\Components\Header\Location\LocationBroker::class
            ]
        )->AuthorizeLocation(
            $generatorApps->getCurrentApplication()->environmentApp
        );
        
        $orm = \Orm::getInstance(
            [
                'Orm',
                \Astronphp\Components\DataBase\Orm::class
            ],
            $generatorApps->getCurrentApplication()
        );
        $orm->doctrine();

        
        $app = \App::getInstance(
            [
                'instanceApplication',
                \Astronphp\Components\Applications\instanceApplication::class
            ],
            $generatorApps->getCurrentApplication()
        );

        $this->addressUri = $app->addressUri;
        $this->addressFullUri = ($app->forceHttps==true?'https://':'http://').($app->forceWww==true?'www.':'').$app->addressUri;


        \Performace::getInstance('Timer')->register('framework', microtime(true));
        \Performace::getInstance('Timer')->register('appload', microtime(true));

        $app->instanceController(); 

        if(class_exists('\Astronphp\Debugbar\View')){
            $a = new \Astronphp\Debugbar\View();
            $a->closeVars();
            $a->showBar();
        }
    }

    public function addressFullUri(){
        return $this->addressFullUri;
    }
    public function addressUri(){
        return $this->addressUri;
    }
    public function version(){
        return $this->version;
    }
}