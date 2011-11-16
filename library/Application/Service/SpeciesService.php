<?php

class Application_Service_SpeciesService 
{
    /**
     * @var Application_Model_User User of the service (used in authorization)
     */
    private $user;
    
    /**
     * @var \Doctrine\ORM\EntityManager 
     */
    private $em;
    
    /**
     * @var array validation error messages
     */
    private $validationErrors;
    
    
    /**
     * @param Application_Model_User $user
     * @param \Doctrine\ORM\EntityManager $em 
     */
    public function __construct($user, $em) {
        $this->user = $user;
        $this->em = $em;
    }
    
    public function create($data) {
        // check authorization
        if ( ! $this->user->isAdmin()) {
            throw new Application_Exception_UnauthorizedException('Permission denied');
        }
        
        // filter input data
        $filteredData = $this->filter($data);
        
        var_dump($filteredData);
        
        // validate input data
        if (! $this->isValidData($filteredData)) {
            throw new Application_Exception_ValidateException('Invalid data');
        }
        
        // create entity
        $species = new Application_Model_Species();
        $species->setGuid(Application_Guid::generate());
        $species->fromArray($filteredData);
        
        foreach ($species->getSynonyms() as $synonym) {
            /* @var $synonym Application_Model_SpeciesSynonym */
            $synonym->setSpecies($species);
            $this->em->persist($synonym);
        }
        
        $this->em->persist($species);
        $this->em->flush();
        
        var_dump($species);
        
        return $species;
    }   
    
    public function update($id, $data) {
        // check authorization
        if ( ! $this->user->isAdmin()) {
            throw new Application_Exception_UnauthorizedException('Permission denied');
        }
        
        // filter input data
        $filteredData = $this->filter($data);
        
        // validate input data
        if (! $this->isValidData($filteredData)) {
            throw new Application_Exception_ValidateException('Invalid data');
        }
        
        // update entity
        $repo = $this->em->getRepository('Application_Model_Species');
        $species = $repo->find($id);
        if (!$species) {
            throw new Exception('Species not found');
        }
        $species->fromArray($filteredData);
        foreach ($species->getSynonyms() as $synonym) {
            /* @var $synonym Application_Model_SpeciesSynonym */
            $synonym->setSpecies($species);
            $this->em->persist($synonym);
        }
        $this->em->persist($species);
        $this->em->flush();
        
        return $species;
    }
    
    public function delete($id) {
        // check authorization
        if ( ! $this->user->isAdmin()) {
            throw new Exception('Permission denied');
        }
        
        // fetch entity
        $repo = $this->em->getRepository('Application_Model_Species');
        $species = $repo->find($id);
        if (!$species) {
            throw new Exception('Species not found');
        }
        
        $this->em->remove($species);
        $this->em->flush();
    }
    
    private function filter($data) {
        $filteredData = array();
        foreach ($data as $key => $value) {
            if ($key == 'name') {
                $filter = new Zend_Filter_StringTrim();
                $filteredData[$key] = $filter->filter($value);
            } else {
                $filteredData[$key] = $value;
            }
        }
        return $filteredData;
    }
    
    private function isValidData($data) {
        $this->validationErrors = array();
        
        if (empty($data['name'])) {
            $message = 'Species name cannot be empty';
            $this->validationErrors['name'] = $message;
        }
        if ($this->nameExists($data['name'])) {
            $message = 'Species name "'. $data['name'] . '" already exists';
            $this->validationErrors['name'] = $message;
        }
        
        $isValid = (count($this->validationErrors) == 0);
        return $isValid;
    }
    
    private function nameExists($name) {
        $query = $this->em->createQuery('
            SELECT s FROM Application_Model_Species s WHERE s.name = :name
            ');
        $query->setParameter('name', $name);
        $species = $query->getResult();
        return (count($species) > 0);
    }
    
    public function getValidationErrors() {
        return $this->validationErrors;
    }
}
