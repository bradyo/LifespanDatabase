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
     * @var Zend_Search_Lucene_Interface
     */
    private $searchIndex;
    
    /**
     * @var array validation error messages
     */
    private $validationErrors;
    
    /**
     * @param Application_Model_User $user
     * @param \Doctrine\ORM\EntityManager $em 
     */
    public function __construct($user, $em, $searchIndex) {
        $this->user = $user;
        $this->em = $em;
        $this->searchIndex = $searchIndex;
    }
    
    public function getValidationErrors() {
        return $this->validationErrors;
    }
    
    public function getCurrent($criteria, $orderBy, $limit, $offset) {
        
        // add fulltext search criteria if given
        $searchIds = array();
        if (isset($criteria['search']) && !empty($criteria['search'])) {
            $searchIds = $this->getMatchIds($criteria['search']);
            if (count($searchIds) === 0) {
                return array();
            }
        }

        // build search query
        $dql = '
            SELECT observation
            FROM Application\Model\Observation observation
            WHERE observation.reviewedAt = (
                SELECT MAX(subsetObservation.reviewedAt)
                FROM Application\Model\Observation subsetObservation
                WHERE observation.publicId = subsetObservation.publicId
                    AND subsetObservation.reviewedAt < :maxReviewedAt
                GROUP BY subsetObservation.publicId
            )
            AND observation.status = :status 
            AND observation.reviewStatus = :reviewStatus
            ';
        if (count($searchIds) > 0) {
            $dql .= ' AND observation.id IN (' . implode(', ', $searchIds) . ')';
        }
        $query = $this->em->createQuery($dql);
        $query->setParameter('status', Observation::STATUS_PUBLIC);
        $query->setParameter('reviewStatus', Observation::REVIEW_STATUS_ACCEPTED);
        $query->setParameter('maxReviewedAt', '2011-08-03');
        return $query->getResult();
    }
    
    private function getMatchIds($searchQuery) {
        $matchIds = array();
        $hits = $this->searchIndex->find($searchQuery);
        foreach ($hits as $hit) {
            /* @var $hit Zend_Search_Lucene_Search_QueryHit */
            $matchIds[] = $hit->id;
        }
        return $matchIds;
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
        
        // update search index
        $doc = $this->getIndexDocument($observation);
        $this->searchIndex->addDocument($doc);
        $this->searchIndex->commit();

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
        
        // update search index document
        $this->updateObservationIndex($observation);
        
        return $observation;
    }
    
    public function rebuildObservationsIndex() {
        // delete existing documents in index
        $docsCount = $this->searchIndex->count();
        for ($i = 0; $i < $docsCount; $i++) {
            $this->searchIndex->delete($i);
        }
        $this->searchIndex->commit();
        
        $repo = $this->em->getRepository('Application\Model\Observation');
        $observations = $repo->findAll();
        foreach ($observations as $observation) {
            $this->updateObservationIndex($observation);
        }
        $this->searchIndex->optimize();
    }
    
    private function updateObservationIndex($observation) {
        // update search index document
        $existingHits = $this->searchIndex->find('id:' . $observation->getId());
        foreach ($existingHits as $hit) {
            $this->searchIndex->delete($hit->id);
        }
        $doc = $this->getIndexDocument($observation);
        $this->searchIndex->addDocument($doc);
        $this->searchIndex->commit();
    }
    
    /**
     * @param Observation $observation 
     */
    private function getIndexDocument($observation) {
        $fields = array();
        $fields[] = $observation->getDescription();
        $fields[] = $observation->getCellType();
        $fields[] = $observation->getMatingType();
        
        if ($observation->getCitation() !== null) {
            /* @var $citation Citation */
            $citation = $observation->getCitation();
            $fields[] = $citation->getAuthors();
            $fields[] = $citation->getTitle();
        }
        
        if ($observation->getSpecies() !== null) {
            /* @var $species Species */
            $species = $observation->getSpecies();
            $fields[] = $species->getName();
            $fields[] = $species->getCommonName();
            foreach ($species->getSynonyms() as $synonym) {
                /* @var $synonym SpeciesSynonym */
                $fields[] = $synonym->getName();
            }
        }
        
        if ($observation->getStrain() !== null) {
            /* @var $strain Strain */
            $strain = $observation->getStrain();
            $fields[] = $strain->getName();
        }

        foreach ($observation->getGeneInterventions() as $geneIntervention) {
            /* @var $geneIntervention GeneIntervention */
            $fields[] = $geneIntervention->getAllele();
            $fields[] = $geneIntervention->getGene()->getSymbol();
            $fields[] = $geneIntervention->getGene()->getLocusTag();
            foreach ($geneIntervention->getGene()->getSynonyms() as $synonym) {
                /* @var $synonym GeneSynonym */
                $fields[] = $synonym->getName();
            }
        }
        
        foreach ($observation->getCompoundInterventions() as $compoundIntervention) {
            /* @var $compoundIntervention CompoundIntervention */
            $fields[] = $compoundIntervention->getGene()->getSymbol();
            $fields[] = $compoundIntervention->getGene()->getLocusTag();
            foreach ($compoundIntervention->getCompound()->getSynonyms() as $synonym) {
                /* @var $synonym CompoundSynonym */
                $fields[] = $synonym->getName();
            }
        }
        
        foreach ($observation->getEnvironmentInterventions() as $envIntervention) {
            /* @var $envIntervention EnvironmentIntervention */
            $fields[] = $envIntervention->getDescription();
            $fields[] = $envIntervention->getEnvironment()->getName();
        }
        
        $doc = new \Zend_Search_Lucene_Document();
        $body = join(' ', $fields);
        $doc->addField(\Zend_Search_Lucene_Field::text('id', $observation->getId()));
        $doc->addField(\Zend_Search_Lucene_Field::unStored('body', $body));
        return $doc;
    }
    
    public function delete($id) {
        $this->authorizeAdmin();
        $observation = $this->getObservation($id);
        
        // remove index
        try {
            $this->searchIndex->delete($observation->getId());
        } catch (Exception $e) {
            // ignore - no existing index doc for this item
        }
        
        $this->em->remove($observation);
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
    
    /**
     * @param integer $id
     * @return Observation
     */
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
