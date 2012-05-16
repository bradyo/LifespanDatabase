<?php

class AccountController extends Zend_Controller_Action
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

        $form = new Application_Form_Login();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {

            // authenticate the user with auth adapter
            $db = Zend_Registry::get('sagewebDb');
            $adapter = new Sageweb_Auth_Adapter($db);
            $adapter->setUsername($form->getValue('username'));
            $adapter->setPassword($form->getValue('password'));
            $result = Zend_Auth::getInstance()->authenticate($adapter);
            if ($result->isValid()) {
                // get the user object
                $username = $form->getValue('username');
                $data = $adapter->getResultRowObject();
                $data->is_moderator = ($data->role == 'moderator' || $data->role == 'admin');
                $auth = Zend_Auth::getInstance();
                $auth->getStorage()->write($data);

                // redirect to given destination, or to profile page
                if ($destination !== null) {
                        $this->_redirect($destination, array('prependBase'=>false));
                    } else {
                        $this->_redirect('account');
                    }
            } elseif ($result->getCode() == Sageweb_Auth_Result::FAILURE_BLOCKED) {
                $this->view->message = 'User has been blocked. Please contact support@sageweb.org to get re-activated.';
            } else {
                $this->view->message = 'Username and password incorrect.';
            }

  //              $auth = Zend_Auth::getInstance();

     //           $db = Zend_Registry::get('db');
    ///            $adapter = new Zend_Auth_Adapter_DbTable($db, 'user',
    //                'username', 'password', 'MD5(?)');
     //           $adapter->setIdentity($form->getValue('username'));
     //           $adapter->setCredential($form->getValue('password'));

      //          $result = $auth->authenticate($adapter);
        //        if ($result->isValid()) {
            //        $username = $form->getValue('username');
          //          $data = $adapter->getResultRowObject(null, array('password'));
              //      $auth->getStorage()->write($data);

               //     if ($destination !== null) {
                //        $this->_redirect($destination, array('prependBase'=>false));
     //               } else {
      //                  $this->_redirect('account');
        //            }
          //      } else {
            //        $this->view->message = 'Username and password incorrect.';
              //  }
            }
        }
        $this->view->form = $form;
    }

    public function logoutAction()
    {
        $destination = $this->getRequest()->getParam('destination', null);

        Zend_Auth::getInstance()->clearIdentity();
        
        $this->_redirect('/');
    }

}

