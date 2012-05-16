<?php

/**
 * Description of ObservationBrowser
 *
 * @author brady
 */
class Application_Form_ObservationBrowser extends Zend_Form
{
    private $_observationBrowser;


    public function __construct($observationBrowser)
    {
        $this->_observationBrowser = $observationBrowser;
        parent::__construct();
    }

    public function init()
    {
        $this->setMethod(Zend_Form::METHOD_GET);
    }
}


