<?php

/**
 * @Entity
 * @Table(name="species")
 */
class Application_Model_Species
{
    /**
     * @var integer
     * @Id 
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string Globally unique identifier.
     * @Column(name="guid", type="string", length="32")
     */
    private $guid;
    
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
     * $var array(Application_Model_GeneSynonym) List of synonyms for species name.
     * @OneToMany(targetEntity="Application_Model_GeneSynonym", mappedBy="gene")
     */
    private $synonyms;
    
    
    public function __construct() {
        $this->synonyms = new ArrayCollection();
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
}
