<?php

/**
 * Description of ObservationBrowser
 *
 * @author brady
 */
class Application_Model_ObservationBrowser
{
    protected $_query;
    protected $_params;
    protected $_filterForm;
    protected $_exportForm;
    protected $_queryString;
    protected $_page;
    protected $_itemsPerPage;

    public function __construct($queryString = '', $page = 1, $itemsPerPage = 100)
    {
        $this->_queryString = trim($queryString);
        $this->_page = $page;
        $this->_itemsPerPage = $itemsPerPage;
    }


    public function getQuery() {
        if ($this->_query == null) {
            $queryParser = new Application_Model_QueryParser($this->_queryString);
        		$this->_query = $queryParser->getQuery();
        }

				//echo "<br /><br />\n";
				//print_r($this->_query->getSqlQuery());
				//die();

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
        if (($subFilters = $this->_getFilters('lifespanEffect')) !== null) {
            $filters['Lifespan Effect'] = $subFilters;
        }

        if (($subFilters = $this->_getFilters('species')) !== null) {
            foreach ($subFilters as &$filter) {
                $filter['label'] = $this->_getShortSpecies($filter['label']);
            }
            $filters['Species'] = $subFilters;
        } else {
            if (($subFilters = $this->_getFilters('strain')) !== null) {
                $filters['Strain'] = $subFilters;
            }
            if (($subFilters = $this->_getFilters('cellType')) !== null) {
                $filters['Cell Type'] = $subFilters;
            }
            if (($subFilters = $this->_getFilters('matingType')) !== null) {
                $filters['Mating Type'] = $subFilters;
            }
        }
        return $filters;
    }

    private function _getFilters($field)
    {
        if (!preg_match('/'.$field.':/', $this->_queryString)) {
            // query count
            $query = $this->getQuery()->copy();

            $query->select('o.'.$field.', COUNT(DISTINCT o.id) as count');
            $query->andWhere('o.'.$field.' IS NOT NULL');
            $query->groupBy('o.'.$field);

            $subFilters = array();
            $results = $query->execute();

						//echo "<br /><br />\n";
						//print_r($query->getSqlQuery());

            foreach ($results as $row) {
                $subFilters[] = array(
                    'label' => $row[$field],
                    'q' => urlencode($this->_queryString.' '.$field.':"'.$row[$field].'"'),
                    'count' => $row['count'],
                );
            }
            if (count($subFilters) > 0) {
                return $subFilters;
            }
        }
    }

    public function getExistingFilters()
    {
        $existingFilters = array();

        if (($filter = $this->_getExistingFilter('lifespanEffect')) !== null) {
            $existingFilters['Lifespan Effect'] = $filter;
        }
        if (($filter = $this->_getExistingFilter('species')) !== null) {
            $filter['label'] = $this->_getShortSpecies($filter['label']);
            $existingFilters['Species'] = $filter;
        }
        if (($filter = $this->_getExistingFilter('strain')) !== null) {
            $existingFilters['Strain'] = $filter;
        }
        if (($filter = $this->_getExistingFilter('cellType')) !== null) {
            $existingFilters['Cell Type'] = $filter;
        }
        if (($filter = $this->_getExistingFilter('matingType')) !== null) {
            $existingFilters['Mating Type'] = $filter;
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
