<?php

/**
 * TagRelease form base class.
 *
 * @method TagRelease getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTagReleaseForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'tag_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Tag'), 'add_empty' => true)),
      'release_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProjectRelease'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'tag_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Tag'), 'required' => false)),
      'release_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ProjectRelease'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tag_release[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TagRelease';
  }

}
