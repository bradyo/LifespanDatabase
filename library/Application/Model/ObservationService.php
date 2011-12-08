<?php

namespace Application\Model;

use Application\AuthorizationException;
use Application\ValidateException;
use Application\Util\Guid;

class ObservationService 
{
    /**
     * @var User User of the service (used in authorization)
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
        $filteredData = $this->filter($data);
        if (! $this->isValidData($filteredData)) {
            throw new ValidateException('Invalid data');
        }
        
        // create entity
        $observation = new Observation();
        $observation->fromArray($filteredData);
        $observation->setGuid(Guid::generate());
        
        $author = $this->em->getReference('Application\Model\User', $filteredData['authorId']);
        $observation->setAuthor($author);
        
        if (isset($filteredData['reviewerId'])) {
            $reviewer = $this->em->getReference('Application\Model\User', $filteredData['reviewerId']);
            $observation->setReviewer($reviewer);
        }
        
        $this->em->persist($observation);
        $this->em->flush();

        return $observation;
    }
    
    public function update($id, $data) {
        if ( ! $this->user->isModerator()) {
            throw new AuthorizationException('Permission denied');
        }

        $filteredData = $this->filter($data);
        if (! $this->isValidData($filteredData)) {
            throw new ValidateException('Invalid data');
        }
        
        // update entity
        $repo = $this->em->getRepository('Application\Model\Observation');
        $observation = $repo->find($id);
        if (!$observation) {
            throw new \Exception('Observation not found');
        }
        $observation->fromArray($filteredData);
        $this->em->persist($observation);
        $this->em->flush();
        
        return $observation;
    }
    
    public function delete($id) {
        if ( ! $this->user->isAdmin()) {
            throw new AuthorizationException('Permission denied');
        }

        // delete entity
        $repo = $this->em->getRepository('Application\Model\Observation');
        $observation = $repo->find($id);
        if (!$observation) {
            throw new \Exception('Observation not found');
        }
        $this->em->remove($observation);
        $this->em->flush();
    }
    
    public function get($id) {
        $repo = $this->em->getRepository('Application\Model\Observation');
        $observation = $repo->find($id);
        if (!$observation) {
            throw new \Exception('Observation not found');
        }
        return $observation->toArray();        
    }
    
    public function getAll() {
        $repo = $this->em->getRepository('Application\Model\Observation');
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
            if ($key == 'lifespan') {
                $filteredData[$key] = (double) $value;
            } else {
                $filteredData[$key] = $value;
            }
        }
        if ( ! isset($filteredData['id'])) {
            $filteredData['id'] = null;
        }
        return $filteredData;
    }
    
    private function isValidData($data) {
        $this->validationErrors = array();
        if (! empty($data['lifespan']) && $data['lifespan'] < 0) {
            $message = 'Lifespan cannot be negative';
            $this->validationErrors['lifespan'] = $message;
        }

        $isValid = (count($this->validationErrors) == 0);
        return $isValid;
    }
    
    public function getValidationErrors() {
        return $this->validationErrors;
    }
}
