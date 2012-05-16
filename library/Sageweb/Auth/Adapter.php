<?php

/**
 * This class authenticates against the user table and stores a user object in
 * the Zend_Auth identity if successful.
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Auth_Adapter implements Zend_Auth_Adapter_Interface
{
    private $_db;
    private $_username;
    private $_password;
    private $_row ;

    public function __construct($db) {
        $this->_db = $db;
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
        $db = $this->_db;
        $stmt = $db->prepare('
            SELECT * FROM user WHERE username = ?
            ');
        $stmt->execute(array($this->_username));

        // if we have a user, check the hashed password
        $row = $stmt->fetch();
        if ($row) {
            unset($row['password']);
            $this->_row = $row;

            $userId = $row['id'];
            $algorithm = $row['password_algorithm'];
            
            $hashedPassword = $this->_password;
            if ($algorithm == 'sha1') {
                $hashedPassword = sha1($this->_password . $row['password_salt']);
            } elseif ($algorithm == 'md5') {
                $hashedPassword = md5($this->_password . $row['password_salt']);
            }

            if ($hashedPassword == $row['password_hash']) {
                if ($row['status'] == 'blocked') {
                    return new Sageweb_Auth_Result(Sageweb_Auth_Result::FAILURE_BLOCKED, null);
                }
                return new Sageweb_Auth_Result(Sageweb_Auth_Result::SUCCESS, $this->_username);
            }
        }
        return new Sageweb_Auth_Result(Sageweb_Auth_Result::FAILURE, null);
    }

    public function getResultRowObject() {
        return (object) $this->_row;
    }
}


