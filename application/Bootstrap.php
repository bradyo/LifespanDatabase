<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
     protected function _initAutoloader() {
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->registerNamespace('Doctrine');
        $loader->registerNamespace('NP');
        $loader->registerNamespace('Application');
    }

    protected function _initHtmlPurifier() {
        require_once 'HTMLPurifier/Bootstrap.php';
        spl_autoload_register(array('HTMLPurifier_Bootstrap', 'autoload'));
    }

    protected function _initLayout() {
        Zend_Layout::startMvc();
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayoutPath(APPLICATION_PATH . '/layouts/scripts');
        $layout->setLayout('layout');
        $layout->startMvc();
    }
    
    protected function _initFrontControllerOptions() {
        $front = Zend_Controller_Front::getInstance();
        $front->setControllerDirectory(APPLICATION_PATH . '/controllers');
    }
    
    protected function _initView() {
        $view = new Zend_View();
        
        $view->addHelperPath('Application/View/Helper', 'Application_View_Helper');
        $view->addHelperPath('NP/View/Helper', 'NP_View_Helper');

        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');

        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        $view->headMeta()->appendHttpEquiv('Content-Language', 'en-US');

        $view->headScript()->appendFile('/library/jquery-1.4.4.min.js');
        $view->headScript()->appendFile('/library/jquery.tools.min.js');

        $view->headLink()->appendStylesheet('/css/global.css');
        $view->headScript()->appendFile('/js/global.js');

        $view->headLink()->appendStylesheet('/library/jquery-ui/css/custom/jquery-ui-1.8.7.custom.css');
        $view->headScript()->appendFile('/library/jquery-ui/js/jquery-ui-1.8.7.custom.min.js');

        $view->headScript()->appendFile('/library/fancybox/jquery.fancybox-1.3.2.pack.js');
        $view->headLink()->appendStylesheet('/library/fancybox/jquery.fancybox-1.3.2.css', 'screen');

        $view->headTitle('Sageweb');
        $view->headTitle()->setSeparator(' - ');
        
        // add view helper
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }

    protected function _initPlugins() {
        $this->bootstrap('frontController');
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Application_Plugin_Access());
    }

    protected function _initCache() {
        $frontend = array(
            'lifetime' => 7200,
            'automatic_serialization' => true,
        );
        $backend = array(
            'cache_dir' => DATA_PATH . '/cache/application'
        );
        $cache = Zend_Cache::factory('core', 'File', $frontend, $backend);
        Zend_Registry::set('cache', $cache);
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

    protected function _initSession() {     
        $this->bootstrap('databases');
        $config = array(
            'name'           => 'session',
            'primary'        => 'id',
            'modifiedColumn' => 'modified',
            'dataColumn'     => 'data',
            'lifetimeColumn' => 'lifetime'
        );
        $sessionHandler = new Zend_Session_SaveHandler_DbTable($config);
        Zend_Session::setSaveHandler($sessionHandler);
        Zend_Session::start();
    }
    
    protected function _initRoutes() {
        $this->bootstrap('frontController');
        $router = $this->frontController->getRouter();
        $router->setChainNameSeparator('/');
        
        $configPath = APPLICATION_PATH . '/configs/routes.ini';
        $config = new Zend_Config_Ini($configPath);
        $router->addConfig($config, 'routes');
    }

    protected function _initI18n() {
        // set up timezone
        date_default_timezone_set('America/Los_Angeles');

        // set up locale
        $locale = Zend_Locale::setDefault('en_US');
        Zend_Registry::set('Zend_Locale', $locale);
    }

    protected function _initLucene() {
        // allow numbers in searches (default is alpha only)
        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive();
        Zend_Search_Lucene_Analysis_Analyzer::setDefault($analyzer);

        // create search index
        $indexPath = DATA_PATH . '/lucene/index';
        try {
            $index = Zend_Search_Lucene::open($indexPath);
        } catch (Zend_Search_Lucene_Exception $e) {
            $index = Zend_Search_Lucene::create($indexPath);
        }
        Zend_Registry::set('searchIndex', $index);
    }
}

