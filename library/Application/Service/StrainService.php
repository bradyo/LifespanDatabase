<?php

class Application_Service_StrainService
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
        // filter input data
        $filteredData = $this->filter($data);
        
        // validate input data
        if (! $this->isValidData($filteredData)) {
            throw new Application_Exception_ValidateException('Invalid data');
        }
        
        // create entity
        $species = new Application_Model_Strain();
        $species->setGuid(Application_Guid::generate());
        $species->fromArray($filteredData);
        $this->em->persist($species);
        $this->em->flush();

        return $species;
    }   
    
    public function update($id, $data) {
        // check authorization
        if ( ! $this->user->isModerator()) {
            throw new Application_Exception_UnauthorizedException('Permission denied');
        }
        
        // filter input data
        $filteredData = $this->filter($data);
        
        // validate input data
        if (! $this->isValidData($filteredData)) {
            throw new Application_Exception_ValidateException('Invalid data');
        }
        
        // update entity
        $repo = $this->em->getRepository('Application_Model_Strain');
        $strain = $repo->find($id);
        if (!$strain) {
            throw new Exception('Species not found');
        }
        $strain->fromArray($filteredData);
        $this->em->persist($strain);
        $this->em->flush();
        
        return $strain;
    }
    
    public function delete($id) {
        // check authorization
        if ( ! $this->user->isAdmin()) {
            throw new Application_Exception_UnauthorizedException('Permission denied');
        }
        
        // delete entity
        $repo = $this->em->getRepository('Application_Model_Strain');
        $strain = $repo->find($id);
        if (!$strain) {
            throw new Exception('Strain not found');
        }
        $this->em->remove($strain);
        $this->em->flush();
    }
    
    public function get($id) {
        $repo = $this->em->getRepository('Application_Model_Strain');
        $item = $repo->find($id);
        if (!$item) {
            throw new Exception('Strain not found');
        }
        return $item->toArray();        
    }
    
    public function getAll() {
        $repo = $this->em->getRepository('Application_Model_Strain');
        $items = $repo->findAll();
        $data = array();
        foreach ($items as $item) {
            $data[] = $item->toArray();
        }
        return $data;     
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
        
        if ( ! isset($data['id'])) {
            $data['id'] = null;
        }
        
        if (empty($data['name'])) {
            $message = 'Species name cannot be empty';
            $this->validationErrors['name'] = $message;
        }

        $repo = $this->em->getRepository('Application_Model_Strain');
        if ($repo->nameExists($data['name'], $data['id'])) {
            $message = 'Strain name "'. $data['name'] . '" already exists';
            $this->validationErrors['name'] = $message;
        }
        
        $isValid = (count($this->validationErrors) == 0);
        return $isValid;
    }
    
    public function getValidationErrors() {
        return $this->validationErrors;
    }
}
