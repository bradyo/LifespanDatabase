<?php 

namespace Application\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="observation")
 * @HasLifecycleCallbacks
 */
class Observation
{
    /**
     * Status given to observations that are active and made public in searches.
     */
    const STATUS_PUBLIC = "public";
    
    /**
     * Status given to observations that have been deleted.
     */
    const STATUS_DELETED = "deleted";
    
    /**
     * Review status given to observations that have not yet been reviewed.
     */
    const REVIEW_STATUS_PENDING = "pending";
    
    /**
     * Review status given to observations that have been accepted by a reviewer.
     */
    const REVIEW_STATUS_ACCEPTED = "accepted";
    
    /**
     * Review status given to observations that have been rejected by a reviewer.
     */
    const REVIEW_STATUS_REJECTED = "rejected";
    
    /**
     * @var integer ID of the observation version (each version has different ID)
     * @Id 
     * @Column(name="id", type="integer")
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
     * @var string Status of observation version.
     * @Column(name="status", type="string")
     */
    private $status = self::STATUS_PUBLIC;

    /**
     * @var DateTime When the author submitted the observation entry.
     * @Column(name="authored_at", type="datetime")
     */
    private $authoredAt;
    
    /**
     * @var User User that created the observation version.
     * @OneToOne(targetEntity="Application\Model\User", fetch="EAGER")
     * @JoinColumn(name="author_Id", referencedColumnName="id")
     */
    private $author;
    
    /**
     * @var string Author's comment on the observation version.
     * @Column(name="author_comment", type="text")
     */
    private $authorComment;
    
    /**
     * @var string Review status of observation.
     * @Column(name="review_status", type="string")
     */
    private $reviewStatus = self::REVIEW_STATUS_PENDING;
    
    /**
     * @var DateTime When the observation entry was reviewed.
     * @Column(name="reviewed_at", type="datetime")
     */
    private $reviewedAt;
    
    /**
     * @var Application_Model_User User that reviewed the observation version.
     * @OneToOne(targetEntity="Application\Model\User", fetch="EAGER")
     * @JoinColumn(name="reviewer_id", referencedColumnName="id")
     */
    private $reviewer;
    
    /**
     * @var string Reviewer's comment on the observation version.
     * @Column(name="reviewer_comment", type="text")
     */
    private $reviewerComment;
              
    /**
     * @var string E-mail address of person that should be contacted in regards to this observation
     * @Column(name="correspondance_email", type="text")
     */
    private $correspondanceEmail;
    
    /**
     * @OneToOne(targetEntity="Application\Model\Citation")
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
     * @OneToOne(targetEntity="Application\Model\Species")
     * @JoinColumn(name="species_id", referencedColumnName="id")
     */
    private $species;
    
    /**
     * @OneToOne(targetEntity="Application\Model\Strain")
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
     * @Column(name="description", type="string")
     */
    private $description;
    
    /**
     * @OneToMany(targetEntity="Application\Model\GeneIntervention", 
     *  mappedBy="observation", fetch="EAGER")
     */
    private $geneInterventions;
    
    /**
     * @OneToMany(targetEntity="Application\Model\CompoundIntervention", 
     *  mappedBy="observation", fetch="EAGER")
     */
    private $compoundInterventions;
    
    /**
     * @OneToMany(targetEntity="Application\Model\EnvironmentIntervention", 
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
    
    public function getCorrespondanceEmail() {
        return $this->correspondanceEmail;
    }

    public function setCorrespondanceEmail($correspondanceEmail) {
        $this->correspondanceEmail = $correspondanceEmail;
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

    public function addGeneIntervention($geneIntervention) {
        /* @var $geneIntervention Application\Model\GeneIntervention */
        $this->geneInterventions->add($geneIntervention);
        $geneIntervention->setObservation($this);
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

    public function getCompoundCount() {
        return $this->compoundCount;
    }

    public function getEnvironmentCount() {
        return $this->environmentCount;
    }
    
    public function fromArray($data) {
        $properties = get_object_vars($this);
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $properties)) {
                $setter = 'set' . ucfirst($key);
                $this->{$setter}($value);
            }
        }
    }
    
    public function toArray() {
        $data = array(
            'id' => $this->id,
            'guid' => $this->guid,
            'publicId' => $this->publicId,
            'status' => $this->status,
            'authoredAt' => ($this->authoredAt) ? $this->authoredAt->format(\DateTime::ISO8601) : null,
            'author' => $this->author->toArray(),
            'authorComment' => $this->authorComment,
            'reviewStatus' => $this->reviewStatus,
            'reviewedAt' => ($this->reviewedAt) ? $this->reviewedAt->format(\DateTime::ISO8601) : null,
            'reviewerComment' => $this->reviewerComment,
            'correspondanceEmail' => $this->correspondanceEmail,
            'citation' => ($this->citation) ? $this->citation->toArray() : null,
            'species' => ($this->species) ? $this->species->toArray() : null,
            'strain' => ($this->strain) ? $this->strain->toArray() : null,
            'cellType' => $this->cellType,
            'temperature' => $this->temperature,
            'lifespanValue' => $this->lifespanValue,
            'lifespanBaseValue' => $this->lifespanBaseValue,
            'lifespanPercentChange' => $this->lifespanPercentChange,
            'lifespanUnits' => $this->lifespanUnits,
            'lifespanMeasure' => $this->lifespanMeasure,
            'lifespanEffect' => $this->lifespanEffect,
            'description' => $this->description,
            'geneInterventionsCount' => $this->geneCount,
            'compoundInterventionsCount' => $this->compoundCount,
            'environmentInterventionsCount' => $this->environmentCount,
        );
        
        $geneInterventionsData = array();
        foreach ($this->getGeneInterventions() as $geneIntervention) {
            $geneInterventionsData[] = $geneIntervention->toArray();
        }
        $data['geneInterventions'] = $geneInterventionsData;
        
//        $compoundInterventionsData = array();
//        foreach ($this->getCompoundInterventions() as $intervention) {
//            $compoundInterventionsData[] = $intervention->toArray();
//        }
//        $data['compoundInterventions'] = $compoundInterventionsData;
//        
//        $envInterventionsData = array();
//        foreach ($this->getEnvironmentInterventions() as $intervention) {
//            $envInterventionsData[] = $intervention->toArray();
//        }
//        $data['environmentInterventions'] = $envInterventionsData;
        
        return $data;
    }
}