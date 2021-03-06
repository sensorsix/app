<?php

/**
 * BaseRole
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $name
 * @property integer $decision_id
 * @property boolean $prioritize
 * @property boolean $show_criteria_weights
 * @property boolean $show_alternatives_score
 * @property enum $prioritization_method
 * @property boolean $view_matrix
 * @property boolean $updateable
 * @property boolean $anonymous
 * @property boolean $show_comments
 * @property boolean $collect_items
 * @property boolean $display_items
 * @property boolean $allow_voting
 * @property boolean $dashboard
 * @property string $comment
 * @property string $token
 * @property string $continue_url
 * @property enum $language
 * @property boolean $active
 * @property Doctrine_Collection $Files
 * @property Decision $Decision
 * @property Doctrine_Collection $RoleFilter
 * @property Doctrine_Collection $Response
 * @property Doctrine_Collection $RoleUploadedFile
 * @property Doctrine_Collection $PlannedAlternativeMeasurement
 * @property Doctrine_Collection $PlannedCriteria
 * 
 * @method string              getName()                          Returns the current record's "name" value
 * @method integer             getDecisionId()                    Returns the current record's "decision_id" value
 * @method boolean             getPrioritize()                    Returns the current record's "prioritize" value
 * @method boolean             getShowCriteriaWeights()           Returns the current record's "show_criteria_weights" value
 * @method boolean             getShowAlternativesScore()         Returns the current record's "show_alternatives_score" value
 * @method enum                getPrioritizationMethod()          Returns the current record's "prioritization_method" value
 * @method boolean             getViewMatrix()                    Returns the current record's "view_matrix" value
 * @method boolean             getUpdateable()                    Returns the current record's "updateable" value
 * @method boolean             getAnonymous()                     Returns the current record's "anonymous" value
 * @method boolean             getShowComments()                  Returns the current record's "show_comments" value
 * @method boolean             getCollectItems()                  Returns the current record's "collect_items" value
 * @method boolean             getDisplayItems()                  Returns the current record's "display_items" value
 * @method boolean             getAllowVoting()                   Returns the current record's "allow_voting" value
 * @method boolean             getDashboard()                     Returns the current record's "dashboard" value
 * @method string              getComment()                       Returns the current record's "comment" value
 * @method string              getToken()                         Returns the current record's "token" value
 * @method string              getContinueUrl()                   Returns the current record's "continue_url" value
 * @method enum                getLanguage()                      Returns the current record's "language" value
 * @method boolean             getActive()                        Returns the current record's "active" value
 * @method Doctrine_Collection getFiles()                         Returns the current record's "Files" collection
 * @method Decision            getDecision()                      Returns the current record's "Decision" value
 * @method Doctrine_Collection getRoleFilter()                    Returns the current record's "RoleFilter" collection
 * @method Doctrine_Collection getResponse()                      Returns the current record's "Response" collection
 * @method Doctrine_Collection getRoleUploadedFile()              Returns the current record's "RoleUploadedFile" collection
 * @method Doctrine_Collection getPlannedAlternativeMeasurement() Returns the current record's "PlannedAlternativeMeasurement" collection
 * @method Doctrine_Collection getPlannedCriteria()               Returns the current record's "PlannedCriteria" collection
 * @method Role                setName()                          Sets the current record's "name" value
 * @method Role                setDecisionId()                    Sets the current record's "decision_id" value
 * @method Role                setPrioritize()                    Sets the current record's "prioritize" value
 * @method Role                setShowCriteriaWeights()           Sets the current record's "show_criteria_weights" value
 * @method Role                setShowAlternativesScore()         Sets the current record's "show_alternatives_score" value
 * @method Role                setPrioritizationMethod()          Sets the current record's "prioritization_method" value
 * @method Role                setViewMatrix()                    Sets the current record's "view_matrix" value
 * @method Role                setUpdateable()                    Sets the current record's "updateable" value
 * @method Role                setAnonymous()                     Sets the current record's "anonymous" value
 * @method Role                setShowComments()                  Sets the current record's "show_comments" value
 * @method Role                setCollectItems()                  Sets the current record's "collect_items" value
 * @method Role                setDisplayItems()                  Sets the current record's "display_items" value
 * @method Role                setAllowVoting()                   Sets the current record's "allow_voting" value
 * @method Role                setDashboard()                     Sets the current record's "dashboard" value
 * @method Role                setComment()                       Sets the current record's "comment" value
 * @method Role                setToken()                         Sets the current record's "token" value
 * @method Role                setContinueUrl()                   Sets the current record's "continue_url" value
 * @method Role                setLanguage()                      Sets the current record's "language" value
 * @method Role                setActive()                        Sets the current record's "active" value
 * @method Role                setFiles()                         Sets the current record's "Files" collection
 * @method Role                setDecision()                      Sets the current record's "Decision" value
 * @method Role                setRoleFilter()                    Sets the current record's "RoleFilter" collection
 * @method Role                setResponse()                      Sets the current record's "Response" collection
 * @method Role                setRoleUploadedFile()              Sets the current record's "RoleUploadedFile" collection
 * @method Role                setPlannedAlternativeMeasurement() Sets the current record's "PlannedAlternativeMeasurement" collection
 * @method Role                setPlannedCriteria()               Sets the current record's "PlannedCriteria" collection
 * 
 * @package    dmp
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseRole extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('role');
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('decision_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('prioritize', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('show_criteria_weights', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('show_alternatives_score', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('prioritization_method', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'forced ranking',
              1 => 'five point scale',
              2 => 'ten point scale',
              3 => 'pairwise comparison',
             ),
             'default' => 'five point scale',
             ));
        $this->hasColumn('view_matrix', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('updateable', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('anonymous', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('show_comments', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('collect_items', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('display_items', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('allow_voting', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('dashboard', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('comment', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('token', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('continue_url', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             ));
        $this->hasColumn('language', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'en',
              1 => 'da',
             ),
             'default' => 'en',
             ));
        $this->hasColumn('active', 'boolean', null, array(
             'type' => 'boolean',
             ));

        $this->option('symfony', array(
             'filter' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('UploadedFile as Files', array(
             'refClass' => 'RoleUploadedFile',
             'local' => 'role_id',
             'foreign' => 'uploaded_file_id'));

        $this->hasOne('Decision', array(
             'local' => 'decision_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('RoleFilter', array(
             'local' => 'id',
             'foreign' => 'role_id'));

        $this->hasMany('Response', array(
             'local' => 'id',
             'foreign' => 'role_id'));

        $this->hasMany('RoleUploadedFile', array(
             'local' => 'id',
             'foreign' => 'role_id'));

        $this->hasMany('PlannedAlternativeMeasurement', array(
             'local' => 'id',
             'foreign' => 'role_id'));

        $this->hasMany('PlannedCriterionPrioritization as PlannedCriteria', array(
             'local' => 'id',
             'foreign' => 'role_id'));

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