<?php

class Application_Form_ObservationFormGeneForm extends Zend_Form_SubForm
{
    private $alleleTypes;
    
    public function __construct($options = null) {
        parent::__construct($options);
        $this->alleleTypes = new Application_Model_AlleleTypes();
    }
    
    public function init() {
        $element = new Zend_Form_Element_Hidden('id');
        $element->setLabel('Id')
            ->addValidator('Int')
            ->setDecorators(array('ViewHelper', 'Errors'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('ncbiGeneId');
        $element->setLabel('NCBI Gene Id')
            ->addValidator('Int')
            ->setDecorators(array('ViewHelper', 'Errors'))
            ->setOptions(array('size' => '10'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('symbol');
        $element->setLabel('Symbol')
            ->setDecorators(array('ViewHelper', 'Errors'))
            ->setOptions(array('size' => '6', 'class' => 'symbol'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Select('alleleType');
        $element->setLabel('Allele Type')
            ->addMultiOptions($this->alleleTypes->getChoices())
            ->setDecorators(array('ViewHelper', 'Errors'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Text('allele');
        $element->setLabel('Allele')
            ->setDecorators(array('ViewHelper', 'Errors'))
            ->setOptions(array('size' => '6', 'class' => 'allele'));
        $this->addElement($element);

        $element = new Zend_Form_Element_Checkbox('isDeleted');
        $element->setLabel('Delete')
            ->setDecorators(array('ViewHelper'));
        $this->addElement($element);
    }
}


