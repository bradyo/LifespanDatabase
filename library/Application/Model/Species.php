<?php

namespace Application\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @entity(repositoryClass="Application\Model\SpeciesRepository")
 * @Table(name="species")
 */
class Species
{
    const STATUS_PENDING = 'pending';
    const STATUS_PUBLIC = 'public';
    const STATUS_DELETED = 'deleted';
    
    /**
     * @var integer
     * @Id 
     * @Column(name="id", type="integer") 
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string Globally unique identifier.
     * @Column(name="guid", type="string", length="36")
     */
    private $guid;
    
    /**
     * @var string Record status (if reviewed or not)
     * @Column(name="status", type="string", length="32")
     */
    private $status = self::STATUS_PENDING;
    
    /**
     * @var string Full species name.
     * @Column(name="name", type="string", length="128")
     */
    private $name;
    
    /**
     * @var string Species common name.
     * @Column(name="common_name", type="string", length="128")
     */
    private $commonName;
    
    /**
     * @var integer Corresponding NCBI taxonomy ID
     * @Column(name="ncbi_tax_id", type="integer")
     */
    private $ncbiTaxonId;
    
    /**
     * $var ArrayCollection Synonyms for species name.
     * @OneToMany(targetEntity="Application\Model\SpeciesSynonym", mappedBy="species", 
     *  cascade={"persist"})
     */
    private $synonyms;
    
    
    public function __construct() {
        $this->synonyms = new ArrayCollection();
    }

    public function fromArray($data) {
        $properties = array(
            'id',
            'guid',
            'status',
            'name',
            'commonName',
            'ncbiTaxonId',
        );
        foreach ($properties as $property) {
            if (isset($data[$property])) {
                $this->{$property} = $data[$property];
            }
        }
        if (isset($data['synonyms'])) {
            foreach ($data['synonyms'] as $synonymData) {
                $synonym = new SpeciesSynonym($synonymData);
                $this->addSynonym($synonym);
            }
        }
    }
    
    public function toArray($expandRelations = array()) {
        $data = array(
            'id' => $this->id,
            'guid' => $this->guid,
            'status' => $this->status,
            'name' => $this->name,
            'commonName' => $this->commonName,
            'ncbiTaxonId' => $this->ncbiTaxonId,
        );
        if (in_array('synonyms', $expandRelations)) {
            $synonymsData = array();
            foreach ($this->getSynonyms() as $synonym) {
                $synonymsData[] = $synonym->toArray();
            }
            $data['synonyms'] = $synonymsData;
        }
        return $data;
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

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getCommonName() {
        return $this->commonName;
    }

    public function setCommonName($commonName) {
        $this->commonName = $commonName;
    }

    public function getNcbiTaxonId() {
        return $this->ncbiTaxonId;
    }

    public function setNcbiTaxonId($ncbiTaxonId) {
        $this->ncbiTaxonId = $ncbiTaxonId;
    }

    public function getSynonyms() {
        return $this->synonyms;
    }

    public function setSynonyms($synonyms) {
        $this->synonyms = $synonyms;
    }

    public function addSynonym($synonym) {
        $synonym->setSpecies($this);
        $this->synonyms->add($synonym);
    }
}
