<?php

namespace Astronphp\Components\Kernel;


class Kernel{

     /**
     * The framework version.
     *
     * @var string
     */
    const VERSION = '0.0.27';
    
    /**
     * The base path for installation.
     *
     * @var string
     */
    protected $basePath;
     
    /**
     * The configurations for applications.
     *
     * @var string
     */
    protected $configurations = [];
    
    function __construct(){
          $this->initTimer();

          $this->getConfigurations();

          $this->Timezone();

          $this->ErrorDefine();

          $this->SessionServer();

          $this->RequestLimiter();

          $this->Http();
     }

     /**
     * Get the version number of the framework.
     *
     * @return string
     */
     public function version()
     {
          return static::VERSION;
     }
     
     public function initTimer()
     {
          //Instance timer's system

          $timer=\Performace::getInstance([
               'Timer',
               \Astronphp\Components\Performance\Timer::class
          ]);
          
          //request server
          $timer->register('systemload',$_SERVER['REQUEST_TIME_FLOAT']);

          //request server
          $timer->register('request',$_SERVER['REQUEST_TIME_FLOAT']);
          $timer->register('request',START_ASTRONPHP);
          
          //request framework
          $timer->register('framework', START_ASTRONPHP);

     }
     
     public function getConfigurations($key=null)
     {    
          $Config = \Config::getInstance([
               'ConfigFile',
               \Astronphp\Components\Config\ConfigFile::class
          ]);

          $this->configurations = $Config->configurations;
          
          if(is_null($key)){
               return $this->configurations;
          }else{
               return $this->configurations[$key];
          }
     }

     public function RequestLimiter()
     {    
          \Http::getInstance(
               [
                   'RequestLimiter',
                   \Astronphp\Components\Http\RequestLimiter::class
               ],
               (isset($this->configurations['Request'])?$this->configurations['Request']:array())
           );
     }

     public function Timezone()
     {     
          if(isset($this->configurations['Region']))
          {
               \Config::getInstance( 
                    [
                         'Timezone',
                         \Astronphp\Components\Region\Timezone::class,
                    ],
                    $this->configurations['Region']
               );
          }
     }
     
     public function ErrorDefine()
     {     
          if(isset($this->configurations['ErrorsDefine']))
          {
               \Errors::getInstance( 
                    [
                         'ErrorsDefine',
                         \Astronphp\Components\ErrorReporting\ErrorsDefine::class,
                    ],
                    $this->configurations['ErrorsDefine']
               );

               \Errors::getInstance(
                    [   'ErrorView',
                        \Astronphp\Components\ErrorReporting\ErrorView::class
                    ]
                );
          }
     }
    
     public function SessionServer()
     {    
          if(isset($this->configurations['Sessions']))
          {
               \Sessions::getInstance(
                    [
                         'SessionServer',
                         \Astronphp\Components\Session\SessionServer::class,
                    ],
                    $this->configurations['Sessions']
               );
          }
     }

     public function Http()
     {    
          if(isset($this->configurations['Request']))
          {
               \Http::getInstance(
                    [
                         'SessionServer',
                         \Astronphp\Components\Http\RequestLimiter::class,
                    ],
                    $this->configurations['Request']
               );
          }
          
     }
}