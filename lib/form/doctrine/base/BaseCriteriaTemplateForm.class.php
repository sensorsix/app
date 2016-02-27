<?php

/**
 * CriteriaTemplate form base class.
 *
 * @method CriteriaTemplate getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseCriteriaTemplateForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'name'          => new sfWidgetFormInputText(),
      'template_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Template'), 'add_empty' => false)),
      'measurement'   => new sfWidgetFormChoice(array('choices' => array('direct rating' => 'direct rating', 'direct float' => 'direct float', 'forced ranking' => 'forced ranking', 'five point scale' => 'five point scale', 'ten point scale' => 'ten point scale'))),
      'variable_type' => new sfWidgetFormChoice(array('choices' => array('Benefit' => 'Benefit', 'Cost' => 'Cost', 'Info' => 'Info'))),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'template_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Template'))),
      'measurement'   => new sfValidatorChoice(array('choices' => array(0 => 'direct rating', 1 => 'direct float', 2 => 'forced ranking', 3 => 'five point scale', 4 => 'ten point scale'), 'required' => false)),
      'variable_type' => new sfValidatorChoice(array('choices' => array(0 => 'Benefit', 1 => 'Cost', 2 => 'Info'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('criteria_template[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'CriteriaTemplate';
  }

}
