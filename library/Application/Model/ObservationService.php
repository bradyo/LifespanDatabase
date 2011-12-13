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
        
        // create entity
        $observation = new Observation();
        $observation->fromArray($cleanData);
        $observation->setGuid(Guid::generate());
        
        $author = $this->em->getReference('Application\Model\User', $cleanData['authorId']);
        $observation->setAuthor($author);
        
        if (isset($cleanData['reviewerId'])) {
            $reviewer = $this->em->getReference('Application\Model\User', $cleanData['reviewerId']);
            $observation->setReviewer($reviewer);
        }
        
        if (isset($cleanData['geneInterventions'])) {
            foreach ($cleanData['geneInterventions'] as $geneInterventionData) {
                $geneIntervention = $this->createGeneIntervention($geneInterventionData);
                $observation->addGeneIntervention($geneIntervention);
            }
        }
        
        $this->em->persist($observation);
        $this->em->flush();

        return $observation;
    }
        
    public function update($id, $data) {
        $this->authorizeAdmin();
        $cleanData = $this->filter($data);
        $this->validate($cleanData);
        
        // update entity
        $observation = $this->getObservation($id);
        $observation->fromArray($cleanData);
        $observation->getGeneInterventions()->clear();
        if (isset($cleanData['geneInterventions'])) {
            foreach ($cleanData['geneInterventions'] as $geneInterventionData) {
                $geneIntervention = $this->createGeneIntervention($geneInterventionData);
                $observation->addGeneIntervention($geneIntervention);
            }
        }
        $this->em->persist($observation);
        $this->em->flush();
        
        return $observation;
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
    
    private function createGeneIntervention($data) {
        $geneIntervention = new GeneIntervention();
        $geneIntervention->fromArray($data);
        $gene = $this->em->getReference('Application\Model\Gene', $data['geneId']);
        $geneIntervention->setGene($gene);
        return $geneIntervention;
    }
    
    private function getObservation($id) {
        $repo = $this->em->getRepository('Application\Model\Observation');
        $observation = $repo->find($id);
        if (!$observation) {
            throw new \Exception('Observation not found');
        }
        return $observation;
    }
        
    private function validate($data) {
        return $data;
    }

    private function filter($data) {
        $cleanData = $data;
        // TODO: filter
        
        return $cleanData;
    }
}
