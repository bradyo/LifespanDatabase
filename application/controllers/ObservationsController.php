<?php

class ObservationsController extends Zend_Controller_Action
{
    /**
     * @var $repository Application_Model_ObservationRepository 
     */
    private $repository;
    
    public function preDispatch() {
        $this->initContextSwitcher();
    }
    
    private function initContextSwitcher() {
        /* @var $contextSwitch Zend_Controller_Action_Helper_ContextSwitch */
        $contextSwitch = $this->getHelper('contextSwitch');
        $contextSwitch->setAutoJsonSerialization(true);

        $actions = array(
            'index',
            'get',
            'post',
            'put',
            'delete',
        );
        $contexts = array(
            'xml',
            'json',
        );
        foreach ($actions as $action) {
            foreach ($contexts as $context) {
                $contextSwitch->addActionContext($action, $context);
            }
        }
        $contextSwitch->initContext();
    }
    
    public function init() {
        $this->repository = Application_Registry::getEm()
            ->getRepository('Application_Model_Observation');
    }
    
    public function indexAction() {
    }
    
    public function getAction() {
        $id = $this->getRequest()->getParam('id');
        
        $observation = $this->repository->findOneBy(array('id' => $id));
        
        $this->view->observation = $observation;
        
        $this->getResponse()->setHttpResponseCode(200);
    }
    
    public function addAction() {
        // show form
    }
    
    public function postAction() {
        
    }
    
    public function putAction() {
        
    }
    
    public function deleteAction() {
        
        
        $this->getResponse()->setHttpResponseCode(204);
    }
}

