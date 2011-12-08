<?php 

namespace Application\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="gene")
 * @HasLifecycleCallbacks
 */
class Gene 
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
     * @var Species Species gene belongs to.
     * @OneToOne(targetEntity="Application\Model\Species")
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
     * $var array(Application\Model\GeneGoTerm) List of associated gene go temrs
     * @OneToMany(targetEntity="Application\Model\GeneGoTerm", mappedBy="gene", cascade={"persist"})
     */
    private $goTerms;
    
    public function __construct() {
        $this->goTerms = ArrayCollection();
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
    
    public function getGoTerms() {
        return $this->goTerms;
    }

    public function setGoTerms($goTerms) {
        $this->goTerms = $goTerms;
    }
    
    public function toArray($expandRelations = array()) {
        $data = array(
            'id' => $this->id,
            'guid' => $this->guid,
            'species' => ($this->species) ? $this->species->toArray() : null,
            'symbol' => $this->symbol,
            'locusTag' => $this->locusTag,
            'description' => $this->description,
            'ncbiGeneId' => $this->ncbiGeneId,
            'ncbiProteinId' => $this->ncbiProteinId,
        );
        
        if (in_array('goTerms', $expandRelations)) {
            $goTermsData = array();
            foreach ($this->getGoTerms() as $goTerm) {
                $goTermsData[] = $goTerm->toArray();
            }
            $data['goTerms'] = $goTermsData;
        }
        
        return $data;
    }
}
