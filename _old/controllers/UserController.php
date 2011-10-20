<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->identity = Zend_Auth::getInstance()->getIdentity();
    }

    public function loginAction()
    {
        $destination = $this->getRequest()->getParam('destination', null);

        $form = new Application_Form_LoginForm();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {
                $auth = Zend_Auth::getInstance();

                $db = Zend_Registry::get('db');
                $adapter = new Zend_Auth_Adapter_DbTable($db, 'user',
                    'username', 'password', 'MD5(?)');
                $adapter->setIdentity($form->getValue('username'));
                $adapter->setCredential($form->getValue('password'));

                $result = $auth->authenticate($adapter);
                if ($result->isValid()) {
                    $username = $form->getValue('username');
                    $data = $adapter->getResultRowObject(null, array('password'));
                    $auth->getStorage()->write($data);

                    if ($destination !== null) {
                        $this->_redirect($destination, array('prependBase'=>false));
                    } else {
                        $this->_redirect('account');
                    }
                } else {
                    $this->view->message = 'Username and password incorrect.';
                }
            }
        }
        $this->view->form = $form;
    }

    public function logoutAction()
    {
        $destination = $this->getRequest()->getParam('destination', null);

        Zend_Auth::getInstance()->clearIdentity();
        
        if ($destination !== null) {
            $this->_redirect($destination, array('prependBase'=>false));
        } else {
            $this->_redirect('/');
        }
    }

}

