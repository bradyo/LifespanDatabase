<?php

/**
 * @Entity
 * @Table(name="strain")
 * @HasLifecycleCallbacks
 */
class Application_Model_Strain
{
    /**
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string Globally unique identifier.
     * @Column(name="guid", type="string", length="36")
     */
    private $guid;
    
    /**
     * @var Application_Model_Species Species strain belongs to.
     * @OneToOne(targetEntity="Application_Model_Species")
     * @JoinColumn(name="species_id", referencedColumnName="id")
     */     
    private $species;
    
    /**
     * @var string Full strain name.
     * @Column(name="name", type="string", length="128")
     */
    private $name;
    
    /**
     * @var string Strain common name.
     * @Column(name="common_name", type="string", length="128")
     */
    private $commonName;
    
    
    /** 
     * Generates a unique GUID.
     * @PrePersist  
     */
    public function generateGuid() {
        if (empty($this->guid)) {
            $this->guid = Application_Guid::generate();
        }
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getGuid() {
        return $this->guid;
    }

    public function setGuid($guid) {
        $this->guid = $guid;
    }

    public function getSpecies() {
        return $this->species;
    }

    public function setSpecies($species) {
        $this->species = $species;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getCommonName() {
        return $this->commonName;
    }

    public function setCommonName($commonName) {
        $this->commonName = $commonName;
    }
}