<?php

/**
 * Scripts form base class.
 *
 * @method Scripts getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseScriptsForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'backend_top'     => new sfWidgetFormInputText(),
      'backend_bottom'  => new sfWidgetFormInputText(),
      'frontend_top'    => new sfWidgetFormInputText(),
      'frontend_bottom' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'backend_top'     => new sfValidatorPass(array('required' => false)),
      'backend_bottom'  => new sfValidatorPass(array('required' => false)),
      'frontend_top'    => new sfValidatorPass(array('required' => false)),
      'frontend_bottom' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('scripts[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Scripts';
  }

}
