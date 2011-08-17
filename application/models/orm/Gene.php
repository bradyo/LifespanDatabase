<?php

/**
 * Gene
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Gene extends BaseGene
{
    public function postSave($event) {
        parent::postSave($event);
        $this->_updateGeneHomologs();
    }

    private function _updateGeneHomologs()
    {
        // update homolog parent links to this gene
        $homologGenes = Doctrine_Core::getTable('GeneHomolog')
            ->findBy('homologNcbiGeneId', $this->ncbiGeneId);
        foreach ($homologGenes as $homologGene) {
            $homologGene->homologGeneId = $this->id;
            $homologGene->save();
        }
    }
}