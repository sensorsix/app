<?php

/**
 * ProjectRelease form base class.
 *
 * @method ProjectRelease getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProjectReleaseForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'decision_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Decision'), 'add_empty' => false)),
      'criterion_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Criterion'), 'add_empty' => true)),
      'name'         => new sfWidgetFormInputText(),
      'status'       => new sfWidgetFormChoice(array('choices' => array('Draft' => 'Draft', 'Reviewed' => 'Reviewed', 'Planned' => 'Planned', 'Doing' => 'Doing', 'Finished' => 'Finished', 'Parked' => 'Parked'))),
      'start_date'   => new sfWidgetFormDateTime(),
      'end_date'     => new sfWidgetFormDateTime(),
      'value'        => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'decision_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Decision'))),
      'criterion_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Criterion'), 'required' => false)),
      'name'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'status'       => new sfValidatorChoice(array('choices' => array(0 => 'Draft', 1 => 'Reviewed', 2 => 'Planned', 3 => 'Doing', 4 => 'Finished', 5 => 'Parked'), 'required' => false)),
      'start_date'   => new sfValidatorDateTime(array('required' => false)),
      'end_date'     => new sfValidatorDateTime(array('required' => false)),
      'value'        => new sfValidatorNumber(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('project_release[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProjectRelease';
  }

}
