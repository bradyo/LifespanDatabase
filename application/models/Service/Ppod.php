<?php

/**
 * Description of Ppod
 *
 * @author brady
 */
class Application_Model_Service_Ppod
{
    public static function getHomologs($databaseId, $proteinId)
    {
        $db = Zend_Registry::get('ppodDb');

        // get homolog families for this protein
        $familyIds = array();
        $stmt = $db->query(
            'SELECT DISTINCT(family_id) FROM homolog
            WHERE database_id = ? AND protein_id = ?',
            array($databaseId, $proteinId)
        );
        foreach ($stmt->fetchAll() as $row) {
            $familyIds[] = $row['family_id'];
        }

        // get data for each family
        $rows = array();
        foreach ($familyIds as $familyId) {
            $stmt = $db->query('SELECT * FROM homolog 
                WHERE family_id = ? AND (database_id != ? || protein_id != ?)',
                array($familyId, $databaseId, $proteinId)
            );
            foreach ($stmt->fetchAll() as $row) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
}

