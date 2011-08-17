<?php

/**
 * BaseTaxonomy
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property string $commonName
 * @property integer $ncbiTaxId
 * @property Doctrine_Collection $gene
 * @property Doctrine_Collection $synonyms
 * @property Doctrine_Collection $Observation
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseTaxonomy extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('taxonomy');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('name', 'string', 128, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '128',
             ));
        $this->hasColumn('common_name as commonName', 'string', 128, array(
             'type' => 'string',
             'notnull' => false,
             'length' => '128',
             ));
        $this->hasColumn('ncbi_tax_id as ncbiTaxId', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '4',
             ));

        $this->option('type', 'INNODB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Gene as gene', array(
             'local' => 'id',
             'foreign' => 'taxonomy_id'));

        $this->hasMany('TaxonomySynonym as synonyms', array(
             'local' => 'id',
             'foreign' => 'taxonomy_id'));

        $this->hasMany('Observation', array(
             'local' => 'id',
             'foreign' => 'taxonomy_id'));
    }
}