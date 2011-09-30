<?php

/**
 * @Entity
 * @Table(name="observation_environment")
 */
class Application_Model_ObservationEnvironment 
{
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Application_Model_Observation
     * @ManyToOne(targetEntity="Application_Model_Observation", inversedBy="environmentInterventions")
     * @JoinColumn(name="observation_id", referencedColumnName="id")
     */
    private $observation;
    
    /**
     * @var Application_Model_Environment
     * @OneToOne(targetEntity="Application_Model_Environment", fetch="EAGER")
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
