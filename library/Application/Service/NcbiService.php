<?php

/**
 * Description of Ncbi
 *
 * @author brady
 */
class Application_Service_NcbiService
{
    public static function getGeneData($ncbiGeneId)
    {
        $db = Zend_Registry::get('ncbiDb');
        $stmt = $db->query(
            'SELECT g.*, t.name as species FROM gene g
            LEFT JOIN taxon t ON g.taxon_id = t.taxon_id
            WHERE g.gene_id = ?',
            $ncbiGeneId
        );
        $data = $stmt->fetch();
        return $data;
    }

    public static function getSpeciesName($ncbiTaxonId)
    {
        $db = Zend_Registry::get('ncbiDb');
        $stmt = $db->query('SELECT name FROM taxon WHERE taxon_id = ?', $ncbiTaxonId);
        $result = $stmt->fetch();
        return $result['name'];
    }

    public static function getCompoundName($ncbiCompoundId)
    {
        // TODO: add compounds to ncbi db
        return null;
    }

    public static function getTaxonData($ncbiTaxonId)
    {
        $db = Zend_Registry::get('ncbiDb');
        $stmt = $db->query('SELECT * FROM taxon WHERE taxon_id = ?', $ncbiTaxonId);
        return $stmt->fetch();
    }

    public static function getTaxonId($speciesName)
    {
        $db = Zend_Registry::get('ncbiDb');
        $stmt = $db->query('SELECT taxon_id FROM taxon WHERE name = ?', $speciesName);
        $result = $stmt->fetch();
        if ($result) {
            return intval($result['taxon_id']);
        }
    }

    public static function getTaxonSynonyms($ncbiTaxId)
    {
        $db = Zend_Registry::get('ncbiDb');
        $stmt = $db->query('SELECT synonym, class FROM taxon_synonym
            WHERE taxon_id = ?', $ncbiTaxId);
        return $stmt->fetchAll();
    }

    public static function getGeneSymbol($ncbiGeneId)
    {
        $db = Zend_Registry::get('ncbiDb');
        $stmt = $db->query('SELECT symbol FROM gene WHERE gene_id = ?', $ncbiGeneId);
        $result = $stmt->fetch();
        return $result['symbol'];
    }

    public static function getGeneIds($symbol, $taxonId = null)
    {
        $db = Zend_Registry::get('ncbiDb');
        if ($taxonId === null) {
            $stmt = $db->query('SELECT gene_id FROM gene WHERE symbol = BINARY ?', $symbol);
        } else {
            $stmt = $db->query('
                SELECT gene_id FROM gene WHERE symbol = ? AND taxon_id = ?',
                array($symbol, $taxonId));
        }

        $geneIds = array();
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $geneId = $row['gene_id'];
            $geneIds[$geneId] = $geneId;
        }
        return array_keys($geneIds);
    }

    public static function getGeneIdsBy($field, $value)
    {
        $db = Zend_Registry::get('ncbiDb');

        if ($field == 'protein_acc') {
            $stmt = $db->query(
                'SELECT gene_id FROM gene WHERE protein_acc = ?',
                $value
            );
        }
        else if ($field == 'uniprot_id') {
            $stmt = $db->query(
                'SELECT g.gene_id FROM gene g
                LEFT JOIN gene_uniprot u ON g.gene_id = u.gene_id
                WHERE u.uniprotkb_id = ?',
                $value
            );
        }
        else {
            return null;
        }

        $geneIds = array();
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $geneId = $row['gene_id'];
            $geneIds[$geneId] = $geneId;
        }
        return array_keys($geneIds);
    }

    public static function getGeneSynonyms($geneId)
    {
        $db = Zend_Registry::get('ncbiDb');
        $stmt = $db->query(
            'SELECT DISTINCT synonym FROM gene_synonym WHERE gene_id = ?',
            $geneId
        );

        $synonyms = array();
        foreach ($stmt->fetchAll() as $row) {
            $synonyms[] = $row['synonym'];
        }
        return $synonyms;
    }

    public static function getGeneDbxrefs($ncbiGeneId)
    {
        $db = Zend_Registry::get('ncbiDb');
        $stmt = $db->query(
            'SELECT DISTINCT dbxref FROM gene_dbxref WHERE gene_id = ?',
            $ncbiGeneId
        );

        $dbxrefs = array();
        foreach ($stmt->fetchAll() as $row) {
            $dbxrefs[] = $row['dbxref'];
        }
        return $dbxrefs;
    }

    public static function getGeneGos($ncbiGeneId)
    {
        $db = Zend_Registry::get('ncbiDb');
        $stmt = $db->query(
            'SELECT * FROM gene_go WHERE gene_id = ?',
            array($ncbiGeneId)
        );
        return $stmt->fetchAll();
    }

    public static function getGeneUniProtIds($ncbiGeneId)
    {
        $db = Zend_Registry::get('ncbiDb');
        $stmt = $db->query(
            'SELECT uniprotkb_id FROM gene_uniprot WHERE gene_id = ?',
            array($ncbiGeneId)
        );
        
        $uniprotIds = array();
        foreach ($stmt->fetchAll() as $row) {
            $uniprotIds[] = $row['uniprotkb_id'];
        }
        return $uniprotIds;
    }

    public static function getGeneHomologs($ncbiGeneId)
    {
        $rows = array();

        // fetch ppod homologs, ppod uses uniprot or ncbi protein accession
        $geneData = self::getGeneData($ncbiGeneId);
        $proteinAcc =  $geneData['protein_acc'];
        if ($proteinAcc !== null) {
            $database = 'NCBI';
            $homologs = Application_Service_PpodService::getHomologs($database, $proteinAcc);
            foreach ($homologs as $homolog) {
                $homolog['source'] = 'PPOD';
                $rows[] = $homolog;
            }
        }

        $uniprotIds = self::getGeneUniProtIds($ncbiGeneId);
        foreach ($uniprotIds as $uniprotId) {
            $database = 'UniProtKB';
            $homologs = Application_Service_PpodService::getHomologs($database, $uniprotId);
            foreach ($homologs as $homolog) {
                $homolog['source'] = 'PPOD';
                $rows[] = $homolog;
            }
        }
        
        return $rows;
    }

    public static function getCompoundSynonyms($ncbiCompoundId)
    {
        $db = Zend_Registry::get('ncbiDb');
        $stmt = $db->query(
            'SELECT DISTINCT synonym FROM compound_synonym WHERE compound_id = ?',
            $ncbiCompoundId
        );

        $synonyms = array();
        foreach ($stmt->fetchAll() as $row) {
            $synonyms[] = $row['synonym'];
        }
        return $synonyms;
    }

}


