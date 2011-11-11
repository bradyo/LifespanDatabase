<?php

/**
index {html, xml, json}
show {html, xml, json}
add {html}
edit {html}
create {html(set flash, redirect to get/edit), xml, json}
update {html(set flash, redirect to get/edit), xml, json}
delete {html(set flash, redirect to index), xml, json}
 */

class Api_ObservationsController extends Application_Controller_RestController
{   
    public function optionsAction() {
        $this->view->message = 'Resource Options';
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function indexAction() {
        $this->view->resources = array();
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function headAction() {
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function getAction() {
        $this->view->id = $this->_getParam('id');
        $this->view->resource = new stdClass;
        $this->view->resource->name = "hello";
        
        $format = $this->getRequest()->getParam('format');
        $json = Zend_Json_Encoder::encode(array('hello' => 'world', 'format' => $format));
        $this->getResponse()->setBody($json);
        $this->getResponse()->setHttpResponseCode(200);
    }

    public function postAction() {
        $this->view->message = 'Resource Created';
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function putAction() {
        $this->view->message = sprintf('Resource #%s Updated', $this->_getParam('id'));
        $this->getResponse()->setHttpResponseCode(201);
    }

    public function deleteAction() {
        $this->view->message = sprintf('Resource #%s Deleted', $this->_getParam('id'));
        $this->getResponse()->setHttpResponseCode(200);
    }
}
