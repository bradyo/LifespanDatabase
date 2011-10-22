<?php

class ObservationsController extends Zend_Controller_Action
{
    /**
     * @var $repository Application_Model_ObservationRepository 
     */
    private $repository;

    public function init() {
        $this->repository = Application_Registry::getEm()
            ->getRepository('Application_Model_Observation');
    }
    
    public function indexAction() {
    }
    
    public function getAction() {
        $id = $this->getRequest()->getParam('id');
        $observation = $this->repository->findOneBy(array('id' => $id));
        if (!$observation) {
            throw new Zend_Controller_Action_Exception('Observation not found.', 404);
        }
        $this->view->observation = $observation;
    }
    
    public function addAction() {
        $form = new Application_Form_ObservationForm();
    }
    
    public function editAction() {
    }
    
    public function postAction() {
        $observation = new Application_Model_Observation();
    }
    
    public function deleteAction() {
        $message = 'Delete operation not supported.';
        throw new Application_Exception_NotSupportedException($message);
    }
}
