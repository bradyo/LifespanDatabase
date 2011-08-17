<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }

    protected function _initDatabases()
    {
        $this->bootstrap('multidb');
        $resource = $this->getPluginResource('multidb');

        $db = $resource->getDb('db');
        Zend_Registry::set('db', $db);

        $ncbiDb = $resource->getDb('ncbiDb');
        Zend_Registry::set('ncbiDb', $ncbiDb);

        $ppodDb = $resource->getDb('ppodDb');
        Zend_Registry::set('ppodDb', $ppodDb);
    }

    protected function _initDoctrine()
    {
        //Load the autoloader
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Doctrine');
        $autoloader->pushAutoloader(array('Doctrine', 'autoload'));
        $autoloader->pushAutoloader(array('Doctrine', 'modelsAutoload'), '');

        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_MODEL_LOADING,
            Doctrine::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

        $options = $this->getOption('doctrine');
        Doctrine::loadModels($options['models_path']);

        $conn = Doctrine_Manager::connection($options['dsn'], 'agingdb');
        $conn->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);

        return $options;
    }

    protected function _initSession()
    {
        $session = $this->getPluginResource('session');
        $session->init();
        Zend_Session::start();
    }

    protected function _initRoutes()
    {
        $this->bootstrap('frontController');

        $router = $this->frontController->getRouter();

        // gene routes
        $route = new Zend_Controller_Router_Route(
            'genes/*',
            array(
                'module'        => 'default',
                'controller'    => 'gene',
                'action'        => 'index',
            )
        );
        $router->addRoute('catalog_category_product', $route);
    }
}

