<?php

/**
 * Description of Query Builder
 *
 * @author brady
 */
class Application_Model_QueryParserGene
{
    private $_query;

    public function __construct($queryString = null)
    {
        $this->_query = Doctrine_Query::create()->from('Gene g')
            ->select('g.*, og.*, COUNT(o.id) as count')
            ->leftJoin('g.ObservationGene og')
            ->leftJoin('og.Observation o')
            ->groupBy('g.id');
        
        $this->_parseQueryString($queryString);
    }
           

    public function getQuery()
    {
        return $this->_query;
    }

    private function _parseQueryString($queryString)
    {
        // extract field specified terms and add to dql
        preg_match_all('/(\w+?):"(.*?)"/', $queryString, $queryMatches);
        foreach ($queryMatches[0] as $i => $match) {
            $fieldMatched = false;
            $fieldName = $queryMatches[1][$i];
            $values = preg_split('/\s*,\s*/', $queryMatches[2][$i]);

            switch ($fieldName) {
                case 'lifespan':
                    if (preg_match('/([<>])(.+)/', $values[0], $matches)) {
                        $op = $matches[1];
                        $lifespan = $matches[2];
                        $this->_query->andWhere('o.lifespan '.$op.' ?', $lifespan);
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
                    $this->_query->andWhereIn('o.species', $species);
                    $fieldMatched = true;
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
                    $this->_query->andWhereIn('og.symbol', $symbols);
                    $fieldMatched = true;
                    break;
                case 'geneAlleleType':
                    $this->_query->andWhereIn('og.alleleType', $values);
                    $fieldMatched = true;
                    break;
            }

            if ($fieldMatched) {
                // strip out the match so it isnt processed anymore
                $queryString = trim(str_replace($match, '', $queryString));
            }
        }

        // extract terms and add to dql
        $terms = array();
        preg_match_all('/"(.+?)"/', $queryString, $matches);
        foreach ($matches[0] as $i => $match) {
            $terms[] = $matches[1][$i];
            $queryString = str_replace($match, '', $queryString);
        }
        preg_match_all('/(\s*.+\s*)/', $queryString, $matches);
        foreach ($matches[0] as $i => $match) {
            $terms[] = $matches[1][$i];
            $queryStrign = str_replace($match, '', $queryString);
        }
        if (count($terms) > 0) {
            foreach ($terms as $term) {
                $whereString = '
                    o.body REGEXP ?
                    OR o.citationAuthor REGEXP ?
                    OR o.citationTitle REGEXP ?
                    ';
                $whereParams = array(
                    '[[:<:]]'.$term.'[[:>:]]',
                    '[[:<:]]'.$term.'[[:>:]]',
                    '[[:<:]]'.$term.'[[:>:]]',
                );

                // add fetched gene symbols to where clause
                $symbols = $this->_getGeneSymbols(array($term));

                foreach ($symbols as $symbol) {
                    if ($symbol != $term) {
                        $whereString .= ' OR og.symbol = ?';
                        $whereParams[] = $symbol;
                    }
                }

                // add fetched species  to where clause
                $species = $this->_getSpecies(array($term));
                foreach ($species as $name) {
                    if ($name != $term) {
                        $whereString .= ' OR o.species = ?';
                        $whereParams[] = $name;
                    }
                }
                $this->_query->andWhere($whereString, $whereParams);
            }
        }
    }

    private function _getSpecies($terms)
    {
        $species = array();
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
        $symbols = array();
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

}
