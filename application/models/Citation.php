<?php

namespace Application\Model;

/**
 * @Entity
 * @Table(name="citation")
 */
class Citation 
{
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * Year the publication was published.
     * @var integer
     * @Column(name="year", type="integer")
     */
    private $year;
    
    /**
     * Authors of the publication, multiple authors separated by commas.
     * @var string
     * @Column(name="author", type="text")
     */
    private $authors;
    
    /**
     * The source information of the citation, like the journal name and page
     * numbers.
     * @var string
     * @Column(name="source", type="text")
     */
    private $source;
    
    /**
     * Pubmed ID of this citaiton if available.
     * @var int
     * @Column(name="pubmed_id", type="integer")
     */
    private $pubmedId;
    
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getYear() {
        return $this->year;
    }

    public function setYear($year) {
        $this->year = $year;
    }

    public function getAuthors() {
        return $this->authors;
    }

    public function setAuthors($authors) {
        $this->authors = $authors;
    }

    public function getSource() {
        return $this->source;
    }

    public function setSource($source) {
        $this->source = $source;
    }

    public function getPubmedId() {
        return $this->pubmedId;
    }

    public function setPubmedId($pubmedId) {
        $this->pubmedId = $pubmedId;
    }
}
