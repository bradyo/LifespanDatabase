<?php

class Test_Model_SpeciesServiceTest {
   
    /**
     * @var \Doctrine\ORM\EntityManager 
     */
    private $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
    }
    
    public function setUp() {
        $adminUser = new Application_Model_User();
        $adminUser->setUsername('admin');
        $adminUser->setPassword('admin');
        $adminUser->setRole(Application_Model_User::ROLE_ADMIN);
        $this->em->persist($adminUser);
        
        $memberUser = new Application_Model_User();
        $memberUser->setUsername('member');
        $memberUser->setPassword('member');
        $memberUser->setRole(Application_Model_User::ROLE_MEMBER);
        $this->em->persist($memberUser);
        
        $this->em->flush();
    }
    
    public function test() {
       //$this->setup();
       $this->testCreate();
       //$this->testUpdate();
    }
    
    public function testCreate() {
        $userRepo = $this->em->getRepository('Application_Model_User');
        $adminUser = $userRepo->findOneBy(array('username' => 'admin'));
        $speciesService = new Application_Service_SpeciesService($adminUser, $this->em);
        
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
        try {
            $species = $speciesService->create($data);
            echo "species created\n";
            var_dump($species);
        } 
        catch (Application_Exception_UnauthorizedException $e) {
            echo "failed to create - user not authorized:\n";
            var_dump($e->getMessage());
        }
        catch (Application_Exception_ValidateException $e) {
            echo "failed to create - validation error:\n";
            var_dump($speciesService->getValidationErrors());
        }
    }
    
    public function testUpdate() {
        $userRepo = $this->em->getRepository('Application_Model_User');
        $adminUser = $userRepo->findOneBy(array('username' => 'admin'));
        $speciesService = new Application_Service_SpeciesService($adminUser, $this->em);
        
        $repo = $this->em->getRepository('Application_Model_Species');
        $oldSpecies = $repo->find(1);
        
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
        try {
            $species = $speciesService->update($oldSpecies->getId(), $newData);
            var_dump($species);
            echo "species updated";
        } 
        catch (Application_Exception_UnauthorizedException $e) {
            echo "failed to update - user not authorized:\n";
            var_dump($e->getMessage());
        }
        catch (Application_Exception_ValidateException $e) {
            echo "failed to update - validation error:\n";
            var_dump($speciesService->getValidationErrors());
        }
    }
}
