<?php

namespace Application\Model;

use Doctrine\ORM\EntityRepository;

class ObservationRepository extends EntityRepository 
{
    /**
     * @var Application_Service_SearchService
     */
    private $searchService;
    
    
    public function __construct($em, Mapping\ClassMetadata $class) {
        parent::__construct($em, $class);
        $this->searchService = new Application_Service_SearchService();
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
}
