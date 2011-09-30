<?php

class Application_Service_ObservationService 
{
    /**
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
}
