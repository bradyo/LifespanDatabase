<?php

class Test_Model_SpeciesServiceTest {
   
    /**
     * @var \Doctrine\ORM\EntityManager 
     */
    private $em;
    
    /**
     * @var Application_Model_User User accessing the service
     */
    private $user;
    
    
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
    
    public function testCreate() {
        $userRepo = $this->em->getRepository('Application_Model_User');
        $adminUser = $userRepo->findOneBy(array('username' => 'admin'));
        $service = new Application_Service_SpeciesService($adminUser, $this->em);
        
        $data = $this->getSampleData();
        try {
            $species = $service->create($data);
            var_dump($species);
            echo "species created\n";
        } catch (Exception $e) {
            echo "failed to create:\n";
            $validationErrors = $service->getValidationErrors();
            var_dump($validationErrors);
        }
    }
    
    public function testCreateNotAuthorized() {
        $userRepo = $this->em->getRepository('Application_Model_User');
        $adminUser = $userRepo->findOneBy(array('username' => 'member'));
        $service = new Application_Service_SpeciesService($adminUser, $this->em);
        
        $data = $this->getSampleData();
        try {
            $species = $service->create($data);
            var_dump($species);
            echo "species created\n";
        } 
        catch (Application_Exception_UnauthorizedException $e) {
            echo "failed to create - user not authorized:\n";
            var_dump($e->getMessage());
        }
        catch (Application_Exception_ValidateException $e) {
            echo "failed to create - validation error:\n";
            $validationErrors = $service->getValidationErrors();
            var_dump($validationErrors);
        }
    }
    
    private function getSampleData() {
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
        return $data;
    }
}
