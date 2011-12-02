<?php

use \Application\Model\SpeciesService;

class Api_SpeciesController extends Application_Controller_RestController
{   
    /**
     * @var Application\Service\SpeciesService
     */
    private $speciesService;

    public function init() {
        parent::init();
        
        // set up service
        $em = Application_Registry::getEm();
        $user = Application_Registry::getCurrentUser();
        $this->speciesService = new SpeciesService($user, $em);
    }
    
    public function indexAction() {
        $this->view->species = $this->speciesService->getAll();
        $this->view->count = count($this->view->species);
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function getAction() {
        $id = $this->getRequest()->getParam('id');
        $this->view->species = $this->speciesService->get($id);
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function postAction() {
        $postData = $this->getRequest()->getParams();
        $this->view->species = $this->speciesService->create($postData);
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function putAction() {
        $id = $this->getRequest()->getParam('id');
        $postData = $this->getRequest()->getParams();
        $this->view->species = $this->speciesService->update($id, $postData);
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        $this->speciesService->delete($id);
        $this->getResponse()->setHttpResponseCode(200);
    }
}
