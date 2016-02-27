<?php

class UserProfileForm extends BasesfGuardUserForm
{
  protected $formatterName = 'bootstrap_horizontal';

  public function configure()
  {
    $this->widgetSchema['country']          = new sfWidgetFormI18nChoiceCountry(array('add_empty' => true));
    $this->validatorSchema['email_address'] = new sfValidatorEmail();
    $this->widgetSchema['password']         = new sfWidgetFormInputPassword();
    $this->validatorSchema['password']->setOption('required', false);
    $this->widgetSchema['password_again']    = new sfWidgetFormInputPassword();
    $this->validatorSchema['password_again'] = clone $this->validatorSchema['password'];
    $this->validatorSchema['country']        = new sfValidatorI18nChoiceCountry(array( 'required' => false ));

    $this->mergePostValidator(
      new sfValidatorSchemaCompare('password', sfValidatorSchemaCompare::EQUAL, 'password_again', array(), array( 'invalid' => 'The two passwords must be the same.' ))
    );

    $this->useFields(array(
      'first_name',
      'last_name',
      'country',
      'email_address',
      'password',
      'password_again'
    ));

    foreach ($this->widgetSchema->getFields() as $widget) {
      if (in_array($widget->getOption('type'), array( 'text', 'password' )) || $widget instanceof sfWidgetFormChoiceBase) {
        $widget->setAttribute('class', 'form-control');
      }
    }
  }

  public function processValues($values)
  {
    $values = parent::processValues($values);
    if (!sfContext::getInstance()->getUser()->hasCredential('admin')) {
      $values['username'] = $values['email_address'];
    }

    return $values;
  }
}