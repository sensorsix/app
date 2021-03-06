<?php

/**
 * BaseRoadmapDecision
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property bigint $roadmap_id
 * @property bigint $decision_id
 * @property Roadmap $Roadmap
 * @property Decision $Decision
 * 
 * @method bigint          getRoadmapId()   Returns the current record's "roadmap_id" value
 * @method bigint          getDecisionId()  Returns the current record's "decision_id" value
 * @method Roadmap         getRoadmap()     Returns the current record's "Roadmap" value
 * @method Decision        getDecision()    Returns the current record's "Decision" value
 * @method RoadmapDecision setRoadmapId()   Sets the current record's "roadmap_id" value
 * @method RoadmapDecision setDecisionId()  Sets the current record's "decision_id" value
 * @method RoadmapDecision setRoadmap()     Sets the current record's "Roadmap" value
 * @method RoadmapDecision setDecision()    Sets the current record's "Decision" value
 * 
 * @package    dmp
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseRoadmapDecision extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('roadmap_decision');
        $this->hasColumn('roadmap_id', 'bigint', 20, array(
             'type' => 'bigint',
             'length' => 20,
             ));
        $this->hasColumn('decision_id', 'bigint', 20, array(
             'type' => 'bigint',
             'length' => 20,
             ));

        $this->option('symfony', array(
             'filter' => false,
             'form' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Roadmap', array(
             'local' => 'roadmap_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE',
             'onUpdate' => 'CASCADE'));

        $this->hasOne('Decision', array(
             'local' => 'decision_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE',
             'onUpdate' => 'CASCADE'));
    }
}