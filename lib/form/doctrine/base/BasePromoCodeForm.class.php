<?php

/**
 * PromoCode form base class.
 *
 * @method PromoCode getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePromoCodeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'code'         => new sfWidgetFormInputText(),
      'account_type' => new sfWidgetFormChoice(array('choices' => array('Trial' => 'Trial', 'Light' => 'Light', 'Basic' => 'Basic', 'Pro' => 'Pro', 'Enterprise' => 'Enterprise'))),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'code'         => new sfValidatorString(array('max_length' => 128)),
      'account_type' => new sfValidatorChoice(array('choices' => array(0 => 'Trial', 1 => 'Light', 2 => 'Basic', 3 => 'Pro', 4 => 'Enterprise'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('promo_code[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PromoCode';
  }

}
