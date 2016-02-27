<?php

/**
 * BaseCriteriaTemplate
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $name
 * @property integer $template_id
 * @property enum $measurement
 * @property enum $variable_type
 * @property TypeTemplate $Template
 * 
 * @method string           getName()          Returns the current record's "name" value
 * @method integer          getTemplateId()    Returns the current record's "template_id" value
 * @method enum             getMeasurement()   Returns the current record's "measurement" value
 * @method enum             getVariableType()  Returns the current record's "variable_type" value
 * @method TypeTemplate     getTemplate()      Returns the current record's "Template" value
 * @method CriteriaTemplate setName()          Sets the current record's "name" value
 * @method CriteriaTemplate setTemplateId()    Sets the current record's "template_id" value
 * @method CriteriaTemplate setMeasurement()   Sets the current record's "measurement" value
 * @method CriteriaTemplate setVariableType()  Sets the current record's "variable_type" value
 * @method CriteriaTemplate setTemplate()      Sets the current record's "Template" value
 * 
 * @package    dmp
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCriteriaTemplate extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('criteria_template');
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('template_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('measurement', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'direct rating',
              1 => 'direct float',
              2 => 'forced ranking',
              3 => 'five point scale',
              4 => 'ten point scale',
             ),
             'default' => 'five point scale',
             ));
        $this->hasColumn('variable_type', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'Benefit',
              1 => 'Cost',
              2 => 'Info',
             ),
             'default' => 'Benefit',
             ));

        $this->option('symfony', array(
             'filter' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('TypeTemplate as Template', array(
             'local' => 'template_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}