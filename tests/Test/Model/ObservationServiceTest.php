<?php

use Application\Model\User;
use Application\Model\ObservationService;
use Application\Model\Citation;
use Application\Model\Species;
use Application\Model\Strain;
use Application\Model\Observation;

class Test_Model_ObservationServiceTest {
   
    /**
     * @var \Doctrine\ORM\EntityManager 
     */
    private $em;
    
    public function __construct(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
    }
    
    public function setUp() {
        // clear tables
        $this->em->getConnection()->exec('TRUNCATE citation');
        $this->em->getConnection()->exec('TRUNCATE observation');
        $this->em->getConnection()->exec('TRUNCATE species');
        $this->em->getConnection()->exec('TRUNCATE strain');
        $this->em->getConnection()->exec('TRUNCATE compound');
        $this->em->getConnection()->exec('TRUNCATE environment');
        $this->em->getConnection()->exec('TRUNCATE gene');
        $this->em->getConnection()->exec('TRUNCATE user');
        
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
        
        $citation = new Citation();
        $citation->setTitle('citation title');
        $citation->setAuthors('citation authors');
        $citation->setSource('citation source');
        $citation->setYear(2010);
        $citation->setPubmedId(99999);
        $this->em->persist($citation);
        
        $species = new Species();
        $species->setGuid(\Application\Util\Guid::generate());
        $species->setName('Saccharomyces cerevisiae');
        $species->setCommonName('yeast');
        $species->setNcbiTaxonId(4932);
        $this->em->persist($species);
        
        $gene = new \Application\Model\Gene();
        $gene->setSymbol('TOR1');
        $gene->setGuid(\Application\Util\Guid::generate());
        $gene->setDescription('gene description');
        $gene->setSpecies($species);
        $this->em->persist($gene);
        
        $this->em->flush();
        
        // load 3 revisions of observation 1
        $baseData = array(
            'publicId' => 1,
            'guid' => \Application\Util\Guid::generate(),
            'status' => Observation::STATUS_PUBLIC,
            'authoredAt' => \DateTime::createFromFormat('Y-m-d', '2011-07-01'),
            'author' => $adminUser,
            'authorComment' => 'author comment',
            'reviewStatus' => Observation::REVIEW_STATUS_ACCEPTED,
            'reviewedAt' => \DateTime::createFromFormat('Y-m-d', '2011-07-01'),
            'reviewer' => $adminUser,
            'reviewerComment' => 'reviewer comment',
            'correspondanceEmail' => 'admin@localhost',
            'citation' => $citation,
            'species' => $species,
            'strain' => null,
            'cellType' => 'cell type',
            'temperature' => 25.5,
            'lifespanValue' => 80,
            'lifespanBaseValue' => 70,
            'lifespanUnits' => 'days',
            'lifespanMeasure' => 'mean',
            'lifespanEffect' => 'increased',
            'description' => 'observation description',
        );
        for ($i = 0; $i < 3; $i++) {
            $data = $baseData;
            $datetime = \DateTime::createFromFormat('Y-m-d', '2011-08-01');
            $datetime->add(new DateInterval('P' . $i . 'D'));
            $data['createdAt'] = $datetime;
            $data['reviewedAt'] = $datetime;
            
            $geneIntervention = new \Application\Model\GeneIntervention();
            $geneIntervention->setGene($gene);
            $geneIntervention->setAlleleType("deletion / null");
            $geneIntervention->setAllele('allele1');
            $this->em->persist($geneIntervention);
            
            $observation = new Observation();
            $observation->fromArray($data);
            $observation->addGeneIntervention($geneIntervention);
            $this->em->persist($observation);
        }
        
        // create 2 versions of observation 2
        $baseData['publicId'] = 2;
        $baseData['guid'] = \Application\Util\Guid::generate();
        $baseData['temperature'] = 30;
        $baseData['lifespanValue'] = 100;
        $baseData['lifespanBaseValue'] = 90;
        for ($i = 0; $i < 2; $i++) {
            $data = $baseData;
            $datetime = \DateTime::createFromFormat('Y-m-d', '2011-08-01');
            $datetime->add(new DateInterval('P' . $i . 'D'));
            $data['createdAt'] = $datetime;
            $data['reviewedAt'] = $datetime;
            
            $geneIntervention = new \Application\Model\GeneIntervention();
            $geneIntervention->setGene($gene);
            $geneIntervention->setAlleleType("deletion / null");
            $geneIntervention->setAllele('allele1');
            $this->em->persist($geneIntervention);
            
            $observation = new Observation();
            $observation->fromArray($data);
            $observation->addGeneIntervention($geneIntervention);
            $this->em->persist($observation);
        }
        
        $this->em->flush();
    }

    
    public function test() {
       //$this->setup();
       $this->testCreate();
       //$this->testUpdate();
    }
    
    public function testCreate() {
        
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
