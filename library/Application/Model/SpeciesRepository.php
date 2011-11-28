<?php

use Doctrine\ORM\Entity\EntityRepository;

class Application_Model_SpeciesRepository extends EntityRepository 
{
    public function nameExists($name, $excludeId = null) {
        $em = $this->getEntityManager();
        if ($excludeId != null) {
            $query = $em->createQuery('
                SELECT s FROM Application_Model_Species s 
                WHERE s.name = :name AND s.id <> :excludeId
                ');
            $query->setParameter('name', $name);
            $query->setParameter('excludeId', $excludeId);
        } else {
            $query = $em->createQuery('
                SELECT s FROM Application_Model_Species s 
                WHERE s.name = :name
                ');
            $query->setParameter('name', $name);
        }
        $species = $query->getResult();
        return (count($species) > 0);
    }
}
