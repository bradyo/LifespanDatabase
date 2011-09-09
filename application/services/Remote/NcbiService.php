<?php

/**
 * Interfaces to the NCBI web service
 *
 * @author brady
 */
class Application_Service_Remote_NcbiService
{
    public static function getCitation($pubmedId)
    {
        // see http://www.ncbi.nlm.nih.gov/entrez/query/DTD/pubmed_080101.dtd
        // for xml DTD definition
        $url = sprintf('http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?'
            . 'db=pubmed&report=citation&mode=xml&id=%d', intval($pubmedId));

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

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
                'year' => $year,
                'author' => $authors,
                'title' => $title,
                'source' => $source,
            );
            return $data;
        }

        return null; // no citation found
    }
}


