<?php
ini_set('auto_detect_line_endings', true);

require_once '../../public/global.php';
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

$db = Zend_Registry::get('ncbiDb');
$fetchStmt = $db->prepare('
    SELECT gene_id FROM gene WHERE protein_id = ? LIMIT 1
');
$insertStmt = $db->prepare('
    INSERT INTO gene_uniprot (gene_id, uniprotkb_id) VALUES (?, ?)
');
$db->exec('TRUNCATE TABLE gene_uniprot');
$db->beginTransaction();

$filename = '/shared/dbx/uniprot/filtered/idmapping-gi.dat';
$file = fopen($filename, "r");
while (!feof($file)) {
    $line = trim(fgets($file, 100));
    $values = explode("\t", $line);
    if (count($values) < 3) {
        echo "skipping line: $line\n";
        continue;
    }
    $proteinId  = $values[2];
    $uniprotId  = $values[0];

    $fetchStmt->execute(array($proteinId));
    $item = $fetchStmt->fetch();
    if (!$item) {
        continue; // skip gene
    }

    $geneId = $item['gene_id'];
    $insertStmt->execute(array($geneId, $uniprotId));
}
fclose($file);
$db->commit();
