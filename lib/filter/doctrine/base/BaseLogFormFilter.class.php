<?php

/**
 * Log filter form base class.
 *
 * @package    dmp
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseLogFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'user_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'action'      => new sfWidgetFormChoice(array('choices' => array('' => '', 'login' => 'login', 'project_create' => 'project_create', 'folder_create' => 'folder_create', 'item_create' => 'item_create', 'item_update' => 'item_update', 'criteria_create' => 'criteria_create', 'criteria_update' => 'criteria_update', 'survey_create' => 'survey_create', 'survey_update' => 'survey_update', 'survey_answered' => 'survey_answered', 'budget_create' => 'budget_create', 'budget_update' => 'budget_update', 'release_create' => 'release_create', 'release_update' => 'release_update', 'wall_update' => 'wall_update', 'wall_visit' => 'wall_visit', 'roadmap_create' => 'roadmap_create', 'roadmap_update' => 'roadmap_update'))),
      'information' => new sfWidgetFormFilterInput(),
      'created_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'user_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'action'      => new sfValidatorChoice(array('required' => false, 'choices' => array('login' => 'login', 'project_create' => 'project_create', 'folder_create' => 'folder_create', 'item_create' => 'item_create', 'item_update' => 'item_update', 'criteria_create' => 'criteria_create', 'criteria_update' => 'criteria_update', 'survey_create' => 'survey_create', 'survey_update' => 'survey_update', 'survey_answered' => 'survey_answered', 'budget_create' => 'budget_create', 'budget_update' => 'budget_update', 'release_create' => 'release_create', 'release_update' => 'release_update', 'wall_update' => 'wall_update', 'wall_visit' => 'wall_visit', 'roadmap_create' => 'roadmap_create', 'roadmap_update' => 'roadmap_update'))),
      'information' => new sfValidatorPass(array('required' => false)),
      'created_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('log_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Log';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'user_id'     => 'ForeignKey',
      'action'      => 'Enum',
      'information' => 'Text',
      'created_at'  => 'Date',
    );
  }
}
