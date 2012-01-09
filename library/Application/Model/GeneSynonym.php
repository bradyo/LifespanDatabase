<?php

namespace Application\Model;

/**
 * @Entity
 * @Table(name="gene_synonym")
 */
class GeneSynonym
{
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var Application\Model\Gene
     * @ManyToOne(targetEntity="Application\Model\Gene", inversedBy="synonyms")
     * @JoinColumn(name="gene_id", referencedColumnName="id")
     */
    private $gene;
    
    /**
     * @var string Synonym of the compound.
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
