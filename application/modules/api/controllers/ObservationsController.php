<?php

use Application\Model\ObservationService;

class Api_ObservationsController extends Application_Controller_RestController
{   
    const DEFAULT_ITEMS_COUNT = 10;
    
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;
    
    /**
     * @var Application_Service_ObservationService
     */
    private $observationService;
    
    /**
     * @var Zend_Search_Lucene_Interface
     */
    private $searchIndex;
    
    public function init() {
        parent::init();
        
        $this->em = Application_Registry::getEm();
        $this->searchIndex = Application_Registry::getSearchIndex();
        $currentUser = Application_Registry::getCurrentUser();
        $this->observationService = new ObservationService($currentUser, $this->em, $this->searchIndex);
    }
    
    public function indexAction() {
        $criteria = $this->getCriteria($this->_getAllParams());
        $orderBy = $this->_getParam('order', null);
        $limit = $this->_getParam('limit', self::DEFAULT_ITEMS_COUNT);
        $offset = $this->_getParam('offset', 0);
        $observations = $this->observationService->getCurrent($criteria, $orderBy, $limit, $offset);

        $data = $this->getObservationsJsonData($observations);
        $body = Zend_Json::encode($data);
        $this->getResponse()->setBody($body);
    }
    
    private function getObservationsJsonData($observations) {
        $data = array();
        foreach ($observations as $observation) {
            $data[] = $this->getObservationJsonData($observation);
        }
        return $data;
    }
    
    private function getObservationJsonData($observation) {
        $data = $observation->toArray();
        return $data;
    }
    
    private function getCriteria($params) {
        $criteria = array();
        if (isset($params['search'])) {
            $criteria['search'] = $params['search'];
        }
        return $criteria;
    }
    
    private function getObservationsData($observations) {
        $data = array();
        foreach ($observations as $observation) {
            $data[] = $this->getObservationData($observation);
        }
        return $data;
    }
    
    private function getObservationData($observation) {
        return $observation->toArray();
    }

    public function getAction() {
        $id = $this->_getParam('id');
        
        $observation = $this->observationRepository->find($id);
        if ( ! $observation) {
            
        }
        
        $data = array();
        
        $this->view->resource = new stdClass;
        $this->view->resource->name = "hello";
        
        
        
        $format = $this->getRequest()->getParam('format');
        $json = Zend_Json_Encoder::encode(array('hello' => 'world', 'format' => $format));
        $this->getResponse()->setBody($json);
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function postAction() {
        $this->view->message = 'Resource Created';
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function putAction() {
        $this->view->message = sprintf('Resource #%s Updated', $this->_getParam('id'));
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function deleteAction() {
        $this->view->message = sprintf('Resource #%s Deleted', $this->_getParam('id'));
        $this->getResponse()->setHttpResponseCode(200);
    }
}
