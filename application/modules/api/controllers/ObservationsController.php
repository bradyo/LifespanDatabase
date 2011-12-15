<?php

/**
index {html, xml, json}
show {html, xml, json}
add {html}
edit {html}
create {html(set flash, redirect to get/edit), xml, json}
update {html(set flash, redirect to get/edit), xml, json}
delete {html(set flash, redirect to index), xml, json}
 */

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
    
    public function init() {
        parent::init();
        
        $this->em = Application_Registry::getEm();
        $currentUser = Application_Registry::getCurrentUser();
        $this->observationService = new Application_Service_Observation($this->em, $currentUser);
    }
    
    public function indexAction() {
        $criteria = $this->getCriteria($this->_getAllParams());
        $orderBy = $this->_getParam('order', null);
        $limit = $this->_getParam('limit', self::DEFAULT_ITEMS_COUNT);
        $offset = $this->_getParam('offset', 0);
        $repo = $this->em->getRepository('Application\Model\Observation');
        $this->view->observations = $repo->findBy($criteria, $orderBy, $limit, $offset);
        
        $this->getResponse()->setHttpResponseCode(200);
    }
    
    private function getCriteria($params) {
        $criteria = array();
        
        
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
