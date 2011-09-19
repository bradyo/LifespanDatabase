<?php

/**
 * @Entity
 * @Table(name="species")
 */
class Application_Model_Species
{
    /**
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
}
