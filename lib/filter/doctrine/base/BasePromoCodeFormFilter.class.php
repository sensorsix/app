<?php

/**
 * PromoCode filter form base class.
 *
 * @package    dmp
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePromoCodeFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'code'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'account_type' => new sfWidgetFormChoice(array('choices' => array('' => '', 'Trial' => 'Trial', 'Light' => 'Light', 'Basic' => 'Basic', 'Pro' => 'Pro', 'Enterprise' => 'Enterprise'))),
    ));

    $this->setValidators(array(
      'code'         => new sfValidatorPass(array('required' => false)),
      'account_type' => new sfValidatorChoice(array('required' => false, 'choices' => array('Trial' => 'Trial', 'Light' => 'Light', 'Basic' => 'Basic', 'Pro' => 'Pro', 'Enterprise' => 'Enterprise'))),
    ));

    $this->widgetSchema->setNameFormat('promo_code_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PromoCode';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'code'         => 'Text',
      'account_type' => 'Enum',
    );
  }
}
