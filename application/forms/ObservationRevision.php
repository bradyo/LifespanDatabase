<?php

/**
 * Description of ObservationRevision form to accept/reject a revision
 *
 * @author brady
 */
class Application_Form_ObservationRevision extends Zend_Form
{
    public function init()
    {
        $this->setMethod(Zend_Form::METHOD_POST);

        $this->addElement('select', 'status', array(
            'label' => 'Set Status:',
            'multiOptions' => ObservationRevision::getStatusChoices(),
            'required' => true,
            'decorators' => array(
                'Label', 'ViewHelper'
            )
        ));

        $this->addElement('textarea', 'comment', array(
            'label' => 'Review Comment:',
            'rows' => 4,
            'cols' => 80,
						'accept-charset' => 'utf-8',
        ));

        $this->addElement('submit', 'Submit');
    }
}


