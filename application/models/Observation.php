<?php 
/**
 * 
 * @Entity
 * @Table(name="observation")
 */
class Application_Model_Observation
{
    /**
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(name="body", type="string")
     */
    private $body;

    public function getBody() {
        return $this->body;
    }
    public function setBody($body) {
        $this->body = $body;
    }

}