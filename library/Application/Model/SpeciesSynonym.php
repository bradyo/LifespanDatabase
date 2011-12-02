<?php

namespace Application\Model;

/**
 * @Entity
 * @Table(name="species_synonym")
 */
class SpeciesSynonym
{
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Species
     * @ManyToOne(targetEntity="Application\Model\Species", inversedBy="synonyms")
     * @JoinColumn(name="species_id", referencedColumnName="id")
     */
    private $species;
    
    /**
     * @var string Type of synonym (i.e. common, mis-spelling, etc)
     * @Column(name="type", type="string", length="64")
     */
    private $type;
    
    /**
     * @var string Synonym name of the species.
     * @Column(name="name", type="string", length="128")
     */
    private $name;
    
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }
    
    public function getSpecies() {
        return $this->species;
    }

    public function setSpecies($species) {
        $this->species = $species;
    }

        
    public function fromArray($data) {
        $properties = get_object_vars($this);
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $properties)) {
                $setter = 'set' . ucfirst($key);
                $this->{$setter}($value);
            }
        }
    }
    
    public function toArray() {
        $data = array(
            'id' => $this->getId(),
            'type' => $this->getType(),
            'name' => $this->getName(),
        );
        return $data;
    }
}
