<?php

class Application_Service_SpeciesService 
{
    /**
     * @var array validation error messages
     */
    private $validationErrors;
    
    private function validateData($data) {
                

    }
    
    
    
    /**
     *
     * @param Application_Model_User $user
     * @param \Doctrine\ORM\EntityManager $em 
     */
    public function __construct($username, $password, $em) {
        $this->user = $user;
        $this->em = $em;
    }
    
    public function create($data) {
        if ($this->user->isAdmin)
        
        $filteredData = $this->filter($data);
        if (! $this->getValidator()->isValid($filteredData)) {
            throw new Exception('Invalid data');
        }
        
        $species = new Application_Model_Species();
        $species->setGuid(Application_Guid::generate());
        $species->fromArray($filteredData);
        
        $this->em->persist($species);
        $this->em->flush();
        
        return $species;
    }
    
    public function update($id, $data) {
        $repo = $this->em->getRepository('Application_Model_Species');
        $species = $repo->find($id);
        if (!$species) {
            throw new Exception('Species not found');
        }
        
        $filteredData = $this->filter($data);
        if (! $this->isValid($filteredData)) {
            throw new Exception('Invalid data');
        }
        
        $species->fromArray($filteredData);
        $this->em->persist($species);
        $this->em->flush();
        
        return $species;
    }
    
    private function populateFromArray($species, $data) {
        
        $species->setName($data['name']);
        $species->setCommonName($data['commonName']);
        $species->setNcbiTaxonId($data['ncbiTaxonId']);
    }
    
    public function delete($id) {
        
    }
    
    private function filter($data) {
        $filteredData = array();
        foreach ($data as $key => $value) {
            if ($key == 'name') {
                $filter = new Zend_Filter_StringTrim();
                $filteredData['key'] = $filter->filter($value);
            }
        }
        return $filteredData;
    }
    
    private function isValid($data) {
        return new Zend_Validate();
    }
}
