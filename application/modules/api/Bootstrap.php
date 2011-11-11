<?php

class Api_Bootstrap extends Zend_Application_Module_Bootstrap 
{
    protected function _initRoutes() {
        $front = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route($front, array(), array('api'));
        $front->getRouter()->addRoute('rest', $restRoute);
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
