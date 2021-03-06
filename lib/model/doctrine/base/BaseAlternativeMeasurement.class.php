<?php

/**
 * BaseAlternativeMeasurement
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $alternative_head_id
 * @property integer $alternative_tail_id
 * @property integer $criterion_id
 * @property string $score
 * @property integer $response_id
 * @property enum $rating_method
 * @property Response $Response
 * @property Alternative $Alternative
 * @property Criterion $Criterion
 * 
 * @method integer                getAlternativeHeadId()   Returns the current record's "alternative_head_id" value
 * @method integer                getAlternativeTailId()   Returns the current record's "alternative_tail_id" value
 * @method integer                getCriterionId()         Returns the current record's "criterion_id" value
 * @method string                 getScore()               Returns the current record's "score" value
 * @method integer                getResponseId()          Returns the current record's "response_id" value
 * @method enum                   getRatingMethod()        Returns the current record's "rating_method" value
 * @method Response               getResponse()            Returns the current record's "Response" value
 * @method Alternative            getAlternative()         Returns the current record's "Alternative" value
 * @method Criterion              getCriterion()           Returns the current record's "Criterion" value
 * @method AlternativeMeasurement setAlternativeHeadId()   Sets the current record's "alternative_head_id" value
 * @method AlternativeMeasurement setAlternativeTailId()   Sets the current record's "alternative_tail_id" value
 * @method AlternativeMeasurement setCriterionId()         Sets the current record's "criterion_id" value
 * @method AlternativeMeasurement setScore()               Sets the current record's "score" value
 * @method AlternativeMeasurement setResponseId()          Sets the current record's "response_id" value
 * @method AlternativeMeasurement setRatingMethod()        Sets the current record's "rating_method" value
 * @method AlternativeMeasurement setResponse()            Sets the current record's "Response" value
 * @method AlternativeMeasurement setAlternative()         Sets the current record's "Alternative" value
 * @method AlternativeMeasurement setCriterion()           Sets the current record's "Criterion" value
 * 
 * @package    dmp
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAlternativeMeasurement extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('alternative_measurement');
        $this->hasColumn('alternative_head_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('alternative_tail_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('criterion_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('score', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('response_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('rating_method', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'direct rating',
              1 => 'direct float',
              2 => 'forced ranking',
              3 => 'five point scale',
              4 => 'ten point scale',
              5 => 'comment',
             ),
             'notnull' => true,
             ));

        $this->option('symfony', array(
             'filter' => false,
             'form' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Response', array(
             'local' => 'response_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('Alternative', array(
             'local' => 'alternative_head_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('Criterion', array(
             'local' => 'criterion_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}