<?php

use Application\Model\User;
use Application\Model\ObservationService;

class Test_Model_ObservationServiceTest {
   
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
        $observationService = new ObservationService($adminUser, $this->em);
        
        $author = $adminUser;
        $reviewer = $adminUser;
        
        $citation = new Citation();
        $citation->setTitle('citation title');
        $citation->setAuthors('citation authors');
        $citation->setSource('citation source');
        $citation->setYear(2010);
        $citation->setPubmedId(99999);
        $citation->setCorrespondanceEmail('admin@localhost');
        $this->em->persist($citation);
        
        $speciesRepo = $this->em->getRepository('Application\Model\Species');
        $species = $speciesRepo->findOneBy(array($name => 'Saccharomyces cerevisiae'));
        
        $strain = null;
        
        $data = array(
            'publicId' => 1,
            'status' => Observation::STATUS_PUBLIC,
            'authoredAt' => \DateTime::createFromFormat('Y-m-d', '2011-07-01'),
            'author' => $author,
            'authorComment' => 'author comment',
            'reviewStatus' => Observation::REVIEW_STATUS_ACCEPTED,
            'reviewedAt' => \DateTime::createFromFormat('Y-m-d', '2011-07-01'),
            'reviewer' => $reviewer,
            'reviewerComment' => 'reviewer comment',
            'citation' => $citation,
            'species' => $species,
            'strain' => $strain,
            'cellType' => 'cell type',
            'temperature' => 25.5,
            'lifespanValue' => 80,
            'lifespanBaseValue' => 70,
            'lifespanPercentChange' => (80 - 70) / 70 * 100,
            'lifespanUnits' => 'days',
            'lifespanMeasure' => 'mean',
            'lifespanEffect' => 'increased',
            'description' => 'observation description',
        );
        $observation = $observationService->create($data);
        echo "created\n";
        var_dump($observation);
    }
    
    public function testUpdate() {
        
    }
}
