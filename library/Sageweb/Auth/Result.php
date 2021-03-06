<?php

class Sageweb_Auth_Result extends Zend_Auth_Result
{
    /**
     * General Failure
     */
    const FAILURE                        =  0;

    /**
     * Failure due to identity not being found.
     */
    const FAILURE_IDENTITY_NOT_FOUND     = -1;

    /**
     * Failure due to invalid credential being supplied.
     */
    const FAILURE_CREDENTIAL_INVALID     = -2;

    /**
     * Failure due to user being blocked.
     */
    const FAILURE_BLOCKED     = -3;

    /**
     * Authentication success.
     */
    const SUCCESS                        =  1;

    /**
     * Authentication result code
     *
     * @var int
     */
    protected $_code;

    /**
     * The identity used in the authentication attempt
     *
     * @var mixed
     */
    protected $_identity;

    /**
     * An array of string reasons why the authentication attempt was unsuccessful
     *
     * If authentication was successful, this should be an empty array.
     *
     * @var array
     */
    protected $_messages;

    /**
     * Sets the result code, identity, and failure messages
     *
     * @param  int     $code
     * @param  mixed   $identity
     * @param  array   $messages
     * @return void
     */
    public function __construct($code, $identity, array $messages = array())
    {
        $code = (int) $code;

        $this->_code     = $code;
        $this->_identity = $identity;
        $this->_messages = $messages;
    }

    /**
     * Returns whether the result represents a successful authentication attempt
     *
     * @return boolean
     */
    public function isValid()
    {
        return ($this->_code > 0) ? true : false;
    }

    /**
     * getCode() - Get the result code for this authentication attempt
     *
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * Returns the identity used in the authentication attempt
     *
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * Returns an array of string reasons why the authentication attempt was unsuccessful
     *
     * If authentication was successful, this method returns an empty array.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }
}
