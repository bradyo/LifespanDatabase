<?php

/**
 * @Entity 
 * @Table(name="observation_stats")
 */
class Application_Model_ObservationStatistics 
{
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
