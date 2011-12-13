<?php

use Application\Model\User;
use Application\Model\SpeciesService;

class Test_Model_SpeciesServiceTest {
   
    /**
     * @var \Doctrine\ORM\EntityManager 
     */
    private $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
    }
    
    public function setUp() {
        $adminUser = new User();
        $adminUser->setUsername('admin');
        $adminUser->setPassword('admin');
        $adminUser->setRole(User::ROLE_ADMIN);
        $this->em->persist($adminUser);
        
        $memberUser = new User();
        $memberUser->setUsername('member');
        $memberUser->setPassword('member');
        $memberUser->setRole(User::ROLE_MEMBER);
        $this->em->persist($memberUser);
        
        $this->em->flush();
    }
    
    public function test() {
       //$this->setup();
       $this->testCreate();
       //$this->testUpdate();
    }
    
    public function testCreate() {
        $userRepo = $this->em->getRepository('Application\Model\User');
        $adminUser = $userRepo->findOneBy(array('username' => 'admin'));
        $speciesService = new SpeciesService($adminUser, $this->em);
        
        $data = array(
            'name' => 'Saccharomyces cerevisiae',
            'commonName' => 'yeast',
            'ncbiTaxonId' => 4932,
            'synonyms' => array(
                array(
                    'type' => 'other',
                    'name' => 'Saccharomyces oviformis',
                ),
                array(
                    'type' => 'other',
                    'name' => 'Saccharomyces italicus',
                ),
                array(
                    'type' => 'common',
                    'name' => 'Baker\'s yeast',
                )
            ),
        );
        $species = $speciesService->create($data);
        echo "created\n";
        var_dump($species);
    }
    
    public function testUpdate() {
        $userRepo = $this->em->getRepository('Application\Model\User');
        $adminUser = $userRepo->findOneBy(array('username' => 'admin'));
        $speciesService = new SpeciesService($adminUser, $this->em);
        
        $repo = $this->em->getRepository('Application\Model\Species');
        $oldSpecies = $repo->findOneBy(array('name' => 'Saccharomyces cerevisiae'));
        if ($oldSpecies == null) {
            throw new \Exception('Species not found');
        }
        $id = $oldSpecies->getId();
        
        $newData = array(
            'name' => 'Saccharomyces cerevisiae',
            'commonName' => 'awesome yeast',
            'ncbiTaxonId' => 4932,
            'synonyms' => array(
                array(
                    'type' => 'common',
                    'name' => 'Brewer\'s yeast',
                )
            ),
        );
        $species = $speciesService->update($id, $newData);
        echo "species updated";
        var_dump($species);
    }
}
