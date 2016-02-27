<?php

/**
 * Criterion form base class.
 *
 * @method Criterion getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCriterionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'decision_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Decision'), 'add_empty' => false)),
      'name'          => new sfWidgetFormInputText(),
      'description'   => new sfWidgetFormTextarea(),
      'measurement'   => new sfWidgetFormChoice(array('choices' => array('direct rating' => 'direct rating', 'direct float' => 'direct float', 'forced ranking' => 'forced ranking', 'five point scale' => 'five point scale', 'ten point scale' => 'ten point scale', 'comment' => 'comment'))),
      'variable_type' => new sfWidgetFormChoice(array('choices' => array('Benefit' => 'Benefit', 'Cost' => 'Cost', 'Info' => 'Info'))),
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
      'root_id'       => new sfWidgetFormInputText(),
      'lft'           => new sfWidgetFormInputText(),
      'rgt'           => new sfWidgetFormInputText(),
      'level'         => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'decision_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Decision'))),
      'name'          => new sfValidatorString(array('max_length' => 255)),
      'description'   => new sfValidatorString(array('required' => false)),
      'measurement'   => new sfValidatorChoice(array('choices' => array(0 => 'direct rating', 1 => 'direct float', 2 => 'forced ranking', 3 => 'five point scale', 4 => 'ten point scale', 5 => 'comment'), 'required' => false)),
      'variable_type' => new sfValidatorChoice(array('choices' => array(0 => 'Benefit', 1 => 'Cost', 2 => 'Info'), 'required' => false)),
      'created_at'    => new sfValidatorDateTime(),
      'updated_at'    => new sfValidatorDateTime(),
      'root_id'       => new sfValidatorInteger(array('required' => false)),
      'lft'           => new sfValidatorInteger(array('required' => false)),
      'rgt'           => new sfValidatorInteger(array('required' => false)),
      'level'         => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('criterion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Criterion';
  }

}
