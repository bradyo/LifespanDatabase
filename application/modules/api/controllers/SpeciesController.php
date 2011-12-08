<?php

use \Application\Model\SpeciesService;

class Api_SpeciesController extends Application_Controller_RestController
{   
    /**
     * @var Application\Model\SpeciesService
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
        $allSpecies = $this->speciesService->getAll();
        
        $response = array();
        $response['species'] = array();
        foreach ($allSpecies as $species) {
            $speciesData = $species->toArray();
            $speciesData['links'] = array(
                array(
                    'rel' => 'self',
                    'href' => $_SERVER['self'],
                ),
            );
            $response['species'] = $speciesData;
        }
        $response['count'] = count($species);
        $response['links'] = array(
            array(
                'rel' => 'next',
                'href' => '/species?start=' . $start . '&count=' . $count
            ),
            array(
                'rel' => 'prev',
                'href' => '/species?start=' . $start . '&count=' . $count
            )
        );
        
        $this->view->responseData = $responseData;
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function getAction() {
        $id = $this->getRequest()->getParam('id');
        
        $species = $this->speciesService->getSpecies($id);
        
        $responseData = $species->toArray();
        $responseData['_links'] = array(
            array(
                'rel' => 'edit',
                'href' => '/species/' . $i . '/edit',
            ),
            array(
                'rel' => 'delete',
                'href' => '/species/' . $i . '/delete',
            ),
        );
        $this->view->responseData = $responseData;
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
