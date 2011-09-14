<?php

/**
 * @Entity
 * @Table(name="observation_gene")
 */
class Application_Model_GeneIntervention {
    
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
     * @var string
     * @Column(name="allele_type", type="string")
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

    public function getObservation() {
        return $this->observation;
    }

    public function setObservation($observation) {
        $this->observation = $observation;
    }

    /**
     *
     * @return Application_Model_Gene
     */
    public function getGene() {
        return $this->gene;
    }

    public function setGene($gene) {
        $this->gene = $gene;
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

        
    
    public static function getAlleleTypes() {
        $alleleTypes = array(
            'normal' => 'normal',
            'over-expression' => 'over-expression',
            'deletion / null' => 'deletion / null',
            'non-null recessive' => 'non-null recessive',
            'non-null dominant' => 'non-null dominant',
            'non-null semi-dominant' => 'non-null semi-dominant',
            'RNAi knockdown' => 'RNAi knockdown',
            'anti-sense RNA' => 'anti-sense RNA',
            'loss of function' => 'loss of function',
            'gain of function' => 'gain of function',
            'dominant negative' => 'dominant negative',
            'heterozygous diploid' => 'heterozygous diploid',
            'unknown' => 'unknown',
        );
        return $alleleTypes;
    }
}
