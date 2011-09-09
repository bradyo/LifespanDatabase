<?php

class Application_Registry {
    
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEm() {
        return Zend_Registry::get('em');
    }
    
}
