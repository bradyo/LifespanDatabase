<?php

class ManageController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_forward('observations');
    }

    public function observationsAction()
    {
        $this->view->tabs = $this->_getTabs();

        $revisions = Doctrine_Core::getTable('ObservationRevision')->findPending();
        $this->view->revisions = $revisions;

        if ($this->getRequest()->getParam('format', null) == 'rss') {
            // build feed
            $baseUrl = 'http://' . $_SERVER['SERVER_NAME'];
            $feedData = array(
                'title' => 'Pending Observation Revisions',
                'link' => $baseUrl . $_SERVER['REQUEST_URI'],
                'charset' => 'utf-8',
                'language' => 'en-us',
                'published' => time(),
            );

            $feedEntries = array();
            foreach ($revisions as $revision) {
                // build content body
                $content = $revision->action . ' observation ';
                if ($revision->observationId !== null) {
                    $content .= $revision->observationId;
                }
                $content .= ' requested by ' . $revision->requestedBy;

                // build timestamp
                list($date, $time) = explode(' ', $revision->requestedAt);
                list($year, $month, $day) = explode('-', $date);
                list($hour, $minute, $second) = explode(':', $time);
                $timestamp = mktime($hour, $minute, $second, $month, $day, $year);

                // build link
                $link = $baseUrl . '/observations/revision/id/' . $revision->id;

                // add entry to feed
                $feedEntries[] = array(
                    'title' => 'observation revision ' . $revision->id,
                    'link' => $link,
                    'description' => $content,
                    'content' => $content,
                    'lastUpdate' => $timestamp
                );
            }
            $feedData['entries'] = $feedEntries;

            $this->_helper->layout->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender(true);

            $feed = Zend_Feed::importArray($feedData, 'rss');
            $feed->send();
        }
    }

    public function commentsAction()
    {
        $this->view->tabs = $this->_getTabs();

        $comments = Doctrine_Core::getTable('Comment')->findPending();
        $this->view->comments = $comments;
    }

    public function conflictsAction()
    {
        $this->view->tabs = $this->_getTabs();
    }

    public function warningsAction()
    {
        $this->view->tabs = $this->_getTabs();
    }

    
    private function _getTabs()
    {
        $tabs = array();
        $requestAction = $this->getRequest()->getActionName();

        $actions = array(
            'observations' => 'Observation Revisions',
            'conflicts' => 'Observation Conflicts',
            'warnings' => 'Warnings',
            'comments' => 'Comments',
        );
        foreach ($actions as $action => $label) {
            $isSelected = ($action == $requestAction) ? true : false;
            $count = $this->_getCount($action);
            $tabs[] = array(
                'label' => $label . ' (' . $count . ')',
                'action' => $action,
                'isSelected' => $isSelected,
            );
        }

        return $tabs;
    }

    private function _getCount($action)
    {
        $count = 0;
        switch ($action) {
            case 'observations':
                $revisionTable = Doctrine_Core::getTable('ObservationRevision');
                $count = $revisionTable->getPendingCount();
                break;
            case 'conflicts':
                // TODO
                break;
            case 'comments':
                $commentTable = Doctrine_Core::getTable('Comment');
                $count = $commentTable->getPendingCount();
                break;
            case 'warnings':
                // TODO
                break;
        }
        return $count;
    }

}

