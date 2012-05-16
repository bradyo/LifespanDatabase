<?php

class ObservationsController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $browser = new Application_Model_ObservationBrowser(
            $this->getRequest()->getParam('q', ''),
            $this->getRequest()->getParam('page', 1)
        );
        $this->view->form = $browser->getForm();
        $this->view->rows = $browser->getRows();
        $this->view->paginator = $browser->getPaginator();
    }

    public function showAction()
    {
        $id = $this->_getParam('id', 1);

        // get observation
        $observation = Doctrine_Core::getTable('Observation')->getObservation($id);
        if ($observation == null) {
            throw new Zend_Controller_Action_Exception('Page does not exist', 404);
        }

        $this->view->observation = $observation;

        $body = $this->view->observation['body'];
        $body = preg_replace('/\[pubmed:(\d+)\]/',
                '<a class="grayed-anchor" href="http://www.ncbi.nlm.nih.gov/pubmed/$1">[pubmed]</a>',
                $body);
        $this->view->observation->body = $body;


        $identity = Zend_Auth::getInstance()->getIdentity();
        $canEdit = ($identity !== null);
        $canPublish = ($identity !== null && $identity->is_moderator == '1');
        $this->view->canEdit = $canEdit;

        // fetch comment stuff
        if ($canEdit) {
            $commentForm = new Application_Form_CommentForm($canPublish);
            if ($this->getRequest()->isPost()) {
                if ($commentForm->isValid($this->getRequest()->getParams())) {
                    $comment = new Comment();
                    $comment->parentTable = 'observation';
                    $comment->parentId = $observation['id'];
                    $comment->author = $identity->username;
                    $comment->createdAt = date('Y-m-d H:i:s', time());
                    $comment->body = trim($commentForm->getValue('body'));
                    $comment->status = ($canPublish) ? 'accepted' : 'pending';
                    $comment->save();

                    $this->_redirect('observations/show/id/'.$id);
                }
            }
            $this->view->commentForm = $commentForm;
        }
        $this->view->comments = Doctrine_Core::getTable('Comment')
            ->findObservationComments($observation->id);

        // fetch related observations
        $this->view->relatedObservations = array();
        $dbh = Zend_Registry::get('db');
        $stmt = $dbh->prepare('
            SELECT citation_pubmed_id as pubmed_id, COUNT(id) AS count FROM observation
            WHERE citation_pubmed_id = ?
            GROUP BY citation_pubmed_id
            '
            );
        $stmt->execute(array($observation->citationPubmedId));
        if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== null) {
            if ($row['count'] > 1) {
                $pubmedId = $row['pubmed_id'];
                $this->view->relatedObservations[] = array(
                    'label' => 'PubMed ID: '.$pubmedId,
                    'page' => 'search',
                    'params' => 't=observation&q='.urlencode('citationPubmedId:"'.$pubmedId.'"'),
                    'count' => $row['count'],
                );
            }
        }

        $stmt = $dbh->prepare('
            SELECT og.ncbi_gene_id, COUNT(og.id) AS count FROM observation_gene og
            WHERE og.ncbi_gene_id IN (
                SELECT DISTINCT ncbi_gene_id FROM observation_gene
                WHERE observation_id = ?
            )
            GROUP BY og.ncbi_gene_id
            ');
        $stmt->execute(array($observation->id));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['count'] > 1) {
                $ncbiGeneId = $row['ncbi_gene_id'];
                $this->view->relatedObservations[] = array(
                    'label' => 'NCBI Gene ID: '.$ncbiGeneId,
                    'page' => 'genes/show',
                    'params' => 'id='.$ncbiGeneId,
                    'count' => $row['count'],
                );
            }
        }

        // get pending revisions count
        $stmt = $dbh->prepare('
            SELECT COUNT(r.id) as count FROM observation_revision r
            WHERE r.status = "pending" AND r.observation_id = ?
            GROUP BY r.observation_id
            ');
        $stmt->execute(array($observation->id));
        $count = 0;
        if (($row = $stmt->fetch(PDO::FETCH_ASSOC)) != false) {
            $count = $row['count'];
        }
        $this->view->pendingRevisionsCount = $count;

    }

    public function exportAction()
    {
        // grab the format parameter
        $format = $this->getRequest()->get('format', null);
        if (!in_array($format, array('csv', 'xml', 'yml'))) {
            throw new Zend_Controller_Action_Exception('Page does not exist', 404);
        }

        // get observation
        $id = $this->_getParam('id', 1);
        $observation = Doctrine_Core::getTable('Observation')->getObservation($id);
        if ($observation == null) {
            throw new Zend_Controller_Action_Exception('Page does not exist', 404);
        }

        // build data array
        $row = $observation->toArray();
        $row['comments'] = Doctrine_Core::getTable('Comment')
            ->findObservationComments($observation->id, 'accepted');
        $this->view->rows = array($row);

        $this->_helper->layout->disableLayout();
        $this->renderScript('observations/export/'.$format.'.phtml');
    }

    public function newAction()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            throw new Zend_Controller_Action_Exception('This page dont exist', 404);
        }

        // See if user can publish (is moderator). Form adapts to show publish
        // form elements.
        $canPublish = false;
        $identity = Zend_Auth::getInstance()->getIdentity();
        $canPublish = ($identity->is_moderator == '1') ? true : false;

        $form = new Application_Form_Observation($canPublish);
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {
                $values = $this->getFilteredFormValues($form->getValues());

                // create a new revision entry
                $revision = new ObservationRevision();
                $revision->action = 'create';
                $revision->observationData = json_encode($values);
                $revision->requestedBy = $identity->username;
                $revision->requestedAt = date('Y-m-d H:i:s');
                $revision->status = 'pending';
                $revision->save();

                if ($canPublish) {
                    $reviewedBy = $identity->username;
                    $reviewerComment = $values['reviewerComment'];
                    $revision->accept($reviewedBy, $reviewerComment);
                } 
                
                // TODO: set a flash message telling save was successful

                // redirect to revision revision or observation
                if ($revision->observationId !== null) {
                    $this->_redirect('observations/show/id/'.$revision->observationId);
                } else {
                    $this->_redirect('observations/revision/id/'.$revision->id);
                }
            }
        }
        $this->view->form = $form;
    }

    public function editAction()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            throw new Zend_Controller_Action_Exception('This page dont exist', 404);
        }

        $identity = Zend_Auth::getInstance()->getIdentity();
        $canPublish = ($identity->is_moderator == '1') ? true : false;

        $form = new Application_Form_Observation($canPublish);
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {
                $values = $this->getFilteredFormValues($form->getValues());

                // create a new revision on this observation
                $revision = new ObservationRevision();
                if ($values['status'] == 'deleted') {
                    $revision->action = 'delete';
                } else {
                    $revision->action = 'edit';
                }
                $revision->observationId = $values['id'];
                $revision->observationData = json_encode($values);
                $revision->requestedBy = $identity->username;
                $revision->requestedAt = date('Y-m-d H:i:s');
                $revision->status = 'pending';
                $revision->save();

                if ($canPublish) {
                    $reviewedBy = $identity->username;
                    $reviewerComment = $values['reviewerComment'];
                    $revision->accept($reviewedBy, $reviewerComment);
                }

                // TODO: set flash message

                $this->_redirect('/observations/show/id/'.$revision->observationId);
            }
        } else {
            $id = $this->_getParam('id');
            $q = Doctrine_Query::create()->from('Observation o')
                ->leftJoin('o.genes g')
                ->leftJoin('o.compounds c')
                ->leftJoin('o.environments e')
                ->leftJoin('o.taxonomy t')
                ->where('o.id = ?', $id);

            $observation = $q->fetchOne();
            if (!$observation) {
                throw new Zend_Controller_Action_Exception('Page does not exist', 404);
            }
            $form->setValues($observation->toArray());
        }
        $this->view->form = $form;
    }

    private function getFilteredFormValues($values)
    {
        $values = $this->getFilteredParams($values);

        // remove deleted or empty interventions
        $cleanGenes = array();
        foreach ($values['genes'] as $geneData) {
            if ($geneData['isDeleted'] || !isset($geneData['symbol'])) {
                continue;
            }
            $cleanGenes[] = $geneData;
        }
        $values['genes'] = $cleanGenes;

        // remove deleted or empty interventions
        $cleanCompounds = array();
        foreach ($values['compounds'] as $compoundData) {
            if ($compoundData['isDeleted'] || !isset($compoundData['name'])) {
                continue;
            }
            $cleanCompounds[] = $compoundData;
        }
        $values['compounds'] = $cleanCompounds;

        // remove deleted or empty interventions
        $cleanEnvironments = array();
        foreach ($values['environments'] as $envData) {
            if ($envData['isDeleted'] || !isset($envData['type'])) {
                continue;
            }
            $cleanEnvironments[] = $envData;
        }
        $values['environments'] = $cleanEnvironments;

        return $values;
    }

    private function getFilteredParams($values)
    {
        // remove empty elements
        $filteredParams = $values;
        foreach ($filteredParams as $key => &$value) {
            if (is_array($value)) {
                $value = $this->getFilteredParams($value);
            } else if ($value === '') {
                $filteredParams[$key] = null;
            }
        }
        return $filteredParams;
    }



    /**
     * Updates observations using batch data in a yaml format.
     */
    public function uploadAction()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            throw new Zend_Controller_Action_Exception('Page does not exist', 404);
        }
        $identity = Zend_Auth::getInstance()->getIdentity();
        $username = $identity->username;
        $isModerator = $identity->is_moderator;

        $this->view->addedCount = 0;
        $this->view->heldItems = array();
        $this->view->heldYaml = null;
        $this->view->inputYaml = null;

        if ($this->getRequest()->isPost()) {
            if (!$this->getRequest()->getParam('resubmit')) {
                $updater = new Application_Model_ObservationUpdater($username, $isModerator);
                $yaml = $this->getRequest()->getParam('yamlInput');
                $this->view->addedCount = $updater->processYaml($yaml);
                $this->view->heldItems = $updater->getHeldItems();
            }
            else {
                // resumbitting
                $yamlInputs = $this->getRequest()->getParam('yamlInputs');
                foreach ($yamlInputs as $yaml) {
                    if (empty($yaml)) {
                        continue;
                    }

                    $updater = new Application_Model_ObservationUpdater($username,
                            $isModerator, true);
                    $addedCount = $updater->processYaml($yaml);
                    $heldItems = $updater->getHeldItems();

                    $this->view->addedCount += $addedCount;
                    foreach ($heldItems as $heldItem) {
                        $this->view->heldItems[] = $heldItem;
                    }
                }
            }

            // build combined held yaml
            $heldYaml = "observations:\n";
            foreach ($this->view->heldItems as $heldItem) {
                $heldYaml .= preg_replace ('/^observations:\n/', '', $heldItem['yaml']);
                $heldYaml .= "\n";
            }
            $this->view->heldYaml = $heldYaml;
        }
    }

    public function revisionsAction()
    {
        $id = $this->getRequest()->getParam('id');
        $table = Doctrine_Core::getTable('ObservationRevision');

        $this->view->observationId = $id;
        $this->view->revisions = $table->findByObservationId($id);
    }

    public function revisionAction()
    {
        $id = $this->getRequest()->getParam('id');
        $revision = Doctrine_Core::getTable('ObservationRevision')->findOneById($id);
        if ($revision == null) {
            throw new Zend_Controller_Action_Exception('Page does not exist', 404);
        }

        $this->view->observation = Doctrine_Core::getTable('Observation')
            ->getObservation($revision->observationId);
        $this->view->revision = $revision;

        $identity = Zend_Auth::getInstance()->getIdentity();
        $username = $identity->username;
        $isModerator = $identity->is_moderator;

        if ($revision->status == 'pending' && $isModerator) {
            $form = new Application_Form_ObservationRevision();
            if ($this->getRequest()->isPost()) {
                if ($form->isValid($this->getRequest()->getParams())) {
                    $values = $form->getValues();

                    $reviewedBy = $username;
                    $comment = $values['comment'];
                    if ($values['status'] == 'accepted') {
                        $revision->accept($reviewedBy, $comment);
                    }
                    elseif ($values['status'] == 'rejected') {
                        $revision->reject($reviewedBy, $comment);
                    }
                }
            }
            $this->view->form = $form;
        }
    }

    public function pendingAction()
    {
        $revisions = Doctrine_Core::getTable('ObservationRevision')->findPending();
        $this->view->revisions = $revisions;
    }

}

