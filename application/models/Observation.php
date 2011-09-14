<?php 

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 * @Table(name="observation")
 */
class Application_Model_Observation
{
    /**
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="body", type="string")
     */
    private $body;
    
    /**
     * @Column(name="lifespan_effect", type="string")
     */    
    private $lifespanEffect;
    
    
//    /**
//     * @OneToOne(targetEntity="Application_Model_Citation")
//     * @JoinColumn(name="citation_id", referencedColumnName="id")
//     */
//    private $citation;
//    
    
    /**
     * @OneToMany(targetEntity="Application_Model_GeneIntervention", mappedBy="observation",
     *      fetch="EAGER")
     */
    private $geneInterventions;
    
    

    public function __construct() {
        $this->geneInterventions = new ArrayCollection();
    }
    
    public function getBody() {
        return $this->body;
    }
    public function setBody($body) {
        $this->body = $body;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getCitation() {
        return $this->citation;
    }

    public function setCitation($citation) {
        $this->citation = $citation;
    }

    /**
     *
     * @return array(Application_Model_GeneIntervention)
     */
    public function getGeneInterventions() {
        return $this->geneInterventions;
    }

    public function setGeneInterventions($geneInterventions) {
        $this->geneInterventions = $geneInterventions;
    }

    
}