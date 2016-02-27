<?php

/**
 * Decision form base class.
 *
 * @method Decision getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseDecisionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'user_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'name'              => new sfWidgetFormInputText(),
      'objective'         => new sfWidgetFormTextarea(),
      'type_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Type'), 'add_empty' => false)),
      'template_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Template'), 'add_empty' => false)),
      'folder_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Folder'), 'add_empty' => true)),
      'assigned_to'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AssignedTo'), 'add_empty' => true)),
      'start_date'        => new sfWidgetFormInputText(),
      'end_date'          => new sfWidgetFormInputText(),
      'color'             => new sfWidgetFormInputText(),
      'status'            => new sfWidgetFormChoice(array('choices' => array('Planned' => 'Planned', 'In Progress' => 'In Progress', 'Done' => 'Done'))),
      'external_id'       => new sfWidgetFormInputText(),
      'save_graph_weight' => new sfWidgetFormInputCheckbox(),
      'root_id'           => new sfWidgetFormInputText(),
      'lft'               => new sfWidgetFormInputText(),
      'rgt'               => new sfWidgetFormInputText(),
      'level'             => new sfWidgetFormInputText(),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_id'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'required' => false)),
      'name'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'objective'         => new sfValidatorString(array('required' => false)),
      'type_id'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Type'), 'required' => false)),
      'template_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Template'))),
      'folder_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Folder'), 'required' => false)),
      'assigned_to'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('AssignedTo'), 'required' => false)),
      'start_date'        => new sfValidatorPass(array('required' => false)),
      'end_date'          => new sfValidatorPass(array('required' => false)),
      'color'             => new sfValidatorString(array('max_length' => 8, 'required' => false)),
      'status'            => new sfValidatorChoice(array('choices' => array(0 => 'Planned', 1 => 'In Progress', 2 => 'Done'), 'required' => false)),
      'external_id'       => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'save_graph_weight' => new sfValidatorBoolean(array('required' => false)),
      'root_id'           => new sfValidatorInteger(array('required' => false)),
      'lft'               => new sfValidatorInteger(array('required' => false)),
      'rgt'               => new sfValidatorInteger(array('required' => false)),
      'level'             => new sfValidatorInteger(array('required' => false)),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('decision[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Decision';
  }

}
