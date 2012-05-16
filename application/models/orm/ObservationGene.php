<?php

/**
 * ObservationGene
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ObservationGene extends BaseObservationGene
{

    public function preSave($event) {
        parent::preSave($event);
        
        if ($this->ncbiGeneId !== null) {
            $this->_updateGene();
        }
    }

    private function _updateGene()
    {
        // fetch the gene object if it exists. If it does not exist, we need
        // to import it from external databases
        $gene = Doctrine_Core::getTable('Gene')->findOneBy('ncbiGeneId', $this->ncbiGeneId);
        if (!$gene) {
            $gene = Doctrine_Core::getTable('Gene')->import($this->ncbiGeneId);
        }

        if ($gene) {
            $this->geneId = $gene->id;
        }
    }

  
    public static function getAlleleTypes()
    {
        $alleleTypes = array(
            'normal' => 'normal',
            'over-expression' => 'over-expression',
            'deletion / null' => 'deletion / null',
            'non-null recessive' => 'non-null recessive',
            'non-null dominant' => 'non-null dominant',
            'non-null semi-dominant' => 'non-null semi-dominant',
            'RNAi knockdown' => 'RNAi knockdown',
            'anti-sense RNA' => 'anti-sense RNA',
            'loss of function' => 'loss of function',
            'gain of function' => 'gain of function',
            'dominant negative' => 'dominant negative',
            'heterozygous diploid' => 'heterozygous diploid',
            'unknown' => 'unknown',
        );
        return $alleleTypes;
    }
}