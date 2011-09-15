<?php 

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="observation")
 */
class Application_Model_Observation
{
    const STATUS_PUBLIC = "public";
    const STATUS_DELETED = "deleted";
    
    const REVIEW_STATUS_PENDING = "pending";
    const REVIEW_STATUS_ACCEPTED = "accepted";
    const REVIEW_STATUS_REJECTED = "rejected";
    
    /**
     * @var integer ID of the observation version (each version has different ID)
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string Globally unique identifier (conserved across versions).
     * @Column(name="guid", type="string")
     */
    private $guid;
    
    /**
     * @var string ID of observation that appears in URLs (conserved across versions).
     * @Column(name="public_id", type="string")
     */
    private $publicId;
    
    /**
     * @var integer Version number of the observation
     * @Column(name="version", type="integer")
     */
    private $version;
    
    /**
     * @var boolean Version number of the observation
     * @Column(name="is_current", type="boolean")
     */
    private $isCurrent;
    
    /**
     * @var string Status of observation version.
     * @Column(name="status", type="string")
     */
    private $status;
    
    /**
     * @var string Review status of observation.
     * @Column(name="review_status", type="string")
     */
    private $reviewStatus;
    
    
    private $authoredAt;
    
    private $author;
    
    private $reviewedAt;
    
    private $reviewer;
    
    private $reviewerComment;
    
    private $createdAt;
    
    private $updatedAt;
    
        
    /**
     * @OneToOne(targetEntity="Application_Model_Citation")
     * @JoinColumn(name="citation_id", referencedColumnName="id")
     */
    private $citation;
    
    /**
     * @var Application_Model_ObservationLifespan Lifespan data for observation.
     * @OneToOne(targetEntity="Application_Model_ObservationLifespan")
     * @JoinColumn(name="id", referencedColumnName="id")
     */
    private $lifespan;
    
    /**
     * @var Application_Model_Species Species used in observation.
     * @OneToOne(targetEntity="Application_Model_Species")
     * @JoinColumn(name="species_id", referencedColumnName="id")
     */
    private $species;
    
    /**
     * @var Application_Model_Strain Strain used in observation.
     * @OneToOne(targetEntity="Application_Model_Strain")
     * @JoinColumn(name="strain_id", referencedColumnName="id")
     */
    private $strain;
    
    /**
     * @var string Cell type if done in culture, i.e. "HeLa cells"
     * @Column(name="cell_type", type="string")
     */
    private $cellType;
    
    /**
     * @var string Mating type of the organism, i.e. "Male", "Female", "MATa", etc...
     * @Column(name="mating_type", type="string")
     */
    private $matingType;
    
    /**
     * @var float Experimental temperature in Celcius.
     * @Column(name="temperature", type="decimal", scale="5", precision="2")
     */
    private $temperature;
    
    /**
     * @var string Description of observation (basic HTML allowed).
     * @Column(name="body", type="string")
     */
    private $body;
    
    /**
     * @OneToMany(targetEntity="Application_Model_GeneIntervention", 
     *  mappedBy="observation", fetch="EAGER")
     */
    private $geneInterventions;
    
    /**
     * @OneToMany(targetEntity="Application_Model_CompoundIntervention", 
     *  mappedBy="observation", fetch="EAGER")
     */
    private $compoundInterventions;
    
    /**
     * @OneToMany(targetEntity="Application_Model_EnvironmentIntervention", 
     *  mappedBy="observation", fetch="EAGER")
     */
    private $environmentInterventions;
    
    /**
     * @OneToOne(targetEntity="Application_Model_ObservationStatistics")
     * @JoinColumn(name="observation_id", referencedColumnName="id")
     */
    private $statistics;

    
    public function __construct() {
        $this->geneInterventions = new ArrayCollection();
        $this->compoundInterventions = new ArrayCollection();
        $this->environmentInterventions = new ArrayCollection();
    }
    
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getGuid() {
        return $this->guid;
    }

    public function setGuid($guid) {
        $this->guid = $guid;
    }

    public function getPublicId() {
        return $this->publicId;
    }

    public function setPublicId($publicId) {
        $this->publicId = $publicId;
    }

    public function getVersion() {
        return $this->version;
    }

    public function setVersion($version) {
        $this->version = $version;
    }

    public function getIsCurrent() {
        return $this->isCurrent;
    }

    public function setIsCurrent($isCurrent) {
        $this->isCurrent = $isCurrent;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getReviewStatus() {
        return $this->reviewStatus;
    }

    public function setReviewStatus($reviewStatus) {
        $this->reviewStatus = $reviewStatus;
    }

    public function getAuthoredAt() {
        return $this->authoredAt;
    }

    public function setAuthoredAt($authoredAt) {
        $this->authoredAt = $authoredAt;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
    }

    public function getReviewedAt() {
        return $this->reviewedAt;
    }

    public function setReviewedAt($reviewedAt) {
        $this->reviewedAt = $reviewedAt;
    }

    public function getReviewer() {
        return $this->reviewer;
    }

    public function setReviewer($reviewer) {
        $this->reviewer = $reviewer;
    }

    public function getReviewerComment() {
        return $this->reviewerComment;
    }

    public function setReviewerComment($reviewerComment) {
        $this->reviewerComment = $reviewerComment;
    }

    public function getCreatedAt() {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt() {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;
    }

    public function getCitation() {
        return $this->citation;
    }

    public function setCitation($citation) {
        $this->citation = $citation;
    }

    public function getLifespan() {
        return $this->lifespan;
    }

    public function setLifespan($lifespan) {
        $this->lifespan = $lifespan;
    }

    public function getSpecies() {
        return $this->species;
    }

    public function setSpecies($species) {
        $this->species = $species;
    }

    public function getStrain() {
        return $this->strain;
    }

    public function setStrain($strain) {
        $this->strain = $strain;
    }

    public function getCellType() {
        return $this->cellType;
    }

    public function setCellType($cellType) {
        $this->cellType = $cellType;
    }

    public function getMatingType() {
        return $this->matingType;
    }

    public function setMatingType($matingType) {
        $this->matingType = $matingType;
    }

    public function getTemperature() {
        return $this->temperature;
    }

    public function setTemperature($temperature) {
        $this->temperature = $temperature;
    }

    public function getBody() {
        return $this->body;
    }

    public function setBody($body) {
        $this->body = $body;
    }

    public function getGeneInterventions() {
        return $this->geneInterventions;
    }

    public function setGeneInterventions($geneInterventions) {
        $this->geneInterventions = $geneInterventions;
    }

    public function getCompoundInterventions() {
        return $this->compoundInterventions;
    }

    public function setCompoundInterventions($compoundInterventions) {
        $this->compoundInterventions = $compoundInterventions;
    }

    public function getEnvironmentInterventions() {
        return $this->environmentInterventions;
    }

    public function setEnvironmentInterventions($environmentInterventions) {
        $this->environmentInterventions = $environmentInterventions;
    }

    public function getStatistics() {
        return $this->statistics;
    }

    public function setStatistics($statistics) {
        $this->statistics = $statistics;
    }

}