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
     * @var string Full title of the publication.
     * @Column(name="title", type="text")
     */
    private $title;
    
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
        
    /**
     * @var string E-mail for correspondance
     * @Column(name="correspondance_email", type="text")
     */
    private $correspondanceEmail;
    
    
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
    
    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
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
    
    public function getCorrespondanceEmail() {
        return $this->correspondanceEmail;
    }

    public function setCorrespondanceEmail($correspondanceEmail) {
        $this->correspondanceEmail = $correspondanceEmail;
    }
}
