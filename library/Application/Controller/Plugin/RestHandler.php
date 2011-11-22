<?php

class Application_Controller_Plugin_RestHandler extends Zend_Controller_Plugin_Abstract
{   
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        // convert request body to param array
        $rawBody = $request->getRawBody();
        $contentType = $request->getHeader('Content-Type');
        try {
            switch ($contentType) {
                case 'application/json':
                    $params = Zend_Json::decode($rawBody);
                    break;
                case 'application/xml':
                    $json = Zend_Json::fromXml($rawBody);
                    $params = Zend_Json::decode($json, Zend_Json::TYPE_OBJECT)->request;
                    break;
                default:
                    $params = $rawBody;
                    break;
            }
            $request->setParams((array) $params);
        } 
        catch (Exception $e) {
            $this->view->message = $e->getMessage();
            $this->getResponse()->setHttpResponseCode(400);

            $request->setControllerName('error');
            $request->setActionName('error');
            $request->setParam('error', $error);

            $request->setDispatched(true);
            return;
        }
    }
    
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        if ($this->getRequest()->getParam('format')) {
            return;
        }
        
        $this->getResponse()->setHeader('Vary', 'Accept');
        $mimeType = strtolower(str_replace(' ', '', $request->getHeader('Accept')));
        switch ($mimeType) {
            case 'application/xml':
                $request->setParam('format', 'xml');
                break;
            case 'application/json':
                $request->setParam('format', 'json');
                break;
            default:
                $request->setParam('format', 'text');
                break;
        }
    }
}
    