<?php

/**
 * BaseObservationCompound
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $observationId
 * @property integer $compoundId
 * @property integer $ncbiCompoundId
 * @property string $name
 * @property string $quantity
 * @property Compound $compound
 * @property Observation $Observation
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseObservationCompound extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('observation_compound');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('observation_id as observationId', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('compound_id as compoundId', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '4',
             ));
        $this->hasColumn('ncbi_compound_id as ncbiCompoundId', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '4',
             ));
        $this->hasColumn('name', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '64',
             ));
        $this->hasColumn('quantity', 'string', 64, array(
             'type' => 'string',
             'notnull' => false,
             'length' => '64',
             ));

        $this->option('type', 'INNODB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Compound as compound', array(
             'local' => 'compound_id',
             'foreign' => 'id'));

        $this->hasOne('Observation', array(
             'local' => 'observation_id',
             'foreign' => 'id'));
    }
}