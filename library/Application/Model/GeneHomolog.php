<?php

/**
 * @Entity
 * @Table(name="gene_homolog")
 */
class Application_Model_GeneHomolog 
{
    const ALGORITHM_JACCARD = 'Jaccard';
    const ALGORITHM_ORTHOMCL = 'OrthoMCL';
    const ALGORITHM_PARA = 'Para';

    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Application_Model_Gene
     * @ManyToOne(targetEntity="Application_Model_Gene", inversedBy="homologs")
     * @JoinColumn(name="gene_id", referencedColumnName="id")
     */
    private $gene;
    
    /**
     * @var Application_Model_Gene
     * @ManyToOne(targetEntity="Application_Model_Gene", inversedBy="goTerms")
     * @JoinColumn(name="gene_id", referencedColumnName="id")
     */
    private $homologGene;
    
    /**
     * @var integer Official NCBI gene ID of homolog since not all homologs have
     *   corresponding locally stored gene data.
     * @Column(name="homolog_ncbi_gene_id", type="string", length="64")
     */
    private $homologNcgiGeneId;
        
    /**
     * @var string Algorithm used to group homologs.
     * @Column(name="algorithm", type="string", length="64")
     */
    private $algorithm;
    
    /**
     * @var string Family designation of homolog cluster.
     * @Column(name="family", type="string", length="64")
     */
    private $family;
    
    /**
     * @var string Full species name for homolog gene.
     * @Column(name="species", type="string", length="64")
     */
    private $speciesName;
    
    /**
     * @var string Gene symbol of homolog.
     * @Column(name="symbol", type="string", length="64")
     */
    private $geneSymbol;
    
    /**
     * @var string Protein database namespace for correpsonding homolog link.
     * @Column(name="protein_database", type="string", length="64")
     */
    private $proteinDatabase;
    
    /**
     * @var string Protein database ID for correpsonding homolog protein link.
     * @Column(name="protein_id", type="string", length="64")
     */
    private $proteinId;
    
    
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getGene() {
        return $this->gene;
    }

    public function setGene($gene) {
        $this->gene = $gene;
    }

    public function getHomologGene() {
        return $this->homologGene;
    }

    public function setHomologGene($homologGene) {
        $this->homologGene = $homologGene;
    }

    public function getHomologNcgiGeneId() {
        return $this->homologNcgiGeneId;
    }

    public function setHomologNcgiGeneId($homologNcgiGeneId) {
        $this->homologNcgiGeneId = $homologNcgiGeneId;
    }

    public function getAlgorithm() {
        return $this->algorithm;
    }

    public function setAlgorithm($algorithm) {
        $this->algorithm = $algorithm;
    }

    public function getFamily() {
        return $this->family;
    }

    public function setFamily($family) {
        $this->family = $family;
    }

    public function getSpeciesName() {
        return $this->speciesName;
    }

    public function setSpeciesName($speciesName) {
        $this->speciesName = $speciesName;
    }

    public function getGeneSymbol() {
        return $this->geneSymbol;
    }

    public function setGeneSymbol($geneSymbol) {
        $this->geneSymbol = $geneSymbol;
    }

    public function getProteinDatabase() {
        return $this->proteinDatabase;
    }

    public function setProteinDatabase($proteinDatabase) {
        $this->proteinDatabase = $proteinDatabase;
    }

    public function getProteinId() {
        return $this->proteinId;
    }

    public function setProteinId($proteinId) {
        $this->proteinId = $proteinId;
    }

    public function getProteinLinkHref() {
        switch ($this->proteinDatabase) {
            case 'UniProtKB':   return self::getUniProtKbHref($this->proteinId);
            case 'NCBI':        return self::getNcbiProteinHref($this->proteinId);
        }
    }
    
    public static function getUniProtKbHref($id) {
        return 'http://www.uniprot.org/uniprot/' . $id;
    }
    
    public static function getNcbiProteinHref($id) {
        return 'http://www.ncbi.nlm.nih.gov/protein/' . $id;
    }
}
