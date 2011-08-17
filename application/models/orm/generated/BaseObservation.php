<?php

/**
 * BaseObservation
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property float $lifespan
 * @property float $lifespanBase
 * @property string $lifespanUnit
 * @property float $lifespanChange
 * @property string $lifespanEffect
 * @property string $lifespanMeasure
 * @property integer $taxonomyId
 * @property string $species
 * @property string $strain
 * @property string $cellType
 * @property string $matingType
 * @property double $temperature
 * @property integer $citationPubmedId
 * @property integer $citationYear
 * @property string $citationAuthor
 * @property string $citationTitle
 * @property string $citationSource
 * @property string $body
 * @property integer $geneCount
 * @property integer $compoundCount
 * @property integer $environmentCount
 * @property string $createdAt
 * @property string $updatedAt
 * @property string $status
 * @property Taxonomy $taxonomy
 * @property Doctrine_Collection $genes
 * @property Doctrine_Collection $compounds
 * @property Doctrine_Collection $environments
 * @property Doctrine_Collection $revisions
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseObservation extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('observation');
        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
         ));
        $this->hasColumn('lifespan as lifespan', 'float', null);
        $this->hasColumn('lifespan_base as lifespanBase', 'float', null);
        $this->hasColumn('lifespan_units as lifespanUnit', 'string', 64);
        $this->hasColumn('lifespan_change as lifespanChange', 'float', null);
        $this->hasColumn('lifespan_effect as lifespanEffect', 'string', 64);
        $this->hasColumn('lifespan_measure as lifespanMeasure', 'string', 64);
        $this->hasColumn('taxonomy_id as taxonomyId', 'integer', 4);
        $this->hasColumn('species as species', 'string', 64);
        $this->hasColumn('strain as strain', 'string', 64);
        $this->hasColumn('cell_type as cellType', 'string', 64);
        $this->hasColumn('mating_type as matingType', 'string', 64);
        $this->hasColumn('temperature as temperature', 'decimal', 5, array(
            'scale' => 2,
        ));
        $this->hasColumn('citation_pubmed_id as citationPubmedId', 'integer', 4);
        $this->hasColumn('citation_year as citationYear', 'integer', 4);
        $this->hasColumn('citation_author as citationAuthor', 'string', 255);
        $this->hasColumn('citation_title as citationTitle', 'string', 255);
        $this->hasColumn('citation_source as citationSource', 'string', 255);
        $this->hasColumn('body as body', 'clob', 65532);
        $this->hasColumn('gene_count as geneCount', 'integer', 4, array(
            'default' => 0,
        ));
        $this->hasColumn('compound_count as compoundCount', 'integer', 4, array(
            'default' => 0,
         ));
        $this->hasColumn('environment_count as environmentCount', 'integer', 4, array(
            'default' => 0,
        ));
        $this->hasColumn('created_at as createdAt', 'datetime', 64);
        $this->hasColumn('updated_at as updatedAt', 'datetime', 64);
        $this->hasColumn('status as status', 'string', 64);

        $this->option('type', 'INNODB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Taxonomy as taxonomy', array(
            'local' => 'taxonomy_id',
            'foreign' => 'id'
        ));
        $this->hasMany('ObservationGene as genes', array(
            'local' => 'id',
            'foreign' => 'observation_id'
        ));
        $this->hasMany('ObservationCompound as compounds', array(
            'local' => 'id',
            'foreign' => 'observation_id'
        ));
        $this->hasMany('ObservationEnvironment as environments', array(
            'local' => 'id',
            'foreign' => 'observation_id'
        ));
        $this->hasMany('ObservationRevision as revisions', array(
            'local' => 'id',
            'foreign' => 'observation_id'
        ));
    }
}