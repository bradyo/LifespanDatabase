<?php

class Application_Validate_ObservationValidator extends Zend_Validate {

    const FLOAT = 'float';

    protected $_messageTemplates = array(
        self::MISSING_GUID => "'%value%' is not a floating point value"
    );

    public function isValid(Application_Model_Observation $observation) {
        
        return true;
    }    
}
