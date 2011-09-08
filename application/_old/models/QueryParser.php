<?php

/**
 * Description of Query Builder
 *
 * @author brady
 */
class Application_Model_QueryParser
{
    private $_query;

    public function __construct($queryString = null)
    {
        $this->_query = Doctrine_Query::create()->from('Observation o')
            ->select('o.*, og.*, oc.*, oe.*, r.*')
            ->leftJoin('o.genes og')
            ->leftJoin('o.compounds oc')
            ->leftJoin('o.environments oe')
						->leftJoin('o.genes og_search')     // search joins since we are
            ->leftJoin('o.compounds oc_search')       // selecting interventions
            ->leftJoin('o.environments oe_search')
						->leftJoin('o.revisions r');

    		$this->_parseQueryString($queryString);
			
				//echo "<br /><br />\n";
				//print_r($this->_query->getSqlQuery());
    }
           

    public function getQuery()
    {
        return $this->_query;
    }

    private function _parseQueryString($queryString)
    {
				$hasWhere = false;

        // extract field specified terms and add to dql
        preg_match_all('/(\w+?):"(.*?)"/', $queryString, $queryMatches);
        foreach ($queryMatches[0] as $i => $match) {
            $fieldMatched = false;

            // strip out the match
            $fieldName = $queryMatches[1][$i];
            $values = preg_split('/\s*,\s*/', $queryMatches[2][$i]);

            switch ($fieldName) {
                case 'lifespan':
                    if (preg_match('/([<>])(.+)/', $values[0], $matches)) {
                        $op = $matches[1];
                        $lifespan = $matches[2];
                        $this->_query->andWhere('o.lifespan '.$op.' ?', $lifespan);
                        $queryString = trim(str_replace($match, '', $queryString));
                        $fieldMatched = true;
                    }
                    break;
                case 'lifespanBase':
                    if (preg_match('/([<>])(.+)/', $values[0], $matches)) {
                        $op = $matches[1];
                        $lifespan = $matches[2];
                        $this->_query->andWhere('o.lifespanBase '.$op.' ?', $lifespan);
                        $fieldMatched = true;
                    }
                    break;
                case 'lifespanChange':
                    if (preg_match('/([<>])(.+)/', $values[0], $matches)) {
                        $op = $matches[1];
                        $lifespanChange = $matches[2];
                        $this->_query->andWhere('o.lifespanChange '.$op.' ?', $lifespanChange);
                        $fieldMatched = true;
                    }
                    break;
                case 'species':
                    $species = $this->_getSpecies($values);
                    if (count($species) > 0) {
                        $this->_query->andWhereIn('o.species', $species);
                        $fieldMatched = true;
                    }
                    break;
                case 'strain':
                    $this->_query->andWhereIn('o.strain', $values);
                    $fieldMatched = true;
                    break;
                case 'lifespanEffect':
                    $this->_query->andWhereIn('o.lifespanEffect', $values);
                    $fieldMatched = true;
                    break;
                case 'cellType':
                    $this->_query->andWhereIn('o.cellType', $values);
                    $fieldMatched = true;
                    break;
                case 'matingType':
                    $this->_query->andWhereIn('o.matingType', $values);
                    $fieldMatched = true;
                    break;
                case 'citationPubmedId':
                    $this->_query->andWhereIn('o.citationPubmedId', $values);
                    $fieldMatched = true;
                    break;
                case 'geneSymbol':
                    $symbols = $this->_getGeneSymbols($values);
                    $this->_query->andWhereIn('og_search.symbol', $symbols);
                    $fieldMatched = true;
                    break;
                case 'geneAlleleType':
                    $this->_query->andWhereIn('og_search.alleleType', $values);
                    $fieldMatched = true;
                    break;
                case 'compoundName':
                    $names = $this->_getCompoundNames($values);
                    $this->_query->andWhereIn('oc_search.name', $names);
                    $fieldMatched = true;
                    break;
                case 'compoundNcbiCompoundId':
                    $this->_query->andWhereIn('oc.ncbiCompoundId', $values);
                    $fieldMatched = true;
                    break;
                case 'environmentType':
                    $this->_query->andWhereIn('oe_search.type', $values);
                    $fieldMatched = true;
                    break;
								case 'submittedBy':
                    $this->_query->andWhereIn('r.requestedBy', $values);
										$this->_query->andWhere('r.action = "create"');
                    $fieldMatched = true;
                    break;
            }

            if ($fieldMatched) {
                // remove from query string so it doesnt get processed more
                $queryString = trim(str_replace($match, '', $queryString));    
								$hasWhere = true;
            }
        }

        // extract terms and add to dql
        $terms = array();
        preg_match_all('/\"(.+?)\"/', $queryString, $matches);
        foreach ($matches[0] as $i => $match) {
            $terms[] = $matches[1][$i];
            $queryString = str_replace($match, '', $queryString);
        }
        $matches = preg_split('/\s+/', $queryString);
        foreach ($matches as $i => $match) {
            $terms[] = $matches[$i];
            $queryString = str_replace($match, '', $queryString);
        }

        if (count($terms) > 0) {
            foreach ($terms as $term) {
                $whereString = '
                    o.body REGEXP ?
                    OR o.citationAuthor REGEXP ?
                    OR o.citationTitle REGEXP ?
                    OR oe_search.body REGEXP ?
                    OR o.species LIKE ?
                    OR o.strain LIKE ?
                    OR og_search.symbol LIKE ?
                    OR oc_search.name LIKE ?
                    OR oe_search.type LIKE ?
                    ';
                $whereParams = array(
		                '[[:<:]]'.$term.'[[:>:]]',
                    '[[:<:]]'.$term.'[[:>:]]',
                    '[[:<:]]'.$term.'[[:>:]]',
                    '[[:<:]]'.$term.'[[:>:]]',
                    '%'.$term.'%',
                    '%'.$term.'%',
                    '%'.$term.'%',
                    '%'.$term.'%',
                    '%'.$term.'%',   
                );

                 // add fetched species  to where clause
                $species = $this->_getSpecies(array($term));
                foreach ($species as $name) {
                    if ($name != $term) {
                        $whereString .= ' OR o.species = ?';
                        $whereParams[] = $name;
                    }
                }

                // add fetched gene symbols to where clause
                $symbols = $this->_getGeneSymbols(array($term));
                foreach ($symbols as $symbol) {
                    $whereString .= ' OR og_search.symbol = ?';
                    $whereParams[] = $symbol;
                }
                
                // search compound name
                $compoundNames = $this->_getCompoundNames(array($term));
                foreach ($compoundNames as $name) {
                    $whereString .= ' OR oc_search.name = ?';
                    $whereParams[] = $name;
                }

                $this->_query->andWhere($whereString, $whereParams);
                $hasWhere = true;
            }
        }

        // if no matches, we should return no results rather than all results
        if (!$hasWhere) {
            $this->_query->andWhere("1 = 2");
        }
    }

    private function _getSpecies($terms)
    {
        $species = $terms;
        $query = Doctrine_Query::create()
            ->select('DISTINCT t.name')
            ->from('Taxonomy t')
            ->leftJoin('t.synonyms ts')
            ->whereIn('t.name', $terms)
            ->orWhereIn('t.commonName', $terms)
            ->orWhereIn('ts.synonym', $terms);
        $rows = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        foreach ($rows as $row) {
            $species[] = $row['name'];
        }
        return $species;
    }

    private function _getGeneSymbols($terms)
    {
        $symbols = $terms;
        $query = Doctrine_Query::create()
            ->select('DISTINCT g.symbol')
            ->from('Gene g')
            ->leftJoin('g.synonyms gs')
            ->leftJoin('g.links gl')
            ->whereIn('g.symbol', $terms)
            ->orWhereIn('g.locusTag', $terms)
            ->orWhereIn('gs.synonym', $terms)
            ->orWhereIn('gl.identifier', $terms);
        $rows = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
        foreach ($rows as $row) {
            $symbols[] = $row['symbol'];
        }
        return $symbols;
    }

    private function _getCompoundNames($terms)
    {
        $query = Doctrine_Query::create()
            ->select('DISTINCT c.name')
            ->from('Compound c')
            ->leftJoin('c.synonyms cs')
            ->whereIn('c.name', $terms)
            ->orWhereIn('cs.synonym', $terms);
        $rows = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $names = array();
        foreach ($rows as $row) {
            $names[] = $row['name'];
        }
        return $names;
    }

}
