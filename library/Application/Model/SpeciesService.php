<?php

namespace Application\Model;

use Application\AuthorizationException;
use Application\ValidateException;
use Application\Util\Guid;

class SpeciesService 
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
    
    public function getValidationErrors() {
        return $this->validationErrors;
    }
    
    
    public function create($data) {
        $this->authorizeMember();
        $cleanData = $this->filter($data);
        $this->validate($cleanData);
        
        $species = new Species();
        $species->fromArray($cleanData);
        $species->setGuid(Guid::generate());
        $species->setStatus(Species::STATUS_PENDING);
        if (isset($cleanData['synonyms'])) {
            foreach ($cleanData['synonyms'] as $synonymData) {
                $synonym = new SpeciesSynonym();
                $synonym->fromArray($synonymData);
                $species->addSynonym($synonym);
            }
        }
        $this->em->persist($species);
        $this->em->flush();

        return $species;
    }
        
    public function update($id, $data) {
        $this->authorizeAdmin();
        $cleanData = $this->filter($data);
        $this->validate($cleanData);
        
        // update entity
        $species = $this->getSpecies($id);
        $species->fromArray($cleanData);
        if (isset($cleanData['synonyms'])) {
            foreach ($species->getSynonyms() as $synonym) {
                $species->getSynonyms()->removeElement($synonym);
                $this->em->remove($synonym);
            }
            foreach ($cleanData['synonyms'] as $synonymData) {
                $synonym = new SpeciesSynonym();
                $synonym->fromArray($synonymData);
                $species->addSynonym($synonym);
            }
        }
        $this->em->persist($species);
        $this->em->flush();
        
        return $species;
    }
    
    public function delete($id) {
        $this->authorizeAdmin();
        $species = $this->getSpecies($id);
        $this->em->remove($species);
        $this->em->flush();
    }

    private function authorizeAdmin() {
        if ( ! $this->user->isAdmin() || $this->user->isBlocked()) {
            throw new AuthorizationException('Permission denied');
        }
    }

    private function authorizeMember() {
        if ($this->user->isGuest() || $this->user->isBlocked()) {
            throw new AuthorizationException('Permission denied');
        }
    }
    
    private function getSpecies($id, $expandRelations = array()) {
        $repo = $this->em->getRepository('Application\Model\Species');
        $species = $repo->find($id);
        if (!$species) {
            throw new \Exception('Species not found');
        }
        return $species;
    }
        
    private function validate($data) {
        // dis-allow any extra fields in data
        $extraFields = array();
        $allowedFields = $this->getAllowedFields();
        foreach (array_keys($data) as $field) {
            if (! in_array($field, $allowedFields)) {
                $extraFields[] = $field;
            }
        }
        if (count($extraFields) > 0) {
            $msg = 'The following fields are not allowed: ' . join(', ', $extraFields);
            $this->validationErrors['extraFields'] = $msg;
        }
        
        $this->validationErrors = array();
        if (empty($data['name'])) {
            $this->validationErrors['name'] = 'Species name cannot be empty';
        }

        $repo = $this->em->getRepository('Application\Model\Species');
        $id = (isset($data['id'])) ? $data['id'] : null;
        if ($repo->nameExists($data['name'], $id)) {
            $message = 'Species name "'. $data['name'] . '" already exists';
            $this->validationErrors['name'] = $message;
        }
        
        if (! $this->user->isModerator() && isset($data['status'])) {
            $this->validationErrors['status'] = 'Only moderators can set status field';
        }
        
        if (count($this->validationErrors) > 0) {
            throw new ValidateException('Supplied data is invalid');
        }
    }
    
    private function getAllowedFields() {
        $fields = array(
            'status',
            'name',
            'commonName',
            'ncbiTaxonId',
            'synonyms',
        );
        if (! $this->user->isAdmin()) {
            unset($fields['status']);
        }
        return $fields;
    }

    private function filter($data) {
        // ID and GUID should be created and set automatically, not by user data
        unset($data['id']);
        unset($data['guid']);
        
        $cleanData = $data;
        foreach ($cleanData as $key => $value) {
            if ($key == 'name') {
                $filter = new \Zend_Filter_StringTrim();
                $cleanData[$key] = $filter->filter($value);
            } else {
                $cleanData[$key] = $value;
            }
        }
        return $cleanData;
    }
}
