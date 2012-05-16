<?php

/**
 * Description of GeneBrowser
 *
 * @author brady
 */
class Application_Model_GeneBrowser
{
    protected $_query;
    protected $_params;
    protected $_filterForm;
    protected $_exportForm;
    protected $_queryString;
    protected $_page;
    protected $_itemsPerPage;

    public function __construct($queryString = '', $page = 1, $itemsPerPage = 25)
    {
        $this->_queryString = trim($queryString);
        $this->_page = $page;
        $this->_itemsPerPage = $itemsPerPage;
    }

    private function _getQuery()
    {
        $queryParser = new Application_Model_QueryParserGene($this->_queryString);
        $query = $queryParser->getQuery();
        return $query;
    }

    public function getQuery() {
        if ($this->_query == null) {
            $this->_query = $this->_getQuery();
        }
        return $this->_query;
    }

    public function getPager()
    {
        $pager = new Doctrine_Pager($this->getQuery(),
            $this->_page, $this->_itemsPerPage);
        return $pager;
    }

    public function getFilters()
    {
        $filters = array();
        if (!preg_match('/species:/', $this->_queryString)) {
            // query count
            $query = $this->_getQuery()->copy();
            $query->select('g.species, COUNT(DISTINCT g.id) as count');
            $query->andWhere('o.species IS NOT NULL');
            $query->groupBy('o.species');

            $subFilters = array();
            $results = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
            foreach ($results as $row) {
                $subFilters[] = array(
                    'label' => $this->_getShortSpecies($row['species']),
                    'q' => urlencode($this->_queryString.' species:"'.$row['species'].'"'),
                    'count' => $row['count'],
                );
            }
            if (count($subFilters) > 0) {
                $filters['Species'] = $subFilters;
            }
        }
        return $filters;
    }

    public function getExistingFilters()
    {
        $existingFilters = array();
        if (($filter = $this->_getExistingFilter('species')) !== null) {
            $filter['label'] = $this->_getShortSpecies($filter['label']);
            $existingFilters['Species'] = $filter;

        }
        return $existingFilters;
    }

    private function _getExistingFilter($field)
    {
        if (preg_match('/'.$field.':"(.+?)"/', $this->_queryString, $matches)) {
            $label = $matches[1];
            $q = $this->_queryString;
            $q = preg_replace('/'.$field.':".+?"/', '', $q);

            $filter = array('label' => $label, 'q' => urlencode($q));
            return $filter;
        }
    }

    private function _getShortSpecies($species)
    {
        if (preg_match('/^([a-z]{1})[a-z]*\s{1}(.+)$/i', $species, $matches)) {
            return $matches[1].'. '.$matches[2];
        }
    }
}
