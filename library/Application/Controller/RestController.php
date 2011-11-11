<?php

abstract class Application_Controller_RestController extends Zend_Rest_Controller
{
    protected $_contexts = array(
        'xml',
        'json',
    );
 
    protected $_actions = array(
        'index',
        'get',
        'post',
        'put',
        'delete',
        'error'
    );
    
    public function init() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout()->disableLayout();
        
        // initialize contexts
        $contextSwitch = $this->getHelper('restContextSwitch');
        $contextSwitch->setAutoSerialization(true);
        foreach ($this->_contexts as $context) {
            foreach ($this->_actions as $action) {
                $contextSwitch->addActionContext($action, $context);
            }
        }
        $contextSwitch->initContext();
    }
    
    public function indexAction()
    {
        $this->getResponse()->setBody('List of Resources');
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function getAction()
    {
        $this->getResponse()->setBody(sprintf('Resource #%s', $this->_getParam('id')));
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function postAction()
    {
        $this->getResponse()->setBody('Resource Created');
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function putAction()
    {
        $this->getResponse()->setBody(sprintf('Resource #%s Updated', $this->_getParam('id')));
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function deleteAction()
    {
        $this->getResponse()->setBody(sprintf('Resource #%s Deleted', $this->_getParam('id')));
        $this->getResponse()->setHttpResponseCode(200);
    }
}
