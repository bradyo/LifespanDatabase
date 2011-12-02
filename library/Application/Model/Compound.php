<?php

namespace Application\Model;

/**
 * @Entity
 * @Table(name="compound")
 */
class Compound
{
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string Common name of the compound.
     * @Column(name="name", type="string")
     */
    private $name;
    
    /**
     * @var string
     * @Column(name="description", type="string")
     */
    private $description;
    
    /**
     * Corresponding NCBI compound ID.
     * @Column(name="ncbi_compound_id", type="integer")
     */
    private $ncbiCompoundId;
    
    /**
     * @OneToMany(targetEntity="Application\Model\CompoundSynonym", mappedBy="compound")
     */
    private $synonyms;
    
    
    
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

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getNcbiCompoundId() {
        return $this->ncbiCompoundId;
    }

    public function setNcbiCompoundId($ncbiCompoundId) {
        $this->ncbiCompoundId = $ncbiCompoundId;
    }
    
    public function getSynonyms() {
        return $this->synonyms;
    }
}
