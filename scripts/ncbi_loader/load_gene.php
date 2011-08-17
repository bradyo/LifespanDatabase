<?php
ini_set('auto_detect_line_endings', true);

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
define('DATA_PATH', realpath(dirname(__FILE__) . '/../../data'));
define('APPLICATION_ENV', 'development');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Application.php';
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

$db = Zend_Registry::get('ncbiDb');

// taxon ids for human, mouse, worm, fly, and yeast
$keyTaxonIds = array(9606, 10090, 6239, 7227, 4932);


// update gene, gene_synonym, and gene_dbxref
echo "loading gene data\n";

//$db->exec('TRUNCATE TABLE gene');
////$db->exec('TRUNCATE TABLE gene_dbxref');
////$db->exec('TRUNCATE TABLE gene_synonym');
//
//$insertGeneStmt = $db->prepare('
//    INSERT INTO gene (gene_id, taxon_id, symbol, locus_tag, description, type)
//    VALUES (?, ?, ?, ?, ?, ?)');
////$insertSynonymStmt = $db->prepare('
////    INSERT INTO gene_synonym (gene_id, synonym) VALUES (?, ?)');
////$insertDbxrefStmt = $db->prepare('
////    INSERT INTO gene_dbxref (gene_id, dbxref) VALUES (?, ?)');
//
//$filename = realpath(dirname(__FILE__)).'/data/ncbi_gene_info_filtered.tab';
//$file = fopen($filename, "r");
//while (!feof($file)) {
//    $line = fgets($file);
//    $values = explode("\t", $line);
//
//    // insert gene info
//    $taxonId     = ($values[0] != '-') ? intval($values[0]) : null;
//    $geneId      = ($values[1] != '-') ? intval($values[1]) : null;
//    $symbol      = ($values[2] != '-') ? $values[2] : null;
//    $locusTag    = ($values[3] != '-') ? $values[3] : null;
//    $description = ($values[8] != '-') ? $values[8] : null;
//    $type        = ($values[9] != '-') ? $values[9] : null;
//
//    if (!in_array($taxonId, $keyTaxonIds)) {
//        continue;
//    }
//
//    $insertGeneStmt->execute(array($geneId, $taxonId, $symbol,
//        $locusTag, $description, $type));
//
////    // insert gene symonyms
////    if ($values[4] != '-') {
////        $inputString = $values[4];
////        if (strpos($inputString, '|') !== null) {
////            $synonyms = explode('|', $inputString);
////        } else {
////            $synonyms = array($inputString);
////        }
////
////        foreach ($synonyms as $synonym) {
////            $insertSynonymStmt->execute(array($geneId, $synonym));
////        }
////    }
////
////    // insert gene dbx refs
////    if ($values[5] != '-') {
////        $inputString = $values[5];
////        if (strpos($inputString, '|') !== null) {
////            $dbxrefs = explode('|', $inputString);
////        } else {
////            $dbxrefs = array($inputString);
////        }
////
////        foreach ($dbxrefs as $dbxref) {
////            $insertDbxrefStmt->execute(array($geneId, $dbxref));
////        }
////    }
//}
//fclose($file);



// update protein id using gene2refseq data
echo "updating gene protein ids\n";

$updateGeneStmt = $db->prepare('
    UPDATE gene SET protein_acc = ?, protein_id = ? WHERE gene_id = ?');

$filename = realpath(dirname(__FILE__)).'/data/ncbi_gene2refseq_filtered.tab';
$file = fopen($filename, "r");
while (!feof($file)) {
    $line = fgets($file);
    $values = explode("\t", $line);

    $taxonId    = ($values[0] != '-') ? $values[0] : null;
    $geneId     = ($values[1] != '-') ? $values[1] : null;
    $proteinAcc = ($values[5] != '-') ? $values[5] : null;
    $proteinId  = ($values[6] != '-') ? $values[6] : null;

    if (!in_array($taxonId, $keyTaxonIds)) {
        continue;
    }
    $updateGeneStmt->execute(array($proteinAcc, $proteinId, $geneId));
}


die();





// update taxonomy tables: taxon and taxon_synonym
echo "loading taxonomy data\n";

$db->exec('TRUNCATE TABLE taxon');
$db->exec('TRUNCATE TABLE taxon_synonym');

$insertTaxonStmt = $db->prepare('
    INSERT INTO taxon (taxon_id, name) VALUES (?, ?)');
$insertTaxonSynonymStmt = $db->prepare('
    INSERT INTO taxon_synonym (taxon_id, synonym, class) VALUES (?, ?, ?)');

$filename = realpath(dirname(__FILE__)).'/data/ncbi_taxon_filtered.tab';
$file = fopen($filename, "r");
while (!feof($file)) {
    $line = fgets($file);
    $values = explode("\t", $line);

    $taxonId = intval($values[0]);
    $name = $values[1];
    $type = $values[3];
    if ($type == 'scientific name' && !empty($name)) {
        $insertTaxonStmt->execute(array($taxonId, $name));
    }
}
$file = fopen($filename, "r");
while (!feof($file)) {
    $line = fgets($file);
    $values = explode("\t", $line);
    
    $taxonId = $values[0];
    $name = $values[1];
    $type = $values[3];
    if ($type != 'scientific name' && !empty($name)) {
        $insertTaxonSynonymStmt->execute(array($taxonId, $name, $type));
    }
}
fclose($file);
