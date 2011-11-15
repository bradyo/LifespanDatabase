<?php

use Doctrine\ORM\Entity\EntityRepository;

class Application_Model_ObservationRepository extends EntityRepository 
{
    /**
     * @param integer $publicId public id of observation versions
     * @return array
     */
    public function findVersions($publicId) {
        return $this->findBy(array('publicId' => $publicId), 'authoredAt');
    }
    
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
