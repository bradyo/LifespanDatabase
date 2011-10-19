<?php 

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="observation")
 * @HasLifecycleCallbacks
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
     * @Column(name="guid", type="string", length="36")
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

        
    /**
     * @var DateTime When the author submitted the observation entry.
     * @Column(name="authored_at", type="datetime")
     */
    private $authoredAt;
    
    /**
     * @var Application_Model_User User that created the observation version.
     * @OneToOne(targetEntity="Application_Model_User", fetch="EAGER")
     * @JoinColumn(name="author_Id", referencedColumnName="id")
     */
    private $author;
    
    /**
     * @var string Author's comment on the observation version.
     * @Column(name="author_comment", type="text")
     */
    private $authorComment;
    
    /**
     * @var DateTime When the observation entry was reviewed.
     * @Column(name="reviewed_at", type="datetime")
     */
    private $reviewedAt;
    
    /**
     * @var Application_Model_User User that reviewed the observation version.
     * @OneToOne(targetEntity="Application_Model_User", fetch="EAGER")
     * @JoinColumn(name="reviewer_id", referencedColumnName="id")
     */
    private $reviewer;
    
    /**
     * @var string Reviewer's comment on the observation version.
     * @Column(name="reviewer_comment", type="text")
     */
    private $reviewerComment;
    
    /**
     * @var DateTime When the first version of the observation was created (original
     *  creation time)
     * @Column(name="created_at", type="datetime")
     */
    private $createdAt;
          
    /**
     * @OneToOne(targetEntity="Application_Model_Citation")
     * @JoinColumn(name="citation_id", referencedColumnName="id")
     */
    private $citation;
    
    /**
     * @var double Lifespan value with interventions.
     * @Column(name="lifespan", type="float")
     */
    private $lifespanValue;
    
    /**
     * @var double Lifespan value without interventions (experiment control).
     * @Column(name="lifespan_base", type="float")
     */
    private $lifespanBaseValue;
    
    /**
     * @var string Units for lifespan values, i.e. days, divisions, etc...)
     * @Column(name="lifespan_units", type="string")
     */
    private $lifespanUnits;
    
    /**
     * @var double Lifespan percent change of intervention vs control.
     * @Column(name="lifespan_change", type="float")
     */
    private $lifespanPercentChange;
    
    /**
     * @var string Direction of lifespan change, if significant.
     * @Column(name="lifespan_effect", type="string")
     */
    private $lifespanEffect;
    
    /**
     * @var string Type of lifespan measurement, i.e. mean, median, max, etc...
     * @Column(name="lifespan_measure", type="string")
     */
    private $lifespanMeasure;
    
    /**
     * @var string Full species name.
     * @Column(name="species", type="string", length="128")
     */
    private $species;
    
    /**
     * @var string Full strain name.
     * @Column(name="strain", type="string", length="128")
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
     * @Column(name="description", type="string")
     */
    private $description;
    
    /**
     * @OneToMany(targetEntity="Application_Model_ObservationGene", 
     *  mappedBy="observation", fetch="EAGER")
     */
    private $geneInterventions;
    
    /**
     * @OneToMany(targetEntity="Application_Model_ObservationCompound", 
     *  mappedBy="observation", fetch="EAGER")
     */
    private $compoundInterventions;
    
    /**
     * @OneToMany(targetEntity="Application_Model_ObservationEnvironment", 
     *  mappedBy="observation", fetch="EAGER")
     */
    private $environmentInterventions;
        
    /**
     * @var integer Number of gene interventions.
     * @Column(name="gene_count", type="integer")
     */
    private $geneCount;
    
    /**
     * @var integer Number of compound interventions.
     * @Column(name="compound_count", type="integer")
     */
    private $compoundCount;
    
    /**
     * @var integer Number of environment interventions.
     * @Column(name="environment_count", type="integer")
     */
    private $environmentCount;
    
    
    public function __construct() {
        $this->geneInterventions = new ArrayCollection();
        $this->compoundInterventions = new ArrayCollection();
        $this->environmentInterventions = new ArrayCollection();
        $this->version = 1;
    }
        
    /** 
     * Update intervention counts. These are persisted along with observation
     * to allow for more efficient queries.
     * @PrePersist 
     */
    public function updateInterventionCounts() {
        $this->geneCount = count($this->geneInterventions);
        $this->compoundCount = count($this->compoundInterventions);
        $this->environmentCount = count($this->environmentInterventions);
    }
    
    /** 
     * Generate a unique GUID if needed
     * @PrePersist 
     */
    public function generateGuid() {
        if (empty($this->guid)) {
            $this->guid = Application_Guid::generate();
        }
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

    public function getAuthorComment() {
        return $this->authorComment;
    }

    public function setAuthorComment($authorComment) {
        $this->authorComment = $authorComment;
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

    public function getCitation() {
        return $this->citation;
    }

    public function setCitation($citation) {
        $this->citation = $citation;
    }

    public function getLifespanValue() {
        return $this->lifespanValue;
    }

    public function setLifespanValue($lifespanValue) {
        $this->lifespanValue = $lifespanValue;
    }

    public function getLifespanBaseValue() {
        return $this->lifespanBaseValue;
    }

    public function setLifespanBaseValue($lifespanBaseValue) {
        $this->lifespanBaseValue = $lifespanBaseValue;
    }

    public function getLifespanUnits() {
        return $this->lifespanUnits;
    }

    public function setLifespanUnits($lifespanUnits) {
        $this->lifespanUnits = $lifespanUnits;
    }

    public function getLifespanPercentChange() {
        return $this->lifespanPercentChange;
    }

    public function setLifespanPercentChange($lifespanPercentChange) {
        $this->lifespanPercentChange = $lifespanPercentChange;
    }

    public function getLifespanEffect() {
        return $this->lifespanEffect;
    }

    public function setLifespanEffect($lifespanEffect) {
        $this->lifespanEffect = $lifespanEffect;
    }

    public function getLifespanMeasure() {
        return $this->lifespanMeasure;
    }

    public function setLifespanMeasure($lifespanMeasure) {
        $this->lifespanMeasure = $lifespanMeasure;
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

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
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

    public function getGeneCount() {
        return $this->geneCount;
    }

    public function setGeneCount($geneCount) {
        $this->geneCount = $geneCount;
    }

    public function getCompoundCount() {
        return $this->compoundCount;
    }

    public function setCompoundCount($compoundCount) {
        $this->compoundCount = $compoundCount;
    }

    public function getEnvironmentCount() {
        return $this->environmentCount;
    }

    public function setEnvironmentCount($environmentCount) {
        $this->environmentCount = $environmentCount;
    }
}