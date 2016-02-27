<?php

class MeasurementStartForm extends BaseForm
{
  private $role_id;

  public function configure()
  {
    $this->widgetSchema['email_address'] = new sfWidgetFormInputText(array('label' => 'Please enter your email address'));
    $this->validatorSchema['email_address'] = new sfValidatorEmail();
    $this->widgetSchema->setNameFormat('measurement_start[%s]');
  }

  public function setRoleId($id)
  {
    $this->role_id = $id;
  }

  public function save()
  {
    sfContext::getInstance()
      ->getUser()
      ->setAttribute('email_address', $this->values['email_address'], 'measurement/email/' . $this->role_id);
  }
}
