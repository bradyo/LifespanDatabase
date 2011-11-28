<?php

use Doctrine\ORM\Entity\EntityRepository;

class Application_Model_StrainRepository extends EntityRepository 
{
    public function nameExists($name, $excludeId = null) {
        $em = $this->getEntityManager();
        if ($excludeId != null) {
            $query = $em->createQuery('
                SELECT s FROM Application_Model_Strain s 
                WHERE s.name = :name AND s.id <> :excludeId
                ');
            $query->setParameter('name', $name);
            $query->setParameter('excludeId', $excludeId);
        } else {
            $query = $em->createQuery('
                SELECT s FROM Application_Model_Strain s 
                WHERE s.name = :name
                ');
            $query->setParameter('name', $name);
        }
        $strains = $query->getResult();
        return (count($strains) > 0);
    }
}
