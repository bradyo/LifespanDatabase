<?php

/**
 * Description of NcbiPubmed
 *
 * @author brady
 */
class Application_Service_CitationService
{
    public static function getCitationFromCache($pubmedId)
    {
        $db = Zend_Registry::get('db');
        $sql = 'SELECT pubmed_id, year, author, title, source FROM citation
            WHERE pubmed_id = ? ';
        $citation = $db->fetchRow($sql, $pubmedId);
        return $citation;
    }

    public static function getCitationData($pubmedId)
	{
        $citation = self::getCitationFromCache($pubmedId);
        if (!$citation) {
            $citation = Application_Service_Remote_NcbiService::getCitation($pubmedId);
        }
        return $citation;
    }

}


