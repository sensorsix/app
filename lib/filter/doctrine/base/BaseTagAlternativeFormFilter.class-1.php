<?php

/**
 * TagAlternative filter form base class.
 *
 * @package    dmp
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTagAlternativeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'tag_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Tag'), 'add_empty' => true)),
      'alternative_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Alternative'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'tag_id'         => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Tag'), 'column' => 'id')),
      'alternative_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Alternative'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('tag_alternative_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TagAlternative';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'tag_id'         => 'ForeignKey',
      'alternative_id' => 'ForeignKey',
    );
  }
}
