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
    
    /**
     * @return Application_Model_User
     */
    public static function getCurrentUser() {
        if (Zend_Registry::isRegistered('currentUser')) {
            return Zend_Registry::get('currentUser');
        } else {
            return new Application\Model\DefaultUser();
        }
    }
}
