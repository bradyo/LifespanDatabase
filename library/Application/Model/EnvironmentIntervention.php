<?php

namespace Application\Model;

/**
 * @Entity
 * @Table(name="observation_environment")
 */
class EnvironmentIntervention
{
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Observation
     * @ManyToOne(targetEntity="Application\Model\Observation", inversedBy="environmentInterventions")
     * @JoinColumn(name="observation_id", referencedColumnName="id")
     */
    private $observation;
    
    /**
     * @var Environment
     * @OneToOne(targetEntity="Application\Model\Environment", fetch="EAGER")
     * @JoinColumn(name="environment_id", referencedColumnName="id")
     */
    private $environment;
    
    /**
     * @var string
     * @Column(name="description", type="string")
     */
    private $description;
    
    
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

    public function getEnvironment() {
        return $this->environment;
    }

    public function setEnvironment($environment) {
        $this->environment = $environment;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }
}
