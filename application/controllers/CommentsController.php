<?php

class CommentsController extends Zend_Controller_Action
{
    public function pendingAction()
    {
        $comments = Doctrine_Core::getTable('Comment')->findPending();
        $this->view->comments = $comments;
    }

}

