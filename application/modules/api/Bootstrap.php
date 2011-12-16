<?php

class Api_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    protected function _initRoutes() {
        $front = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route($front, array(), array('api'));
        $front->getRouter()->addRoute('rest', $restRoute);
    }
    
    protected function _initView() {
        if ($this->getModuleName() !== 'Api') {
            return;
        }
        
        $view = new Zend_View();
        $view->setScriptPath(APPLICATION_PATH . '/modules/api/views');
        $view->addHelperPath('Application/View/Helper', 'Application_View_Helper');
        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');

        // add view helper
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
    }
    
    protected function _initPlugins() {
        if ($this->getModuleName() !== 'Api') {
            return;
        }
        
        // initialize rest controller plugin
        $restHandlerPlugin = new Application_Controller_Plugin_RestHandler();
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin($restHandlerPlugin);
    }
}
