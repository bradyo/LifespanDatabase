<?php

/**
 * @Entity
 * @Table(name="observation")
 */
class Application_Model_Observation 
{    
    /**
     * @var integer ID of the observation
     * @Id 
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Application_Model_Measurement Data for observation.
     * @OneToOne(targetEntity="Application_Model_Measurement")
     * @JoinColumn(name="id", referencedColumnName="id")
     */
    private $measurement;
}
    

/**
 * @Entity
 * @Table(name="observation")
 */
class Application_Model_Measurement {
    
    /**
     * @var integer ID of the parent observation
     * @Id 
     * @Column(name="id", type="integer")
     */
    private $id;
    
    /**
     * @var double Measurement value.
     * @Column(name="test_value", type="float")
     */
    private $testValue;
    
    /**
     * @var double Measurement control value.
     * @Column(name="control_value", type="float")
     */
    private $controlValue;
    
    /**
     * Gets the log base e ratio of the measurement vs control.
     * @return float
     */
    public function getLogRatio() {
        return log($this->testValue / $this->controlValue);
    }
}

/** SQL:

CREATE TABLE observation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_value DOUBLE,
    control_value DOUBLE,
);

 */