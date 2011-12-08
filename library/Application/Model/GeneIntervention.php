<?php

namespace Application\Model;

/**
 * @Entity
 * @Table(name="observation_gene")
 */
class GeneIntervention
{
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Observation
     * @ManyToOne(targetEntity="Application\Model\Observation", inversedBy="geneInterventions")
     * @JoinColumn(name="observation_id", referencedColumnName="id")
     */
    private $observation;
    
    /**
     * @var Gene
     * @OneToOne(targetEntity="Application\Model\Gene", fetch="EAGER")
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
    
    public function toArray() {
        $data = array(
            'id' => $this->id,
            'alleleType' => $this->alleleType,
            'allele' => $this->allele,
            'gene' => $this->gene->toArray(),
        );
        return $data;
    }
}
