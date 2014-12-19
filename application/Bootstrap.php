<?php
use Doctrine\DBAL\Types\Type;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    protected function _initCaching(){

        $frontendOptions = array(
            'lifetime' => 3600,
            'automatic_serialization' => true
        );

        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH . '/../data/cache'
        );

        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

        Zend_Registry::set('Zend_Cache', $cache);

    }
    
    // Do not rename this method _initDoctrine() this will result in a circular
    // dependency error.
    protected function _initDoctrineExtra(){
        
        $doctrine = $this->bootstrap('doctrine')->getResource('doctrine');
        
        $em = $doctrine->getEntityManager();
        
        Zend_Registry::set('em', $em);
    }

    protected function _initRoutes(){

        $this->bootstrap('frontController');
        $router = $this->getResource('frontController')->getRouter();

        $this->bootstrap('caching');
        $cache = Zend_Registry::get('Zend_Cache');

        $myroutes = $cache->load('myroutes');

        if($myroutes === false) {
            $myroutes = array();

            $route = new Zend_Controller_Router_Route('/',
                    array(
                        'module' => 'application',
                        'controller' => 'index',
                        'action' => 'index'
                    ));
            $myroutes['home'] = $route;

            $cache->save($myroutes);

            // var_dump($myroutes);
        }
        $router->addRoutes($myroutes);
    }

    protected function _initConfig(){
        $config = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $config);
        return $config;
    }
    // TODO Session (in Zusammenhang mit Doctrine)
    // protected function _initSession(){
    
    // $options = $this->getOptions();
    
    // $config = $options['resources']['session'];
    // Zend_Session::setOptions($config);
    
    // Zend_Session::start();
    // }
    
    protected function _initView(){
        
        $options = $this->getOptions();
        $config = $options['resources']['view'];
        
        if(isset($config)) {
            $view = new Zend_View($config);
        } else {
            $view = new Zend_View();
        }
        
        if(isset($config['doctype'])) {
            $view->doctype($config['doctype']);
        }
        
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view);
        
        return $view;
    }

    protected function _initErrorHandler(){
        
        // TODO : über app.ini lösen?!
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(
                new Zend_Controller_Plugin_ErrorHandler(
                        array(
                            'module' => 'application',
                            'controller' => 'error', 
                            'action' => 'index-error'
                        )));
    }



}