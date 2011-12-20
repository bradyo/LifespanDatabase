<?php

namespace Application\Model;

use Doctrine\ORM\Entity\EntityRepository;

class ObservationRepository extends EntityRepository 
{
    public function getCurrent($criteria, $orderBy, $limit, $offset) {
        
        // todo: fetch id subset from fulltext search
        $searchMatchIds = array();
        
        $dql = '
            SELECT observation
            FROM (
                SELECT observationSubset
                FROM (
                        SELECT id, MAX(authoredAt) as versionDate 
                        FROM Application\Model\Observation GROUP BY publicId
                ) AS observationSubset
                LEFT JOIN Application\Model\Observation o ON o.id = observationSubset.id
            ) as observation
            WHERE observation.status = :status 
            AND observation.reviewStatus = :reviewStatus
            ';
        if (count($searchMatchIds) > 0) {
            $dql .= ' AND observation.id IN (' . implode(', ', $searchMatchIds) . ')';
        }
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('status', Observation::STATUS_PUBLIC);
        $query->setParameter('reviewStatus', Observation::REVIEW_STATUS_ACCEPTED);
        
        return $query->getResult();
    }
}
