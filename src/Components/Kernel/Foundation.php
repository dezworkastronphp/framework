<?php

namespace Astronphp\Components\Kernel;


class Foundation{
        
        public function __construct()
        {   
            ob_start();
            define('PATH_ROOT', explode('vendor/astronphp', __DIR__)[0]);

            $this->defineAlias();
            
            register_shutdown_function(function(){
                $this->terminate();
            });

            try {
                //Instance base system with low level settings
                \Kernel::getInstance([
                    'Kernel',
                    \Astronphp\Components\Kernel\Kernel::class
                ]);
                
                //Instance APP with configurations and controller
                \App::getInstance([
                    'App',
                    \Astronphp\Components\Kernel\Application::class
                ])->generatorApp();

            } catch (\Exception $e) {
                \Errors::getInstance(
                    [   'ErrorView',
                        \Astronphp\Components\ErrorReporting\ErrorView::class
                    ]
                )->setType('Framework')->setTitle('Foundation')->setExeption($e);
            }

        }

        public function defineAlias()
        {
            foreach ([
                'App'           =>   [\Astronphp\Components\Support\App::class],
                'Config'        =>   [\Astronphp\Components\Support\Config::class],
                'Kernel'        =>   [\Astronphp\Components\Support\Kernel::class],
                'Errors'        =>   [\Astronphp\Components\Support\Errors::class],
                'Sessions'      =>   [\Astronphp\Components\Support\Sessions::class],
                'Http'          =>   [\Astronphp\Components\Support\Http::class],
                'Orm'           =>   [\Astronphp\Components\Support\Orm::class],
                'Performace'    =>   [\Astronphp\Components\Support\Performace::class],
            ] as $key => $aliases) {
                foreach ($aliases as $alias) {
                    class_alias($alias,$key);
                }
            }
        }
        
        
        public function terminate() //will be called when php script ends.
        {

            if(!is_null(error_get_last())){
                \Errors::getInstance('ErrorsDefine')->errorHandler(error_get_last());
            }
            if(\Errors::getInstance('ErrorView')->hasError){
                ob_clean();
                \Errors::getInstance('ErrorView')->showError();
            }
            
            ob_flush();
            
        }

}