<?php

/**
 * BaseComment
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $decision_id
 * @property integer $alternative_id
 * @property integer $criterion_id
 * @property string $text
 * @property string $email
 * @property integer $user_id
 * @property Decision $Decision
 * @property Criterion $Criterion
 * @property Alternative $Alternative
 * @property sfGuardUser $User
 * 
 * @method integer     getDecisionId()     Returns the current record's "decision_id" value
 * @method integer     getAlternativeId()  Returns the current record's "alternative_id" value
 * @method integer     getCriterionId()    Returns the current record's "criterion_id" value
 * @method string      getText()           Returns the current record's "text" value
 * @method string      getEmail()          Returns the current record's "email" value
 * @method integer     getUserId()         Returns the current record's "user_id" value
 * @method Decision    getDecision()       Returns the current record's "Decision" value
 * @method Criterion   getCriterion()      Returns the current record's "Criterion" value
 * @method Alternative getAlternative()    Returns the current record's "Alternative" value
 * @method sfGuardUser getUser()           Returns the current record's "User" value
 * @method Comment     setDecisionId()     Sets the current record's "decision_id" value
 * @method Comment     setAlternativeId()  Sets the current record's "alternative_id" value
 * @method Comment     setCriterionId()    Sets the current record's "criterion_id" value
 * @method Comment     setText()           Sets the current record's "text" value
 * @method Comment     setEmail()          Sets the current record's "email" value
 * @method Comment     setUserId()         Sets the current record's "user_id" value
 * @method Comment     setDecision()       Sets the current record's "Decision" value
 * @method Comment     setCriterion()      Sets the current record's "Criterion" value
 * @method Comment     setAlternative()    Sets the current record's "Alternative" value
 * @method Comment     setUser()           Sets the current record's "User" value
 * 
 * @package    dmp
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseComment extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('comment');
        $this->hasColumn('decision_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('alternative_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('criterion_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('text', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('email', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('user_id', 'integer', null, array(
             'type' => 'integer',
             ));

        $this->option('symfony', array(
             'filter' => false,
             'form' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Decision', array(
             'local' => 'decision_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('Criterion', array(
             'local' => 'criterion_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('Alternative', array(
             'local' => 'alternative_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('sfGuardUser as User', array(
             'local' => 'user_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             'created' => 
             array(
              'name' => 'created_at',
              'type' => 'timestamp',
              'format' => 'Y-m-d H:i:s',
             ),
             'updated' => 
             array(
              'name' => 'updated_at',
              'type' => 'timestamp',
              'format' => 'Y-m-d H:i:s',
             ),
             ));
        $this->actAs($timestampable0);
    }
}