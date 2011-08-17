<?php
ini_set('auto_detect_line_endings', true);

require_once '../../public/global.php';
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

$db = Zend_Registry::get('ncbiDb');

// taxon ids for human, mouse, worm, fly, and yeast
$keyTaxonIds = array(9606, 10090, 6239, 7227, 4932);

// update protein id using gene2refseq data
echo "updating gene protein ids\n";

$updateGeneStmt = $db->prepare('
    UPDATE gene SET protein_acc = ?, protein_id = ? WHERE gene_id = ?');

$end = 156627;
$i = 0;
$delta = round($end / 100);
$limit = $delta;
$percent = 1;

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
    $params = array($proteinAcc, $proteinId, $geneId);
    $updateGeneStmt->execute($params);

    if ($i > $limit) {
        $limit += $delta;
        echo "$percent% done\n";
        $percent++;
    }
    $i++;
}
