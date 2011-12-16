<?php

namespace Application\Model;



/**
 * @Entity
 * @Table(name="observation_compound")
 */
class CompoundIntervention
{
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Observation
     * @ManyToOne(targetEntity="Application\Model\Observation", inversedBy="compoundInterventions")
     * @JoinColumn(name="observation_id", referencedColumnName="id")
     */
    private $observation;
    
    /**
     * @var Application_Model_Compound
     * @OneToOne(targetEntity="Application\Model\Compound", fetch="EAGER")
     * @JoinColumn(name="compound_id", referencedColumnName="id")
     */
    private $compound;
    
    /**
     * @var string
     * @Column(name="quantity", type="string")
     */
    private $quantity;
    
    
    
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

    public function getCompound() {
        return $this->compound;
    }

    public function setCompound($compound) {
        $this->compound = $compound;
    }

    public function getQuantity() {
        return $this->quantity;
    }

    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }
}
