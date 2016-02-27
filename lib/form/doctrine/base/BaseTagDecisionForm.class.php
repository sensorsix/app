<?php

/**
 * TagDecision form base class.
 *
 * @method TagDecision getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTagDecisionForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'tag_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Tag'), 'add_empty' => true)),
      'decision_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Decision'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'tag_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Tag'), 'required' => false)),
      'decision_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Decision'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tag_decision[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TagDecision';
  }

}
