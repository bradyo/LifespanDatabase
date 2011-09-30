<?php

class Application_Form_CommentForm extends Zend_Form
{
    public function init()
    {
        $this->setMethod(Zend_Form::METHOD_POST);
        $this->setAction('');

        $this->addElement('textarea', 'body', array(
            'label'      => 'Comment:',
            'required'   => true,
            'rows'       => 3,
            'cols'       => 40,
            'style' => 'width: 98%; ',
        ));

        $this->addElement('submit', 'Submit', array(
            'ignore'   => true,
            'label'    => 'Submit',
            'style'     => 'width:10em'
        ));

        // set decorator to view script
        $this->setDecorators(array(
            array('ViewScript', array('viewScript' => 'forms/comment.phtml'))
        ));
    }
}


