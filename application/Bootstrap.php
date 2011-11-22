<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoloader() {
        $loader = Zend_Loader_Autoloader::getInstance();
        $loader->registerNamespace('Doctrine');
        $loader->registerNamespace('Application');
        $loader->registerNamespace('Test');
    }
    
    protected function _initHtmlPurifier() {
        require_once 'HTMLPurifier/Bootstrap.php';
        spl_autoload_register(array('HTMLPurifier_Bootstrap', 'autoload'));
    }
    
    protected function _initLayout() {
        Zend_Layout::startMvc();
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayoutPath(APPLICATION_PATH . '/layouts');
        $layout->setLayout('main');
        $layout->startMvc();
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

    protected function _initDatabases() {
        $this->bootstrap('multidb');
        $resource = $this->getPluginResource('multidb');

        /* @var $db Zend_Db_Adapter_Abstract */
        $db = $resource->getDb('db');
        Zend_Registry::set('db', $db);

        $ncbiDb = $resource->getDb('ncbiDb');
        Zend_Registry::set('ncbiDb', $ncbiDb);

        $ppodDb = $resource->getDb('ppodDb');
        Zend_Registry::set('ppodDb', $ppodDb);
        
        // set up doctrine with database settings
        $config = new \Doctrine\ORM\Configuration;
        $config->setProxyDir(DATA_PATH . '/cache/doctrine/proxies');
        $config->setProxyNamespace('Proxies');
        $config->setAutoGenerateProxyClasses(true);
        
        $driver = $config->newDefaultAnnotationDriver(BASE_PATH . '/library/Application/Model');
        $config->setMetadataDriverImpl($driver);
        
        $cache = new \Doctrine\Common\Cache\ArrayCache;
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        
        $connectionParams = $db->getConfig();
        $connectionParams['user'] = 'root';
        $connectionParams['driver'] = 'pdo_mysql';
        $em = \Doctrine\ORM\EntityManager::create($connectionParams, $config);
        Zend_Registry::set('em', $em);
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

    protected function _initI18n() {
        // set up timezone
        date_default_timezone_set('UTC');

        // set up locale
        $locale = Zend_Locale::setDefault('en_US');
        Zend_Registry::set('Zend_Locale', $locale);
    }

    protected function _initLucene() {
        // allow numbers in searches (default is alpha only)
        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive();
        Zend_Search_Lucene_Analysis_Analyzer::setDefault($analyzer);

        // create search index
        $indexPath = DATA_PATH . '/lucene';
        try {
            $index = Zend_Search_Lucene::open($indexPath);
        } catch (Zend_Search_Lucene_Exception $e) {
            $index = Zend_Search_Lucene::create($indexPath);
        }
        Zend_Registry::set('searchIndex', $index);
    }
}

