<?php

namespace Application\Model;

use Doctrine\ORM\EntityRepository;

class ObservationRepository extends EntityRepository 
{
    /**
     * @var Zend_Search_Lucene_Interface
     */
    private $searchIndex;
    
    
    public function __construct($em, $searchIndex, Mapping\ClassMetadata $class) {
        parent::__construct($em, $class);
        $this->searchIndex = $searchIndex;
    }
    
    public function getCurrent($criteria, $orderBy, $limit, $offset) {
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
        
        // add fulltext search criteria if given
        if (isset($criteria['search']) && !empty($criteria['search'])) {
            $searchMatchIds = $this->searchService->getMatchIds($criteria['search']);
            $dql .= ' AND observation.id IN (' . implode(', ', $searchMatchIds) . ')';
        }
        
        $query = $this->getEntityManager()->createQuery($dql);
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
}
