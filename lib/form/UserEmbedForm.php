<?php
 
class UserEmbedForm extends BasesfGuardUserAdminForm
{
  public function configure()
  {
    if ($this->isNew()) {
      $this->validatorSchema['password']->setOption('required', true);
    }

    $this->validatorSchema['email_address'] = new sfValidatorEmail();
    $this->widgetSchema['username'] = new laWidgetFormText(array(
      'model'  => 'sfGuardUser',
      'object' => $this->getObject(),
      'method' => 'getUsername',
      'content_tag' => 'span',
      'label'  => false
    ));
    $this->validatorSchema['username']->setOption('required', false);
    $this->useFields(array(
      'username',
      'email_address',
      'password',
      'password_again'
    ));
  }

  public function processValues($values)
  {
    $values = parent::processValues($values);
    $values['username'] = $values['email_address'];
    if ($this->isNew()) {
      $values['account_type'] = 'Pro';
      $values['is_active'] = true;
    }

    $this->object->link('Permissions', array(sfGuardPermission::DECISION_MANAGEMENT));

    return $values;
  }
}
 