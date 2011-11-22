<?php

abstract class Application_Controller_RestController extends Zend_Rest_Controller
{
    public function init() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        
        // initialize contexts       
        $contextSwitch = $this->getHelper('contextSwitch');
        $contextSwitch->setContexts(
            array( 
                'json' => array(
                    'headers' => array(
                        'Content-Type' => 'application/json',
                        'Content-Charset' => 'utf-8',
                    ),
                    'callbacks' => array(
                        'post' => array(&$this, 'renderJson')
                    ),
                ),
                'xml' => array(
                    'headers' => array(
                        'Content-Type' => 'text/xml',
                        'Content-Charset' => 'utf-8',
                    ),
                    'callbacks' => array(
                        'post' => array(&$this, 'renderXml')
                    ),
                ),
                'text' => array(
                    'headers' => array(
                        'Content-Type' => 'text/plain',
                        'Content-Charset' => 'utf-8',
                    ),
                    'callbacks' => array(
                        'post' => array(&$this, 'renderText')
                    )
                )
            )
        );
        
        // add contexts
        $actions = array('options', 'head', 'index', 'get', 'post', 'put', 'delete');
        foreach ($actions as $action) {
            $contextSwitch->addActionContext($action, array('xml', 'json', 'text'));
        }
        $contextSwitch->initContext();
    }
    
    public function optionsAction() {
        $this->view->message = 'Resource Options';
        $this->getResponse()->setHttpResponseCode(200);
    }
    
    public function headAction() {
        $this->getResponse()->setHttpResponseCode(200);
    }
    
    public function indexAction() {
        $this->getResponse()->setBody('List of Resources');
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function getAction() {
        $this->getResponse()->setBody(sprintf('Resource #%s', $this->_getParam('id')));
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function postAction() {
        $this->getResponse()->setBody('Resource Created');
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function putAction() {
        $response = $this->getResponse();
        $response->setBody(sprintf('Resource #%s Updated', $this->_getParam('id')));
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function deleteAction() {
        $response = $this->getResponse();
        $response->setBody(sprintf('Resource #%s Deleted', $this->_getParam('id')));
        $response->setHttpResponseCode(200);
    }
    
    public function renderXml() {
        $data = $this->view->getVars();
        if (count($data) !== 0) {
            $serializer = new Application_Serializer_Adapter_Xml();
            $body = $serializer->serialize($data);
            $this->getResponse()->setBody($body);
        }
    }
    
    public function renderJson() {
        $data = $this->view->getVars();
        if (count($data) !== 0) {
            $serializer = new Zend_Serializer_Adapter_Json();
            $body = $serializer->serialize($data);

            $callback = $this->getRequest()->getParam('jsonp-callback', false);
            if ($callback !== false and !empty($callback)) {
                $body = sprintf('%s(%s)', $callback, $body);
            }
            $this->getResponse()->setBody($body);
        }
    }
    
    public function renderText() {
        $data = $this->view->getVars();
        if (count($data) !== 0) {
            $serializer = new Zend_Serializer_Adapter_Json();
            $body = $serializer->serialize($data);
            $this->getResponse()->setBody($body);
        }
    }
}
