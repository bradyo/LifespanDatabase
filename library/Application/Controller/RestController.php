<?php

abstract class Application_Controller_RestController extends Zend_Rest_Controller
{
    protected $contexts = array(
        'xml',
        'json',
    );
 
    protected $actions = array(
        'options',
        'head',
        'index',
        'get',
        'post',
        'put',
        'delete'
    );
    
    public function init() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        
        // initialize contexts
        $contextSwitch = $this->getHelper('restContextSwitch');
        $contextSwitch->setAutoSerialization(true);
        foreach ($this->contexts as $context) {
            foreach ($this->actions as $action) {
                $contextSwitch->addActionContext($action, $context);
            }
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
}
