<?php

use Doctrine\ORM\Entity\EntityRepository;

class Application_Model_ObservationRepository extends EntityRepository 
{
    /**
     *
     */
    private function getBaseQueryBuilder() {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getEntityManager();
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder->from('Application_Model_Observation observation');
        
        return $queryBuilder;
    }
}
