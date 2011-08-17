<?php
ini_set('auto_detect_line_endings', true);

require_once '../../public/global.php';
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

$db = Zend_Registry::get('ppodDb');
$db->exec('TRUNCATE TABLE homolog');

$filenames = array(
    'Jaccard' => realpath(dirname(__FILE__)).'/data/GO3-Jaccard.tsv',
    'OrthoMCL' => realpath(dirname(__FILE__)).'/data/GO3-OrthoMCL.tsv',
    'Para' => realpath(dirname(__FILE__)).'/data/GO3-Para.tsv',
);

foreach ($filenames as $algorithm => $filename) {
    loadFile($filename, $algorithm);
}

function loadFile($filename, $algorithm)
{
    global $db;

    echo "processing: $filename\n";

    $insertStmt = $db->prepare('
        INSERT INTO homolog (family_id, algorithm, database_id, protein_id)
        VALUES (?, ?, ?, ?)
    ');

    $db->beginTransaction();

    $file = fopen($filename, "r");
    while (!feof($file)) {
        $line = fgets($file);
        $values = explode("\t", $line);
        if (count($values) < 2) {
            echo "skipping line: $line\n";
            continue;
        }

        $family = array_shift($values);
        if (strstr($family, 'orphans') !== false) {
            continue;
        }

        foreach ($values as $value) {
            /* values:
                CAEEL|WB:WBGene00017664|UniProtKB:Q19677
                DROME|FB:FBgn0001148|UniProtKB:P09082
                HUMAN|ENSEMBL:ENSG00000125813|UniProtKB:P15863
                MOUSE|MGI:MGI:97485|UniProtKB:P09084
                YEAST|SGD:S000001368|UniProtKB:P40484
             */
            $ids = explode('|', $value);
            if (count($ids) < 2) {
                echo "skipping value: $value\n";
                continue;
            }

            $species = array_shift($ids);
            $validSpecies = array('HUMAN', 'MOUSE', 'CAEEL', 'DROME', 'YEAST');
            if (!in_array($species, $validSpecies)) {
                continue;
            }

            $database = null;
            $protein = null;
            foreach ($ids as $id) {
                if (preg_match("/^(UniProtKB|NCBI):(.+)$/", $id, $matches)) {
                    $database = $matches[1];
                    $protein = $matches[2];
                    break;
                }
            }
            if ($database == null || $protein == null) {
                continue;
            }

            // save to database
            $insertStmt->execute(array($family, $algorithm, $database, $protein));
        }
    }
    fclose($file);

    $db->commit();
}
