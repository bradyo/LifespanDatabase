<?php

/**
 * @Entity
 * @Table(name="strain")
 */
class Application_Model_Strain
{
    /**
     * @Id @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
}
