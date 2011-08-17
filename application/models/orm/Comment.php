<?php

/**
 * Comment
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Comment extends BaseComment
{
    public function postSave($event)
    {
        parent::postSave($event);

        // update links
        // TODO 

        // update search table
        $dbh = Doctrine_Manager::getInstance()->getConnection('agingdb')->getDbh();
        $stmt = $dbh->prepare("INSERT INTO comment_search (id, body) VALUE (?, ?)");
        $stmt->execute(array($this->id, $this->body));
    }

    public function preDelete($event)
    {
        parent::preDelete($event);

        // update search table
        $dbh = Doctrine_Manager::getInstance()->getConnection('agingdb')->getDbh();
        $stmt = $dbh->prepare("DELETE FROM comment_search WHERE id = ?");
        $stmt->execute(array($this->id));
    }

}