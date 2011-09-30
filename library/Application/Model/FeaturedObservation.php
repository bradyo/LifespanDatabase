<?php

/**
 * @Entity
 * @Table(name="featured_observation")
 */
class Application_Model_FeaturedObservation 
{
    /**
     * @var integer ID of the featured observation.
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Application_Model_Observation
     * @OneToOne(targetEntity="Application_Model_Observation")
     * @JoinColumn(name="observation_id", referencedColumnName="id")
     */
    private $observation;
    
    /**
     * @var integer Relative position in a list of ordered entities.
     * @Column(name="position", type="integer")
     */
    private $position;
    
    
    
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

    public function getPosition() {
        return $this->position;
    }

    public function setPosition($position) {
        $this->position = $position;
    }
}
