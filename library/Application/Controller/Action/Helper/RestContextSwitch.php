<?php

class Application_Controller_Action_Helper_RestContextSwitch 
    extends Zend_Controller_Action_Helper_ContextSwitch
{   
    protected $_autoSerialization = true;
 
    protected $_availableAdapters = array(
        'json'  => 'Zend_Serializer_Adapter_Json',
        'xml'   => 'Application_Serializer_Adapter_Xml',
    );
 
    public function __construct($options = null) {
        if ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }
        elseif (is_array($options)) {
            $this->setOptions($options);
        }
 
        $this->addContexts(
            array( 
                'json' => array(
                    'suffix'    => 'json',
                    'headers'   => array(
                        'Content-Type' => 'application/json'
                    ),
                    'callbacks' => array(
                        'init' => 'initAbstractContext',
                        'post' => 'restContext'
                    ),
                ),
                'xml' => array(
                    'suffix'    => 'xml',
                    'headers'   => array(
                        'Content-Type' => 'application/xml'
                    ),
                    'callbacks' => array(
                        'init' => 'initAbstractContext',
                        'post' => 'restContext'
                    ),
                ),
            )
        );
        $this->init();
    }
 
    public function initAbstractContext() {
        if (!$this->getAutoSerialization()) {
            return;
        }
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;
        if ($view instanceof Zend_View_Interface) {
            $viewRenderer->setNoRender(true);
        }
    }
 
    public function restContext() {
        if (!$this->getAutoSerialization()) {
            return;
        }
 
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;
        if ($view instanceof Zend_View_Interface) {
            if (method_exists($view, 'getVars')) {
                $data = $view->getVars();
                if (count($data) !== 0) {
                    $serializer = new $this->_availableAdapters[$this->_currentContext];
                    $body = $serializer->serialize($view->getVars());
                    
                    if ($this->_currentContext == 'json') {
                        $callback = $this->getRequest()->getParam('jsonp-callback', false);
                        if ($callback !== false and !empty($callback)) {
                            $body = sprintf('%s(%s)', $callback, $body);
                        }
                    }
                    $this->getResponse()->setBody($body);
                }
            }
        }
    }
 
    public function setAutoSerialization($flag) {
        $this->_autoSerialization = (bool) $flag;
        return $this;
    }
 
    public function getAutoSerialization() {
        return $this->_autoSerialization;
    }
}
