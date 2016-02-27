<?php

/**
 * TypeTemplate form base class.
 *
 * @method TypeTemplate getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTypeTemplateForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                       => new sfWidgetFormInputHidden(),
      'name'                     => new sfWidgetFormInputText(),
      'type_id'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Type'), 'add_empty' => false)),
      'alternative_alias'        => new sfWidgetFormInputText(),
      'alternative_plural_alias' => new sfWidgetFormInputText(),
      'partitioner_alias'        => new sfWidgetFormInputText(),
      'user_id'                  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'id'                       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'                     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'type_id'                  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Type'))),
      'alternative_alias'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'alternative_plural_alias' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'partitioner_alias'        => new sfValidatorString(array('max_length' => 64, 'required' => false)),
      'user_id'                  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('type_template[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TypeTemplate';
  }

}
