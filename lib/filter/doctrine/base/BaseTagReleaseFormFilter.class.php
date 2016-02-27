<?php

/**
 * TagRelease filter form base class.
 *
 * @package    dmp
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTagReleaseFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'tag_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Tag'), 'add_empty' => true)),
      'release_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProjectRelease'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'tag_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Tag'), 'column' => 'id')),
      'release_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ProjectRelease'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('tag_release_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TagRelease';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'tag_id'     => 'ForeignKey',
      'release_id' => 'ForeignKey',
    );
  }
}
