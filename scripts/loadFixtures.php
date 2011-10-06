<?php

use Doctrine\Common\Collections\ArrayCollection;

define('APPLICATION_ENV', 'development');
define('BASE_PATH', realpath(dirname(__FILE__) . '/..'));
define('APPLICATION_PATH', BASE_PATH . '/application');
define('DATA_PATH', BASE_PATH . '/data');

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(BASE_PATH . '/library'),
    realpath(BASE_PATH . '/library/HtmlPurifier'),
    get_include_path(),
)));

require_once 'Zend/Application.php';
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();


// create sample data
$em = Application_Registry::getEm();

$adminUser = new Application_Model_User();
$adminUser->setUsername('admin');
$adminUser->setEmail('admin@localhost');
$adminUser->setName('Administrator');
$adminUser->setRole(Application_Model_User::ROLE_ADMIN);
$adminUser->setStatus(Application_Model_User::STATUS_ACTIVE);
$adminUser->setPassword('admin');
$em->persist($adminUser);

$species = new Application_Model_Species();
$species->setName('Saccharomyces cerevisiae');
$species->setCommonName('Yeast');
$species->setNcbiTaxonId(4932);
$em->persist($species);

$gene = new Application_Model_Gene();
$gene->setSymbol('SIR2');
$gene->setLocusTag('YDL042C');
$gene->setNcbiGeneId(851520);
$gene->setNcbiProteinId(6320163);
$gene->setDescription('Conserved NAD+ dependent histone deacetylase of the Sirtuin '
    . 'family involved in regulation of lifespan; plays roles in silencing at HML, '
    . 'HMR, telomeres, and the rDNA locus; negatively regulates initiation of DNA '
    . 'replication');
$gene->setSpecies($species);
$em->persist($gene);

$citation = new Application_Model_Citation();
$citation->setAuthors('Kaeberlein M, Kirkland KT, Fields S, Kennedy BK.');
$citation->setTitle('Genes determining yeast replicative life span in a long-lived genetic background');
$citation->setSource('Mech. Ageing Dev.');
$citation->setYear(2005);
$citation->setPubmedId(15722108);
$em->persist($citation);

// create a few varying entries
for ($i = 1; $i <= 3; $i++) {
    $day = 20 + $i;
    $creationDate = new DateTime('2010-04-'.$day.' 10:24:1', new DateTimeZone('America/Los_Angeles'));
    
    $observation = new Application_Model_Observation();
    $observation->setPublicId($i);
    $observation->setVersion(1);
    $observation->setStatus(Application_Model_Observation::STATUS_PUBLIC);
    $observation->setAuthoredAt($creationDate);
    $observation->setAuthor($adminUser);
    $observation->setAuthorComment('noted in paper');
    $observation->setReviewStatus(Application_Model_Observation::REVIEW_STATUS_ACCEPTED);
    $observation->setReviewer($adminUser);
    $observation->setReviewedAt($creationDate);
    $observation->setReviewerComment('data checks out');
    $observation->setCreatedAt($creationDate);
    $observation->setLifespanUnits('days');
    $observation->setLifespanValue(60 + i);
    $observation->setLifespanBaseValue(50);
    $observation->setLifespanEffect('increased');
    $observation->setTemperature(25.0);
    $observation->setDescription('Lifespan changed, see <a href="www.google.com?q=sir2">sir2</a>.');
    $observation->setCitation($citation);
    
    $geneIntervention = new Application_Model_ObservationGene();
    $geneIntervention->setGene($gene);
    $geneIntervention->setAlleleType('deletion / null');
    $geneIntervention->setAllele('sir2-604');
    $geneIntervention->setObservation($observation);
    $em->persist($geneIntervention);
    
    $geneInterventions = new ArrayCollection();
    $geneInterventions->add($geneIntervention);
    $observation->setGeneInterventions($geneInterventions);
    
    $em->persist($observation);
}

$em->flush();
