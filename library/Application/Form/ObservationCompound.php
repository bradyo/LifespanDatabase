<?php

class Application_Form_ObservationFormCompound extends Zend_Form_SubForm
{
    public function init()
    {
        $idElement = new Zend_Form_Element_Hidden('id');
        $idElement->setLabel('Id')
            ->addValidator('Int')
            ->setDecorators(array('ViewHelper', 'Errors'));

        $ncbiCompoundIdElement = new Zend_Form_Element_Text('ncbiCompoundId');
        $ncbiCompoundIdElement->setLabel('NCBI Compound Id')
            ->addValidator('Int')
            ->setDecorators(array('ViewHelper', 'Errors'))
            ->setOptions(array('size' => '10'));

        $nameElement = new Zend_Form_Element_Text('name');
        $nameElement->setLabel('Name')
            ->setRequired(true)
            ->setDecorators(array('ViewHelper', 'Errors'))
            ->setOptions(array('size' => '16', 'class' => 'name'));

        $quantityElement = new Zend_Form_Element_Text('quantity');
        $quantityElement->setLabel('Quantity')
            ->setDecorators(array('ViewHelper', 'Errors'))
            ->setOptions(array('size' => '10'));


        $deleteElement = new Zend_Form_Element_Checkbox('isDeleted');
        $deleteElement->setLabel('Delete')
            ->setDecorators(array('ViewHelper'));

        $this->setElements(array(
            $idElement,
            $ncbiCompoundIdElement,
            $nameElement,
            $quantityElement,
            $deleteElement,
        ));
    }
}


