<?php

namespace Application\Model;

/**
 * @Entity
 * @Table(name="compound_synonym")
 */
class CompoundSynonym
{
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Compound
     * @ManyToOne(targetEntity="\Application\Model\Compound", inversedBy="synonyms")
     * @JoinColumn(name="compound_id", referencedColumnName="id")
     */
    private $compound;
        
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

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }
    
    public function getCompound() {
        return $this->compound;
    }

    public function setCompound($compound) {
        $this->compound = $compound;
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
            'name' => $this->getName(),
        );
        return $data;
    }
}

