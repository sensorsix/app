<?php

/**
 * LogicalFilter form base class.
 *
 * @method LogicalFilter getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseLogicalFilterForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'decision_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Decision'), 'add_empty' => false)),
      'criterion_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Criterion'), 'add_empty' => false)),
      'logical_operator' => new sfWidgetFormChoice(array('choices' => array('>' => '>', '<' => '<', '=' => '='))),
      'value'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'decision_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Decision'))),
      'criterion_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Criterion'))),
      'logical_operator' => new sfValidatorChoice(array('choices' => array(0 => '>', 1 => '<', 2 => '='))),
      'value'            => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('logical_filter[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'LogicalFilter';
  }

}
