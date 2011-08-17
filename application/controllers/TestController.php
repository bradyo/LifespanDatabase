<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
//        $observation = new Observation();
//        $observation->body = 'testing save';
//
//        $gene = new ObservationGene();
//        $gene->alleleType = 'deletion';
//        $gene->ncbiGeneId = 177924;
//        $gene->symbol = 'sir-2.1';
//        $observation->genes[] = $gene;
//        $observation->save();

        GeneTable::import(177924);
        GeneTable::import(851520);

        die();
    }


}

