<?php

use Doctrine\ORM\EntityManager;

class Application_Service_ObservationService 
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    
    /**
     * @var Application_Model_User
     */
    private $user;
    
    
    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
    }
    
    public function setUser($user) {
        $this->user = $user;
    }
    
    public function index($params) {
    }
    
    public function get($id) {
        
    }
    
    public function put($id, $data) {
        
    } 
    
    public function post($data) {
        
    }
    
    public function delete($id) {
        
    }
}
