<?php

class Application_Form_ObservationGene extends Zend_Form_SubForm
{
    public function init()
    {
        $idElement = new Zend_Form_Element_Hidden('id');
        $idElement->setLabel('Id')
            ->addValidator('Int')
            ->setDecorators(array('ViewHelper', 'Errors'));

        $ncbiGeneIdElement = new Zend_Form_Element_Text('ncbiGeneId');
        $ncbiGeneIdElement->setLabel('NCBI Gene Id')
            ->addValidator('Int')
            ->setDecorators(array('ViewHelper', 'Errors'))
            ->setOptions(array('size' => '10'));

        $symbolElement = new Zend_Form_Element_Text('symbol');
        $symbolElement->setLabel('Symbol')
            ->setDecorators(array('ViewHelper', 'Errors'))
            ->setOptions(array('size' => '6', 'class' => 'symbol'));

        $alleleTypeChoices = ObservationGene::getAlleleTypes();
        $alleleTypeElement = new Zend_Form_Element_Select('alleleType');
        $alleleTypeElement->setLabel('Allele Type')
            ->addMultiOptions($alleleTypeChoices)
            ->setDecorators(array('ViewHelper', 'Errors'));

        $alleleElement = new Zend_Form_Element_Text('allele');
        $alleleElement->setLabel('Allele')
            ->setDecorators(array('ViewHelper', 'Errors'))
            ->setOptions(array('size' => '6', 'class' => 'allele'));

        $deleteElement = new Zend_Form_Element_Checkbox('isDeleted');
        $deleteElement->setLabel('Delete')
            ->setDecorators(array('ViewHelper'));

        $this->setElements(array(
            $idElement,
            $ncbiGeneIdElement,
            $symbolElement,
            $alleleTypeElement,
            $alleleElement,
            $deleteElement,
        ));
    }

}


