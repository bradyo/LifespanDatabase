<?php

/**
 * @Entity
 * @Table(name="comment")
 */
class Application_Model_Comment 
{
    const STATUS_PUBLIC = "public";
    const STATUS_DELETED = "deleted";
    
    const REVIEW_STATUS_PENDING = "pending";
    const REVIEW_STATUS_ACCEPTED = "accepted";
    const REVIEW_STATUS_REJECTED = "rejected";
    
    /**
     * @var integer ID of the comment
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string Status of observation version.
     * @Column(name="status", type="string")
     */
    private $status;
    
    /**
     * @var string Review status of observation.
     * @Column(name="review_status", type="string")
     */
    private $reviewStatus;
        
    /**
     * @var DateTime When the author submitted the observation entry.
     * @Column(name="authored_at", type="datetime")
     */
    private $authoredAt;
    
    /**
     * @var Application_Model_User User that created the observation version.
     * @OneToOne(targetEntity="Application_Model_User", fetch="EAGER")
     * @JoinColumn(name="author_Id", referencedColumnName="id")
     */
    private $author;
       
    /**
     * @var DateTime When the observation entry was reviewed.
     * @Column(name="reviewed_at", type="datetime")
     */
    private $reviewedAt;
    
    /**
     * @var Application_Model_User User that reviewed the observation version.
     * @OneToOne(targetEntity="Application_Model_User", fetch="EAGER")
     * @JoinColumn(name="reviewer_id", referencedColumnName="id")
     */
    private $reviewer;
    
    /**
     * @var string Unique identifier for this comment's parent entity.
     * @Column(name="parent_guid", type="string")
     */
    private $parentGuid;

    /**
     * @var string Name of the author 
     * @Column(name="parent_guid", type="string", length="255")
     */
    private $authorName;
    
    /**
     * @var string E-mail of the author
     * @Column(name="parent_guid", type="string", length="255")
     */
    private $authorEmail;
    
    /**
     * @var string Body of the comment (can contain limited HTML).
     * @Column(name="body", type="text")
     */
    private $body;
    
    
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getReviewStatus() {
        return $this->reviewStatus;
    }

    public function setReviewStatus($reviewStatus) {
        $this->reviewStatus = $reviewStatus;
    }

    public function getAuthoredAt() {
        return $this->authoredAt;
    }

    public function setAuthoredAt($authoredAt) {
        $this->authoredAt = $authoredAt;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function setAuthor($author) {
        $this->author = $author;
    }

    public function getReviewedAt() {
        return $this->reviewedAt;
    }

    public function setReviewedAt($reviewedAt) {
        $this->reviewedAt = $reviewedAt;
    }

    public function getReviewer() {
        return $this->reviewer;
    }

    public function setReviewer($reviewer) {
        $this->reviewer = $reviewer;
    }

    public function getParentGuid() {
        return $this->parentGuid;
    }

    public function setParentGuid($parentGuid) {
        $this->parentGuid = $parentGuid;
    }

    public function getAuthorName() {
        return $this->authorName;
    }

    public function setAuthorName($authorName) {
        $this->authorName = $authorName;
    }

    public function getAuthorEmail() {
        return $this->authorEmail;
    }

    public function setAuthorEmail($authorEmail) {
        $this->authorEmail = $authorEmail;
    }

    public function getBody() {
        return $this->body;
    }

    public function setBody($body) {
        $this->body = $body;
    }
}
