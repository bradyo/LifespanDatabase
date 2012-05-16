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

				$sagewebDb = $resource->getDb('sagewebDb');
        Zend_Registry::set('sagewebDb', $sagewebDb);

				// set up doctrine
				$autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Doctrine');
        $autoloader->pushAutoloader(array('Doctrine', 'autoload'));
        $autoloader->pushAutoloader(array('Doctrine', 'modelsAutoload'), '');

				$manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);
        $manager->setCharset('utf8');
        $manager->setCollate('utf8_general_ci');

        Doctrine_Manager::connection($db->getConnection());
        Doctrine::loadModels(APPLICATION_PATH . "/models/orm");
    }

    protected function _initSessionData()
    {
        $this->bootstrap('databases');

        $db = Zend_Registry::get('sagewebDb');
        Zend_Db_Table_Abstract::setDefaultAdapter($db);
        
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

}

