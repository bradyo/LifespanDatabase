<?php

class Default_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    protected function _initRoutes() {
        $this->bootstrap('frontController');
        $router = $this->frontController->getRouter();
        $router->setChainNameSeparator('/');
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/modules/default/configs/routes.ini');
        $router->addConfig($config, 'routes');
    }
    
    protected function _initView() {
        if ($this->getModuleName() !== 'Default') {
            return;
        }
        
        $view = new Zend_View();
        
        $view->setScriptPath(APPLICATION_PATH . '/modules/default/views');
        $view->addHelperPath('Application/View/Helper', 'Application_View_Helper');

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

        $view->headTitle('Lifespan Observation Database');
        $view->headTitle()->setSeparator(' - ');
        
        // add view helper
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }
}
