<?php

/**
 * BaseCompoundSynonym
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $compoundId
 * @property string $synonym
 * @property Compound $Compound
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCompoundSynonym extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('compound_synonym');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('compound_id as compoundId', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('synonym', 'string', 64, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '64',
             ));

        $this->option('type', 'INNODB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Compound', array(
             'local' => 'compound_id',
             'foreign' => 'id'));
    }
}