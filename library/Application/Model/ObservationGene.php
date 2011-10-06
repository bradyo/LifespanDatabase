<?php

/**
 * @Entity
 * @Table(name="observation_gene")
 */
class Application_Model_ObservationGene
{
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Application_Model_Observation
     * @ManyToOne(targetEntity="Application_Model_Observation", inversedBy="geneInterventions")
     * @JoinColumn(name="observation_id", referencedColumnName="id")
     */
    private $observation;
    
    /**
     * @var Application_Model_Gene
     * @OneToOne(targetEntity="Application_Model_Gene", fetch="EAGER")
     * @JoinColumn(name="gene_id", referencedColumnName="id")
     */
    private $gene;
    
    /**
     * @var string Gene symbol.
     * @Column(name="symbol", type="string", length="64")
     */
    private $symbol;
    
    /**
     * @var string
     * @Colu*mn(name="allele_type", type="string")
     */
    private $alleleType;
    
    /**
     * @var string
     * @Column(name="allele", type="string")
     */
    private $allele;
    
    
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
    
    public function getSymbol() {
        return $this->symbol;
    }

    public function setSymbol($symbol) {
        $this->symbol = $symbol;
    }

    public function getAlleleType() {
        return $this->alleleType;
    }

    public function setAlleleType($alleleType) {
        $this->alleleType = $alleleType;
    }

    public function getAllele() {
        return $this->allele;
    }

    public function setAllele($allele) {
        $this->allele = $allele;
    }
    
    public function getObservation() {
        return $this->observation;
    }

    public function setObservation($observation) {
        $this->observation = $observation;
    }
}
