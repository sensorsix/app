<?php

/**
 * tagDecision filter form base class.
 *
 * @package    dmp
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasetagDecisionFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'tag_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Tag'), 'add_empty' => true)),
      'decision_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Decision'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'tag_id'      => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Tag'), 'column' => 'id')),
      'decision_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Decision'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('tag_decision_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'tagDecision';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'tag_id'      => 'ForeignKey',
      'decision_id' => 'ForeignKey',
    );
  }
}
