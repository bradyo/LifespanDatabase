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


// update gene_go
echo "loading gene go data\n";

$db->exec('TRUNCATE TABLE gene_go');

$insertGeneGoStmt = $db->prepare('
    INSERT INTO gene_go (gene_id, go_id, evidence, category, term)
    VALUES (?, ?, ?, ?, ?)');

$filename = realpath(dirname(__FILE__)).'/data/ncbi_gene2go_filtered.tab';
$file = fopen($filename, "r");
while (!feof($file)) {
    $line = fgets($file);
    $values = explode("\t", $line);

    // insert gene info
    $taxonId    = ($values[0] != '-') ? intval($values[0]) : null;
    $geneId     = ($values[1] != '-') ? intval($values[1]) : null;
    $goId       = ($values[2] != '-') ? $values[2] : null;
    $goEvidence = ($values[3] != '-') ? $values[3] : null;
    $goTerm     = ($values[5] != '-') ? $values[5] : null;
    $goCategory = ($values[7] != '-') ? $values[7] : null;

    if (!in_array($taxonId, $keyTaxonIds)) {
        continue;
    }

    $insertGeneGoStmt->execute(array($geneId, $goId, $goEvidence, $goTerm, $goCategory));
}
fclose($file);