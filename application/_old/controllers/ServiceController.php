<?php

class ServiceController extends Zend_Controller_Action
{
    public function getGeneSymbolAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();

        $ncbiGeneId = $this->_getParam('ncbiGeneId');
        $symbol = $this->_getGeneSymbol($ncbiGeneId);

        $data = array(
            'symbol' => $symbol,
            'targetId' => $this->_getParam('targetId')
        );
        $json = Zend_Json::encode($data);
        $this->getResponse()->setBody($json);
    }
    
    public function getSpeciesAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();

        $ncbiTaxonId = $this->_getParam('ncbiTaxonId');
        $species = $this->_getSpecies($ncbiTaxonId);

        $json = Zend_Json::encode($species);
        $this->getResponse()->setBody($json);
    }

    public function getCompoundNameAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();

        $ncbiCompoundId = $this->_getParam('ncbiCompoundId');
        $name = Application_Service_NcbiService::getCompoundName($ncbiCompoundId);
        $data = array(
            'name' => $name,
            'targetId' => $this->_getParam('targetId')
        );
        $json = Zend_Json::encode($data);
        $this->getResponse()->setBody($json);
    }


    private function _getGeneSymbol($ncbiGeneId)
    {
        $db = Zend_Registry::get('ncbiDb');
        $stmt = $db->query('SELECT symbol FROM gene WHERE gene_id = ?', $ncbiGeneId);
        $result = $stmt->fetch();
        return $result['symbol'];
    }

    private function _getSpecies($ncbiTaxonId)
    {
        $db = Zend_Registry::get('ncbiDb');
        $stmt = $db->query('SELECT name FROM taxon WHERE taxon_id = ?', $ncbiTaxonId);
        $result = $stmt->fetch();
        return $result['name'];
    }

    public function getCitationDataAction()
    {
        $this->getHelper('layout')->disableLayout();
        $this->getHelper('ViewRenderer')->setNoRender();

        $pubmedId = $this->_getParam('pubmedId');
        $data = $this->_getCitationData($pubmedId);

        $json = Zend_Json::encode($data);
        $this->getResponse()->setBody($json);
    }


    private function _getCitationData($pubmedId)
    {
        // check local database first
        $q = Doctrine_Query::create()
            ->from('Citation c')
            ->where('c.pubmed_id = ?', $pubmedId);
        $citation = $q->fetchOne();
        if ($citation) {
            return array(
                'author' => $citation['author'],
                'title' => $citation['title'],
                'year' => $citation['year'],
                'source' => $citation['source']
            );
        }

        // see http://www.ncbi.nlm.nih.gov/entrez/query/DTD/pubmed_080101.dtd
        // for xml DTD definition
        $url = sprintf('http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?'
            . 'db=pubmed&report=citation&mode=xml&id=%d', intval($pubmedId));

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        $data = array();
        $xml = new SimpleXMLElement($response);
        if ($xml && $xml->PubmedArticle && $xml->PubmedArticle->MedlineCitation) {
            $article = $xml->PubmedArticle->MedlineCitation->Article;
        }

        if (isset($article)) {
            $title = (string)$article->ArticleTitle;
            $pages = (string)$article->Pagination->MedlinePgn;
            $journal = $article->Journal;
            if ($journal) {
                $name = (string)$journal->ISOAbbreviation;
                $year = intval((string)$journal->JournalIssue->PubDate->Year);
                $vol = (string)$journal->JournalIssue->Volume;
                $source = $name . " " . $vol . ": " . $pages;
            }

            $authorsArr = array();
            if ($article->AuthorList) {
                foreach ($article->AuthorList->xpath('//Author') as $author) {
                    $authorsArr[] = $author->LastName . " " . $author->Initials;
                }
            }
            $authors = join(", ", $authorsArr);
            $data = array(
                'author' => $authors,
                'title' => $title,
                'source' => $source,
                'year' => $year
            );

            // trim and strip tailing period
            foreach ($data as $key => &$value) {
                $data[$key] = trim($value);
                if (preg_match('/(.+)\./', $value, $matches)) {
                    $data[$key] = $matches[1];
                }
            }

            return $data;
        }
    }
}
