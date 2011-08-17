<?php

require_once '../../public/global.php';
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

$db = Zend_Registry::get('ncbiDb');
$fetchStmt = $db->prepare('
    SELECT gene_id FROM gene WHERE gene_id = ? LIMIT 1
');
$deleteStmt = $db->prepare('
    DELETE FROM gene_uniprot WHERE gene_id = ?
');

$end = 23013156;
$i = 0;
$delta = round($end / 100);
$limit = $delta;
$percent = 1;


$stmt = $db->query('SELECT gene_id FROM gene_uniprot');
$stmt->execute();

while ($row = $stmt->fetch()) {
    $geneId = $row['gene_id'];

    $fetchStmt->execute(array($geneId));
    $item = $fetchStmt->fetch();
    if (!$item) {
        $deleteStmt->execute(array($geneId));
    }

    if ($i > $limit) {
        $limit += $delta;
        echo "$percent% done\n";
        $percent++;
    }
    $i++;
}

