<?php

/**
 * @Entity
 * @Table(name="species_synonym")
 */
class Application_Model_SpeciesSynonym
{
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Application_Model_Species
     * @ManyToOne(targetEntity="Application_Model_Species", inversedBy="synonyms")
     * @JoinColumn(name="species_id", referencedColumnName="id")
     */
    private $species;
    
    /**
     * @var string Synonym name of the species.
     * @Column(name="name", type="string", length="64")
     */
    private $name;
    
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }
}
