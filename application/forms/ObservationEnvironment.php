<?php

class Application_Form_ObservationEnvironment extends Zend_Form_SubForm
{
    public function init()
    {
        $idElement = new Zend_Form_Element_Hidden('id');
        $idElement->setLabel('Id')
            ->addValidator('Int')
            ->setDecorators(array('ViewHelper', 'Errors'));

        $typeElement = new Zend_Form_Element_Text('type');
        $typeElement->setLabel('Type')
            ->setDecorators(array('ViewHelper', 'Errors'))
            ->setOptions(array('size' => '14'));

        $bodyElement = new Zend_Form_Element_Textarea('body');
        $bodyElement->setLabel('Description')
            ->setDecorators(array('ViewHelper', 'Errors'))
            ->setOptions(array(
                'rows' => '3',
                'cols' => '60',
								'accept-charset' => 'utf-8',
            )
        );

        $deleteElement = new Zend_Form_Element_Checkbox('isDeleted');
        $deleteElement->setLabel('Delete')
            ->setDecorators(array('ViewHelper'));

        $this->setElements(array(
            $idElement,
            $typeElement,
            $bodyElement,
            $deleteElement,
        ));
    }
}


