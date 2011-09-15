<?php

/**
 * @Entity
 * @Table(name="citation")
 */
class Application_Model_Citation 
{
    /**
     * @var integer
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var integer Year the publication was published.
     * @Column(name="year", type="integer")
     */
    private $year;
    
    /**
     * @var string Authors of the publication, multiple authors separated by commas.
     * @Column(name="author", type="text")
     */
    private $authors;
    
    /**
     * @var string Source information like the journal, name, and page.
     * @Column(name="source", type="text")
     */
    private $source;
    
    /**
     * @var integer Pubmed ID of this citaiton if available.
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
