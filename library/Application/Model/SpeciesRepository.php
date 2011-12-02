<?php

namespace Application\Model;

use Doctrine\ORM\Entity\EntityRepository;

class SpeciesRepository extends EntityRepository 
{
    public function nameExists($name, $excludeId = null) {
        $em = $this->getEntityManager();
        if ($excludeId != null) {
            $query = $em->createQuery('
                SELECT s FROM Application\Model\Species s 
                WHERE s.name = :name AND s.id <> :excludeId
                ');
            $query->setParameter('name', $name);
            $query->setParameter('excludeId', $excludeId);
        } else {
            $query = $em->createQuery('
                SELECT s FROM Application\Model\Species s 
                WHERE s.name = :name
                ');
            $query->setParameter('name', $name);
        }
        $species = $query->getResult();
        return (count($species) > 0);
    }
    
    public function getObservationsPerSpecies() {
        $dql = '
            SELECT s.id, s.name, count(o.id) AS observationCount 
            FROM Application\Model\Observation o
            JOIN o.species s 
            WHERE s.status = ?1 AND o.status = ?2 AND o.reviewStatus = ?3 AND o.isCurrent = ?4
            GROUP BY o.publicId
            ';
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter(1, Species::STATUS_PUBLIC);
        $query->setParameter(2, Observation::STATUS_PUBLIC);
        $query->setParameter(3, Observation::REVIEW_STATUS_ACCEPTED);
        $query->setParameter(4, true);
        return $query->getScalarResult();
    }
    
    public function getCurrentObservationsPerSpecies() {
        $dql = '
            SELECT s.id, s.name, count(o.id) AS observationCount 
            FROM (
                SELECT oSubset
                FROM (
                        SELECT id, MAX(authoredAt) at versionDate 
                        FROM Application\Model\Observation GROUP BY publicId
                ) AS oSubset
                LEFT JOIN Application\Model\Observation o ON o.id = oSubset.id
            ) as o
            JOIN o.species s 
            WHERE s.status = ?1 AND o.status = ?2 AND o.reviewStatus = ?3
            GROUP BY o.publicId
            ';
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter(1, Species::STATUS_PUBLIC);
        $query->setParameter(2, Observation::STATUS_PUBLIC);
        $query->setParameter(3, Observation::REVIEW_STATUS_ACCEPTED);
        return $query->getScalarResult();
    }
}
