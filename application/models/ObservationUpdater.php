<?php

/**
 * Description of ObservationUpdater
 *
 * @author brady
 */
class Application_Model_ObservationUpdater
{
    private $_username;
    private $_isModerator = false;
    private $_heldItems = array();
    private $_isResubmit = false;
 
    public function __construct($username = 'anonymous', $isModerator = false, $isResubmit = false)
    {
        $this->_username = $username;
        $this->_isModerator = $isModerator;
        $this->_isResubmit = $isResubmit;
    }

    public function processYaml($yaml)
    {
        $this->_heldItems = array();

        // first make sure we can load the yaml (valid format)
        try {
            require_once('sfYaml/sfYaml.php');
            $yaml = trim($yaml);
            $data = sfYaml::load($yaml);

            if (is_array($data)) {
                $data = $this->_stripNulls($data);
            }
        }
        catch (Exception $e) {
            $this->_heldItems[] = array(
                'messages' => array($e->getMessage()),
                'yaml' => $yaml,
            );
            return false;
        }

        // check for data root (top level)
        if (!is_array($data['observations'])) {
            $this->_heldItems[] = array(
                'messages' => array('Yaml input must contain list of observations.'),
                'yaml' => $yaml,
            );
            return false;
        }

        // now we need to check individual observations
        $addedCount = 0;
        foreach ($data['observations'] as $data) {
            $errorMessages = $this->_checkObservationData(&$data);
            if (count($errorMessages) == 0) {
                $this->_importData($data);
                $addedCount++;
            } else {
                $this->_heldItems[] = array(
                    'messages' => $errorMessages,
                    'yaml' => $this->_getObservationYaml($data),
                );
            }
        }
        return $addedCount;
    }

    private function _getObservationYaml($data)
    {
        try {
            require_once('sfYaml/sfYaml.php');

            // set the yaml to be a list of observations, even though data
            // contains just one observation's data
            $rootedData = array('observations' => array($data));
            $yaml = sfYaml::dump($rootedData, 5);
            return $yaml;
        }
        catch (Exception $e) {
            return 'failed to dump yaml';
        }
    }

    public function getHeldItems()
    {
        return $this->_heldItems;
    }


    private function _checkObservationData($data)
    {
        $messages = array();

        // check that the id exists, if given
        if (isset($data['id'])) {
            $id = $data['id'];
            $observation = Doctrine_Core::getTable('Observation')
                ->findOneBy('id', $id);
            if (!$observation) {
                $messages[] = "Observation with id = $id does not exist";
            }
        }

        // check lifespan effect
        if (isset($data['lifespanEffect'])) {
            $validEffects = array_keys(Observation::getLifespanEffects());
            if (!in_array($data['lifespanEffect'], $validEffects)) {
                $messages[] = 'lifespanEffect must be: '
                    . join(', ', $validEffects) . '.';
            }
        }

        // check lifespan measure
        if (isset($data['lifespanMeasure'])) {
            $validMeasures = array_keys(Observation::getLifespanMeasures());
            if (!in_array($data['lifespanMeasure'], $validMeasures)) {
                $messages[] = 'lifespanMeasure must be: '
                    . join(', ', $validMeasures) . '.';
            }
        }

        // check lifespan units
        if (isset($data['lifespanUnit'])) {
            $validUnits = array_keys(Observation::getLifespanUnits());
            if (!in_array($data['lifespanUnit'], $validUnits)) {
                $messages[] = 'lifespanUnit must be: '
                    . join(', ', $validUnits) . '.';
            }
        }

        // check lifespan
        if (isset($data['lifespan'])) {
            if (!is_numeric($data['lifespan'])) {
                $messages[] = 'lifespan must be numeric.';
            }
        }

        // check lifespan ase
        if (isset($data['lifespanBase'])) {
            if (!is_numeric($data['lifespanBase'])) {
                $messages[] = 'lifespanBase must be numeric.';
            }
        }

        // check lifespan change
        if (isset($data['lifespanChange'])) {
            if (!is_numeric($data['lifespanChange'])) {
                $messages[] = 'lifespanChange must be numeric.';
            }
        }

        // check that if taxon id is given, it matches species
        if (isset($data['ncbiTaxId'])) {
            if (!is_int($data['ncbiTaxId'])) {
                $messages[] = 'ncbiTaxId must be an integer.';
            }
        }

        // if both ncbi tax id and species are set, check that they match
        if (isset($data['ncbiTaxId']) && isset($data['species'])) {
            $ncbiTaxId = $data['ncbiTaxId'];
            $ncbiSpecies = Application_Model_Service_Ncbi::getSpeciesName($ncbiTaxId);
            if ($ncbiSpecies !== null && $data['species'] !== $ncbiSpecies) {
                $messages[] = "species does not match ncbiTaxId ($ncbiTaxId is $ncbiSpecies).";
            } else {
                $data['species'] = $ncbiSpecies;
            }
        }

        if (isset($data['matingType'])) {
            $validTypes = array_keys(Observation::getMatingTypes());
            if (!in_array($data['matingType'], $validTypes)) {
                $messages[] = 'matingType must be: '
                    . join(', ', $validTypes) . '.';
            }
        }

        // check that either pubmed id or citation info is set
        if (!isset($data['citationPubmedId'])) {
            if (empty($data['citationAuthor']) || empty($data['citationTitle'])
                || empty($data['citationSource']) || empty($data['citationYear'])) {
                $messages[] = 'citationPubmedId or citationAuthor, citationTitle, '
                    . 'citationSource, and citationYear is required.';
            }
        } else {
            if (!is_int($data['citationPubmedId'])) {
                $messages[] = 'citationPubmedId must be a integer.';
            } else {
                $pubmedId = $data['citationPubmedId'];
                $citationData = Application_Model_Service_PubMed::getCitationData($pubmedId);
                if (!$citationData) {
                    $messages[] = 'failed to get citation data for pubmed id';
                } else {
                    $data['citationAuthor'] = $citationData['author'];
                    $data['citationTitle'] = $citationData['title'];
                    $data['citationSource'] = $citationData['source'];
                    $data['citationYear'] = $citationData['year'];
                }
            }
        }

        // check gene symbol and gene ids
        if (isset($data['genes'])) {
            $validAlleleTypes = array_keys(ObservationGene::getAlleleTypes());
            foreach ($data['genes'] as &$gene) {
                // must have gene symbol or ncbi gene id
                if (!isset($gene['symbol']) && !isset($gene['ncbiGeneId'])) {
                    $messages[] = 'Gene symbol or ncbiGeneId required.';
                }

                // ncbiGeneId species must match species or ncbiTaxId
                if (isset($gene['ncbiGeneId'])) {
                    if (!is_int($gene['ncbiGeneId'])) {
                        $messages[] = 'Gene ncbiGeneId must be an integer.';
                    } else {
                        if (!isset($gene['symbol'])) {
                            // look up gene symbol
                            $ncbiGeneId = $gene['ncbiGeneId'];
                            $symbol = Application_Model_Service_Ncbi::getGeneSymbol($ncbiGeneId);
                            if ($symbol) {
                                $gene['symbol'] = $symbol;
                            } else {
                                $messages[] = 'Gene symbol could not be found for ncbiGeneId.';
                            }
                        }
                    }
                }

                // look up symbol in ncbi genes
                if (!isset($gene['ncbiGeneId']) && isset($gene['symbol'])) {
                    $symbol = $gene['symbol'];

                    // include species info in gene search
                    $ncbiTaxId = null;
                    if (isset($data['ncbiTaxId'])) {
                        $ncbiTaxId = $data['ncbiTaxId'];
                    } elseif (isset($data['species'])) {
                        $species = $data['species'];
                        $ncbiTaxId = Application_Model_Service_Ncbi::getTaxonId($species);
                    }

                    $geneIds = Application_Model_Service_Ncbi::getGeneIds($symbol, $ncbiTaxId);
                    if (count($geneIds) > 1) {
                        $messages[] = "Gene symbol '$symbol' resolves to multiple NCBI gene IDs: "
                            . join(', ', $geneIds) . '.';
                    } elseif (count($geneIds) == 1) {
                        $gene['ncbiGeneId'] = $geneIds[0];
                    }
                }

                // check allele type
                if (isset($gene['alleleType'])) {
                    if (!in_array($gene['alleleType'], $validAlleleTypes)) {
                        $messages[] = 'Gene alleleType must be: '
                            . join(', ', $validAlleleTypes) . '.';
                    }
                }
            }
        }

        // check compounds
        if (isset($data['compounds'])) {
            foreach ($data['compounds'] as $compound) {
                if (!isset($compound['name']) && !isset($compound['ncbiCompoundId'])) {
                    $messages[] = 'Compound name or ncbiCompoundId required.';
                }
            }
        }

        // check environments
        if (isset($data['environments'])) {
            foreach ($data['environments'] as $environment) {
                if (!isset($environment['type'])) {
                    $messages[] = 'Environment type is required.';
                }
            }
        }

        // check revision fields
        if (isset($data['status'])) {
            $validTypes = array_keys(Observation::getStatusChoices());
            if (!in_array($data['status'], $validTypes)) {
                $messages[] = 'status must be: ' . join(', ', $validTypes) . '.';
            }
        }

        // if not resubmitting, throw errors on some minor messages
        if (!$this->_isResubmit) {
            // nothing for now
        }

        return $messages;
    }

    private function _stripNulls($hash)
    {
        $strippedHash = array();
        foreach ($hash as $key => $value) {
            if (is_array($value)) {
                $strippedHash[$key] = $this->_stripNulls($value);
            } else {
                if (!empty($value)) {
                    $strippedHash[$key] = $value;
                }
            }
        }
        return $strippedHash;
    }


    private function _importData($values)
    {
        // create a new revision on this observation
        $revision = new ObservationRevision();

        if ($values['status'] == 'deleted') {
            $revision->action = 'delete';
        } else {
            if (empty($values['id'])) {
                $revision->action = 'create';
            } else {
                $revision->action = 'edit';
            }
        }
        $revision->observationId = $values['id'];
        $revision->observationData = json_encode($values);
        $revision->requestedBy = $this->_username;
        $revision->requestedAt = date('Y-m-d H:i:s');
        $revision->status = 'pending';
        $revision->save();

        if ($this->_isModerator) {
            $reviewedBy = $this->_username;
            $reviewerComment = $values['reviewerComment'];
            $revision->accept($reviewedBy, $reviewerComment);
        }
    }

}
