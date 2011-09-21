<?php

/**
 * @Entity
 * @Table(name="gene_go")
 */
class Application_Model_GeneGoTerm 
{  
    const CATEGORY_FUNCTION = 'Function';
    const CATEGORY_PROCESS = 'Process';
    const CATEGORY_COMPONENT = 'Component';
    
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Application_Model_Gene
     * @ManyToOne(targetEntity="Application_Model_Gene", inversedBy="goTerms")
     * @JoinColumn(name="gene_id", referencedColumnName="id")
     */
    private $gene;
        
    /**
     * @var string Official GO term ID.
     * @Column(name="go_id", type="string", length="64")
     */
    private $termId;
    
    /**
     * @var string GO Evidence code, see: http://www.geneontology.org/GO.evidence.tree.shtml
     * @Column(name="evidence", type="string", length="64")
     */
    private $evidenceCode;
    
    /**
     * @var string Category of term
     * @Column(name="go_id", type="string", length="64")
     */
    private $category;
    
    /**
     * @var string Category of term
     * @Column(name="term", type="string", length="255")
     */
    private $description;
    
    
    
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

    public function getTermId() {
        return $this->termId;
    }

    public function setTermId($termId) {
        $this->termId = $termId;
    }

    public function getEvidenceCode() {
        return $this->evidenceCode;
    }

    public function setEvidenceCode($evidenceCode) {
        $this->evidenceCode = $evidenceCode;
    }

    public function getCategory() {
        return $this->category;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }
}
