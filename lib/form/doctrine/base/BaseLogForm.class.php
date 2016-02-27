<?php

/**
 * Log form base class.
 *
 * @method Log getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLogForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'user_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'action'      => new sfWidgetFormChoice(array('choices' => array('login' => 'login', 'project_create' => 'project_create', 'folder_create' => 'folder_create', 'item_create' => 'item_create', 'item_update' => 'item_update', 'criteria_create' => 'criteria_create', 'criteria_update' => 'criteria_update', 'survey_create' => 'survey_create', 'survey_update' => 'survey_update', 'survey_answered' => 'survey_answered', 'budget_create' => 'budget_create', 'budget_update' => 'budget_update', 'release_create' => 'release_create', 'release_update' => 'release_update', 'wall_update' => 'wall_update', 'wall_visit' => 'wall_visit', 'roadmap_create' => 'roadmap_create', 'roadmap_update' => 'roadmap_update'))),
      'information' => new sfWidgetFormInputText(),
      'created_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'required' => false)),
      'action'      => new sfValidatorChoice(array('choices' => array(0 => 'login', 1 => 'project_create', 2 => 'folder_create', 3 => 'item_create', 4 => 'item_update', 5 => 'criteria_create', 6 => 'criteria_update', 7 => 'survey_create', 8 => 'survey_update', 9 => 'survey_answered', 10 => 'budget_create', 11 => 'budget_update', 12 => 'release_create', 13 => 'release_update', 14 => 'wall_update', 15 => 'wall_visit', 16 => 'roadmap_create', 17 => 'roadmap_update'), 'required' => false)),
      'information' => new sfValidatorPass(array('required' => false)),
      'created_at'  => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('log[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Log';
  }

}
