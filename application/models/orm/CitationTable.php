<?php

/**
 * CitationTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class CitationTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object CitationTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Citation');
    }

    public function getCitationData($pubmedId)
    {
        $q = Doctrine_Query::create()
            ->from('Citation c')
            ->where('c.pubmed_id = ?', $pubmedId);
        return $q->fetchOne();
    }

    public function importCitation($pubmedId)
    {
        if ($this->getCitationData($pubmedId)) {
            return;
        }

        $data = Application_Model_Service_PubMed::getCitationData($pubmedId);
        if (!$data) {
            return;
        }

        $citation = new Citation();
        $citation->pubmedId = $pubmedId;
        $citation->author = $data['author'];
        $citation->title = $data['title'];
        $citation->source = $data['source'];
        $citation->year = $data['year'];
        $citation->save();
    }
}