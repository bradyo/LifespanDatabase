<?php 

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="gene")
 * @HasLifecycleCallbacks
 */
class Application_Model_Gene 
{
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string Globally unique identifier.
     * @Column(name="guid", type="string")
     */
    private $guid;
    
    /**
     * @var Application_Model_Species Species gene belongs to.
     * @OneToOne(targetEntity="Application_Model_Species")
     * @JoinColumn(name="species_id", referencedColumnName="id")
     */     
    private $species;
    
    /**
     * @var string Official gene symbol.
     * @Column(name="symbol", type="string")
     */
    private $symbol;
    
    /**
     * @var string Corresponding locus tag of gene.
     * @Column(name="locus_tag", type="string")
     */
    private $locusTag;
    
    /**
     * @var string
     * @Column(name="description", type="string")
     */
    private $description;
    
    /**
     * Corresponding NCBI gene ID.
     * @Column(name="ncbi_gene_id", type="integer")
     */
    private $ncbiGeneId;
    
    /**
     * Corresponding NCBI protein ID.
     * @Column(name="ncbi_protein_id", type="integer")
     */
    private $ncbiProteinId;
    
    /**
     * @OneToMany(targetEntity="Application_Model_GeneSynonym", mappedBy="gene")
     */
    private $synonyms;
    
    /**
     * @OneToMany(targetEntity="Application_Model_GeneLink", mappedBy="gene")
     */
    private $links;
    
    /**
     * @OneToMany(targetEntity="Application_Model_GeneGoTerm", mappedBy="gene")
     */
    private $goTerms;
    
    /**
     * @OneToMany(targetEntity="Application_Model_GeneHomolog", mappedBy="gene")
     */
    private $homologs;
    
    
    
    public function __construct() {
        $this->synonyms = new ArrayCollection();
        $this->links = new ArrayCollection();
        $this->goTerms = new ArrayCollection();
        $this->homologs = new ArrayCollection();
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

    public function getSpecies() {
        return $this->species;
    }

    public function setSpecies($species) {
        $this->species = $species;
    }

    public function getSymbol() {
        return $this->symbol;
    }

    public function setSymbol($symbol) {
        $this->symbol = $symbol;
    }

    public function getLocusTag() {
        return $this->locusTag;
    }

    public function setLocusTag($locusTag) {
        $this->locusTag = $locusTag;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getNcbiGeneId() {
        return $this->ncbiGeneId;
    }

    public function setNcbiGeneId($ncbiGeneId) {
        $this->ncbiGeneId = $ncbiGeneId;
    }

    public function getNcbiProteinId() {
        return $this->ncbiProteinId;
    }

    public function setNcbiProteinId($ncbiProteinId) {
        $this->ncbiProteinId = $ncbiProteinId;
    }
    
    public function getSynonyms() {
        return $this->synonyms;
    }

    public function setSynonyms($synonyms) {
        $this->synonyms = $synonyms;
    }

    public function getLinks() {
        return $this->links;
    }

    public function setLinks($links) {
        $this->links = $links;
    }

    public function getGoTerms() {
        return $this->goTerms;
    }

    public function setGoTerms($goTerms) {
        $this->goTerms = $goTerms;
    }

    public function getHomologs() {
        return $this->homologs;
    }

    public function setHomologs($homologs) {
        $this->homologs = $homologs;
    }
}
