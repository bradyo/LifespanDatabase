<?php

class GenesController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }

    public function showAction()
    {
        $id = $this->_getParam('id', 1);

        // fetch gene information
        $gene = GeneTable::getInstance()->findOneBy('ncbiGeneId', $id);
        if (!$gene) {
            throw new Zend_Controller_Action_Exception('Page not found.', 404);
        }
        $this->view->gene = $gene;

        // fetch synonyms
        $this->view->synonyms = array();
        $geneSynonyms = GeneSynonymTable::getInstance()->findBy('geneId', $gene->id);
        foreach ($geneSynonyms as $geneSynonym) {
            $this->view->synonyms[] = $geneSynonym->synonym;
        }

        // fetch gene links
        $this->view->geneLinks = array();
        $geneLinks = GeneLinkTable::getInstance()->findBy('geneId', $gene->id);
        foreach ($geneLinks as $geneLink) {
            $databaseId = $geneLink->databaseId;
            $identifier = $geneLink->identifier;
            $linkUrl = $this->_getGeneLinkUrl($databaseId, $identifier);
            $this->view->geneLinks[$databaseId] = $linkUrl;
        }
        $this->view->geneLinks['Google'] = 'http://www.google.com/search?q=gene+'.$gene->symbol;

        // fetch lifespan observations
        $q = Doctrine_Query::create()->from('Observation o')
            ->select('o.*, g.*, c.*, e.*')
            ->leftJoin('o.genes g')
            ->leftJoin('o.compounds c')
            ->leftJoin('o.environments e')
            ->leftJoin('o.genes g_search')
            ->where('g_search.ncbiGeneId = ?', $id);
        $observations = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
        $this->view->geneObservations = $observations;

        // store homologs with observations
        // TODO set model to fetch homologs only with obeervations
        $geneHomologs = GeneHomologTable::getInstance()->findWithObservations($gene->id);
        foreach ($geneHomologs as $geneHomolog) {
            $species = $this->_getShortSpecies($geneHomolog->species);
            if (!isset($relatedHomologs[$species])) {
                $relatedHomologs[$species] = array();
            }

            $ncbiGeneId = $geneHomolog->homologNcbiGeneId;
            $data = array(
                'symbol' => $geneHomolog->symbol,
                'ncbiGeneId' => $ncbiGeneId,
            );
            $relatedHomologs[$species][$ncbiGeneId] = $data;
        }
        $this->view->relatedHomologs = $relatedHomologs;

        // set up comments
        $identity = Zend_Auth::getInstance()->getIdentity();
        $canEdit = ($identity !== null);
        $canPublish = ($identity !== null && $identity->is_moderator == '1');
        $this->view->canEdit = $canEdit;

        if ($canEdit) {
            $commentForm = new Application_Form_CommentForm($canPublish);
            if ($this->getRequest()->isPost()) {
                if ($commentForm->isValid($this->getRequest()->getParams())) {
                    $comment = new Comment();
                    $comment->parentTable = 'gene';
                    $comment->parentId = $gene->id;
                    $comment->author = $identity->username;
                    $comment->createdAt = date('Y-m-d H:i:s', time());
                    $comment->body = trim($commentForm->getValue('body'));
                    $comment->status = ($canPublish) ? 'accepted' : 'pending';
                    $comment->save();

                    $this->_redirect('genes/show/id/'.$gene->ncbiGeneId);
                }
            }
            $this->view->commentForm = $commentForm;
        }

        $this->view->comments = Doctrine_Core::getTable('Comment')
            ->findGeneComments($gene->id);
    }

    private function _getShortSpecies($species)
    {
        if (preg_match('/^([a-z]{1})[a-z]*\s{1}(.+)$/i', $species, $matches)) {
            return $matches[1].'. '.$matches[2];
        }
    }

    public function getHomologTableAction()
    {
        $this->getHelper('layout')->disableLayout();

        $id = $this->_getParam('id', 1);
        $gene = GeneTable::getInstance()->findOneBy('ncbiGeneId', $id);
        if (!$gene) {
            throw new Zend_Controller_Action_Exception('Page not found.', 404);
        }

        $this->view->homologs = array();
        $geneHomologs = GeneHomologTable::getInstance()->findBy('geneId', $gene->id);
        foreach ($geneHomologs as $geneHomolog) {
            $this->view->homologs[] = $geneHomolog->toArray();
        }
    }


    public function getGoTableAction()
    {
        $this->getHelper('layout')->disableLayout();

        $id = $this->_getParam('id', 1);
        $gene = GeneTable::getInstance()->findOneBy('ncbiGeneId', $id);
        if (!$gene) {
            throw new Zend_Controller_Action_Exception('Page not found.', 404);
        }

        // fetch gene ontology terms and group by go id
        $geneGos = GeneGoTable::getInstance()->findBy('geneId', $gene->id);
        $sortedGeneGos = array();
        foreach ($geneGos as $geneGo) {
            $goData = $geneGo->toArray();
            $goId = $goData['goId'];
            if (isset($sortedGeneGos[$goId])) {
                $goData['evidence'] .= ', '. $sortedGeneGos[$goId]['evidence'];
            }
            $sortedGeneGos[$goId] = $goData;
        }
        $this->view->gos = $sortedGeneGos;
    }

    private function _getGeneLinkUrl($databaseId, $identifier)
    {
        switch ($databaseId) {
        case 'SGD':
            return 'http://www.yeastgenome.org/cgi-bin/locus.fpl?locus='.$identifier;

        case 'Ensembl':
            if (preg_match('/^ENSG\d+$/', $identifier)) {
                return 'http://www.ensembl.org/Homo_sapiens/Gene/Summary?g='.$identifier;
            }
            else if (preg_match('/^ENSMUSG\d+$/', $identifier)) {
                return 'http://www.ensembl.org/Mus_musculus/Gene/Summary?db=core;g='.$identifier;
            }
            else {
                return 'http://www.ebi.ac.uk/ebisearch/search.ebi?db=allebi&query='.$identifier;
            }

        case 'FLYBASE':
            return 'http://flybase.org/reports/'.$identifier .'.html';

        case 'HGNC':
            return 'http://www.genenames.org/data/hgnc_data.php?hgnc_id='.$identifier;

        case 'HPRD':
            return 'http://www.hprd.org/protein/'.$identifier;

        case 'MGI':
            return 'http://www.informatics.jax.org/searches/accession_report.cgi?id='.$identifier;

        case 'MIM':
            return 'http://www.ncbi.nlm.nih.gov/omim/'.$identifier;

        case 'RGD':
            return 'http://rgd.mcw.edu/objectSearch/qtlReport.jsp?rgd_id='.$identifier;

        case 'WormBase':
            return 'http://www.wormbase.org/db/gene/gene?name='.$identifier.';class=Gene';
        }
    }    
}
