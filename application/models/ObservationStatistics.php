<?php

/**
 * @Entity 
 * @Table(name="observation_stats")
 */
class Application_Model_ObservationStatistics 
{
    /**
     * @var integer ID of the observation version (each version has different ID)
     * @Id 
     * @Column(name="id", type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var integer ID of the observation version (each version has different ID)
     * @Column(name="observation_id", type="integer")
     */
    private $observationId;
    
    /**
     * @var integer Number of gene interventions.
     * @Column(name="gene_count", type="integer")
     */
    private $geneCount;
    
    /**
     * @var integer Number of compound interventions.
     * @Column(name="compound_count", type="integer")
     */
    private $compoundCount;
    
    /**
     * @var integer Number of environment interventions.
     * @Column(name="environment_count", type="integer")
     */
    private $environmentCount;
}
