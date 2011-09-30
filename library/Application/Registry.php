<?php

class Application_Registry {
    
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEm() {
        return Zend_Registry::get('em');
    }
    
    /**
     * @return Zend_Db_Adapter_Mysqli
     */
    public static function getDb() {
        return Zend_Registry::get('db');
    }
}
