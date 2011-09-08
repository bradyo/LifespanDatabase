<?php

class Application_Model_Validate_Float extends Zend_Validate_Abstract
{
      const FLOAT = 'float';

      protected $_messageTemplates = array(
          self::FLOAT => "'%value%' is not a floating point value"
      );

      public function isValid($value)
      {
          $this->_setValue($value);
          if (!is_float($value)) {
              $this->_error();
              return false;
          }
          return true;
      }
}