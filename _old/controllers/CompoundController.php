<?php

class CompoundController extends Zend_Controller_Action
{
    public function showAction()
    {
        $id = $this->_getParam('id', 1);

        // fetch gene information
        $compound = CompoundTable::getInstance()->findOneBy('ncbiCompoundId', $id);
        if (!$compound) {
            throw new Zend_Controller_Action_Exception('Page not found.', 404);
        }
        $this->view->compound = $compound;

        // fetch synonyms
        $this->view->synonyms = array();
        $synonyms = CompoundSynonymTable::getInstance()->findBy('compoundId', $compound->id);
        foreach ($synonyms as $synonym) {
            $this->view->synonyms[] = $synonym->synonym;
        }

        // fetch gene links
        $this->view->links = array();
        $links = array();
        //$links = CompoundLinkTable::getInstance()->findBy('compoundId', $compound->id);
        foreach ($links as $link) {
            $databaseId = $link->databaseId;
            $identifier = $link->identifier;
            $linkUrl = $this->_getLinkUrl($databaseId, $identifier);
            $this->view->links[$databaseId] = $linkUrl;
        }
        $this->view->links['Google'] = 'http://www.google.com/search?q=compound+'.$compound->name;

        // fetch lifespan observations
        $q = Doctrine_Query::create()->from('Observation o')
            ->select('o.*, g.*, c.*, e.*')
            ->leftJoin('o.genes g')
            ->leftJoin('o.compounds c')
            ->leftJoin('o.environments e')
            ->leftJoin('o.compounds c_search')
            ->where('c_search.ncbiCompoundId = ?', $id);
        $observations = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
        $this->view->observations = $observations;
    }
    
}