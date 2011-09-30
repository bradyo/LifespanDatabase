<?php

class SearchController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $request = $this->getRequest();
        $queryString = urldecode($request->get('q', ''));
        $page = $request->get('p', '');
        $format = $request->get('format', null);

        // save the search in session
        if (!empty($queryString)) {
            $searchNamespace = new Zend_Session_Namespace('search');
            if (!isset($searchNamespace->searches)) {
                $searchNamespace->searches = array();
            }
            array_unshift($searchNamespace->searches, $queryString);
            if (count($searchNamespace->searches) > 10) {
                $discard = array_pop($searchNamespace->searches);
            }
        }
        $browser = new Application_Model_ObservationBrowser($queryString, $page);

        // redirect view script based on format
        if (in_array($format, array('csv', 'xml', 'yml'))) {
            $query = $browser->getQuery();
            $rows = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
            $this->view->rows = $rows;

            $this->_helper->layout->disableLayout();
            $this->renderScript('export/'.$format.'.phtml');
            return;
        }

        // pass data into view
        $this->view->type = $viewType;
        $this->view->queryString = $queryString;
        $this->view->filters = $browser->getFilters();
        $this->view->existingFilters = $browser->getExistingFilters();
        $this->view->page = $page;
        $this->view->pager = $browser->getPager();
        $this->view->rows = $this->view->pager->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
       
        // pass params to layout
        $this->_helper->layout()->q = trim($queryString);
    }


    public function advancedAction()
    {
        $form = new Application_Form_AdvancedSearch();

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getParams())) {
            // build search string
            $queryString = '';
            
            $values = $form->getValues();
            if (!empty($values['search'])) {
                $queryString = $values['search'];
            }
            if (!empty($values['species'])) {
                $queryString .= ' species:"'.$values['species'].'"';
            }
            if (!empty($values['strain'])) {
                $queryString .= ' strain:"'.$values['strain'].'"';
            }
            if (!empty($values['cellType'])) {
                $queryString .= ' cellType:"'.$values['cellType'].'"';
            }
            if (!empty($values['matingType'])) {
                $queryString .= ' matingType:"'.$values['matingType'].'"';
            }
            if (!empty($values['citationPubmedId'])) {
                $queryString .= ' citationPubmedId:"'.$values['citationPubmedId'].'"';
            }

            if (!empty($values['lifespanEffect'])) {
                $queryString .= ' lifespanEffect:"'.$values['lifespanEffect'].'"';
            }
            if (!empty($values['lifespanMeasure'])) {
                $queryString .= ' lifespanMeasure:"'.$values['lifespanMeasure'].'"';
            }

            $lifespanMultiplier = null;
            if (!empty($values['lifespanUnit'])) {
                $lifespanUnit = $values['lifespanUnit'];
                if ($lifespanUnit == 'years') {
                    $lifespanMultiplier = 365;
                    $lifespanUnit = 'days';
                }
                $queryString .= ' lifespanUnit:"'.$lifespanUnit.'"';
            }
            if (!empty($values['lifespan'])) {
                $lifespan = doubleval($values['lifespan']);
                if ($lifespanMultiplier !== null) {
                    $lifespan = $lifespanMultiplier * $lifespan;
                }
                $lifespanOp = '>';
                if ($values['lifespanOp'] == '<') {
                    $lifespanOp = '<';
                }
                $queryString .= ' lifespan:"'.$lifespanOp.$lifespan.'"';
            }
            if (!empty($values['lifespanBase'])) {
                $lifespan = doubleval($values['lifespanBase']);
                if ($lifespanMultiplier !== null) {
                    $lifespan = $lifespanMultiplier * $lifespan;
                }
                $lifespanOp = '>';
                if ($values['lifespanBaseOp'] == '<') {
                    $lifespanOp = '<';
                }
                $queryString .= ' lifespanBase:"'.$lifespanOp.$lifespan.'"';
            }
            if (!empty($values['lifespanChange'])) {
                $lifespanChange = doubleval($values['lifespanChange']);
                $lifespanOp = '>';
                if ($values['lifespanChangeOp'] == '<') {
                    $lifespanOp = '<';
                }
                $queryString .= ' lifespanChange:"'.$lifespanOp.$lifespanChange.'"';
            }

            if (!empty($values['geneSymbol'])) {
                $queryString .= ' geneSymbol:"'.$values['geneSymbol'].'"';
            }
            if (!empty($values['geneAllele'])) {
                $queryString .= ' geneAllele:"'.$values['geneAllele'].'"';
            }
            if (!empty($values['geneAlleleType'])) {
                $queryString .= ' geneAlleleType:"'.$values['geneAlleleType'].'"';
            }


            if (!empty($values['compoundName'])) {
                $queryString .= ' compoundName:"'.$values['compoundName'].'"';
            }

            if (!empty($values['environmentType'])) {
                $queryString .= ' environmentType:"'.$values['environmentType'].'"';
            }
            if (!empty($values['environmentDescription'])) {
                $queryString .= ' environmentDescription:"'.$values['environmentDescription'].'"';
            }
            
            $this->_redirect('search?q='.urlencode($queryString));
        }

        if ($this->getRequest()->getParam('q')) {
            // parse query string for form values
            $queryString = $this->getRequest()->getParam('q');
            $formValues = $this->_getFormValues($queryString);
            $form->populate($formValues);
        }

        $this->view->form = $form;

        $searchNamespace = new Zend_Session_Namespace('search');
        $this->view->savedSearches = $searchNamespace->searches;
    }

    private function _getFormValues($queryString)
    {
        $formValues = array();

        // extract field specified terms and add to dql
        preg_match_all('/(\w+?):"(.*?)"/', $queryString, $fieldMatches);
        foreach ($fieldMatches[0] as $i => $match) {
            // strip out the match
            $queryString = str_replace($match, '', $queryString);
            $fieldName = $fieldMatches[1][$i];
            $values = preg_split('/\s*,\s*/', $fieldMatches[2][$i]);

            switch ($fieldName) {
                case 'species':
                    $formValues['species'] = join(',', $values);
                    break;
                case 'strain':
                    $formValues['strain'] = join(',', $values);
                    break;
                case 'lifespanEffect':
                    $formValues['lifespanEffect'] = $values[0];
                    break;
                case 'cellType':
                    $formValues['cellType'] = join(',', $values);
                    break;
                case 'matingType':
                    $formValues['matingType'] = join(',', $values);
                    break;
                case 'temperature':
                    $formValues['temperature'] = join(',', $values);
                    break;
                case 'citationPubmedId':
                    $formValues['citationPubmedId'] = join(',', $values);
                    break;
                case 'lifespanMeasure':
                    $formValues['lifespanMeasure'] = join(',', $values);
                    break;
                case 'lifespanUnit':
                    $formValues['lifespanUnit'] = join(',', $values);
                    break;
                case 'lifespanChange':
                    if (preg_match('/([<>])(.+)/', $values[0], $matches)) {
                        $formValues['lifespanChangeOp'] = $matches[1];
                        $formValues['lifespanChange'] = $matches[2];
                    }
                    break;
                case 'lifespan':
                    if (preg_match('/([<>])(.+)/', $values[0], $matches)) {
                        $formValues['lifespanOp'] = $matches[1];
                        $formValues['lifespan'] = $matches[2];
                    }
                    break;
                case 'lifespanBase':
                    if (preg_match('/([<>])(.+)/', $values[0], $matches)) {
                        $formValues['lifespanBaseOp'] = $matches[1];
                        $formValues['lifespanBase'] = $matches[2];
                    }
                    break;
                case 'geneSymbol':
                    $formValues['geneSymbol'] = join(',', $values);
                    break;
                case 'geneAllele':
                    $formValues['geneAllele'] = join(',', $values);
                    break;
                case 'geneAlleleType':
                    $formValues['geneAlleleType'] = join(',', $values);
                    break;

                case 'compoundName':
                    $formValues['compoundName'] = join(',', $values);
                    break;
                case 'environmentType':
                    $formValues['environmentType'] = join(',', $values);
                    break;
                case 'environmentDescription':
                    $formValues['environmentDescription'] = join(',', $values);
                    break;
            }
        }
        $formValues['search'] = trim($queryString);

        return $formValues;
    }

    public function clearAction()
    {
        $searchNamespace = new Zend_Session_Namespace('search');
        unset($searchNamespace->searches);
        $this->_redirect('search/advanced');
    }
}

