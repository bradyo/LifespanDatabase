<?php

class Application_Form_CitationForm extends Zend_Form
{
    public function init() {
        $this->addElement('hidden', 'id', array(
           'required' => false,
        ));
        
        $this->addElement('text', 'citationPubmedId', array(
            'label' => 'PubMed Id:',
            'validators' => array(
                array('validator' => 'Int'),
            ),
            'size' => 8,
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));
        
        $this->addElement('text', 'citationYear', array(
            'label' => 'Year:',
            'validators' => array(
                array('validator' => 'Int'),
            ),
            'size' => 4,
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));
        
        $this->addElement('text', 'citationAuthor', array(
            'label' => 'Author:',
            'required' => true,
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 255)),
            ),
            'size' => 60,
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));
        
        $this->addElement('text', 'citationTitle', array(
            'label' => 'Title:',
            'required' => true,
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 255)),
            ),
            'size' => 60,
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));
        
        $this->addElement('text', 'citationSource', array(
            'label' => 'Source:',
            'required' => true,
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, 255)),
            ),
            'size' => 60,
            'decorators' => array('Label', 'ViewHelper', 'Errors'),
        ));
    }
}
