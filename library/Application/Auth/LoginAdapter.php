<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AuthAdapeter
 *
 * @author brady
 */
class Application_Auth_LoginAdapeter implements Zend_Auth_Adapter_Interface
{
    private $_username;
    private $_password;

    public function __construct()
    {
    }

    public function setUsername($username)
    {
        $this->_username = $username;
    }

    public function setPassword($password)
    {
        $this->_password = $password;
    }

    public function authenticate() 
    {
        $result = new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->_username);
        return $result;
    }

}


