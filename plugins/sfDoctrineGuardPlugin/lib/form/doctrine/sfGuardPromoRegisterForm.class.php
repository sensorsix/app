<?php
 
class sfGuardPromoRegisterForm extends BasesfGuardRegisterForm
{
  public function configure()
  {
    $this->validatorSchema['email_address'] = new sfValidatorEmail();
    $this->validatorSchema['email_address']->setMessage('required', 'Email is required');
    $this->validatorSchema['email_address']->setMessage('invalid', 'Invalid');
    $this->validatorSchema['password']->setMessage('required', 'Password is required');
    $this->validatorSchema['password']->setMessage('invalid', 'Invalid');

    $this->widgetSchema['promo_code'] = new sfWidgetFormInputText();
    $this->validatorSchema['promo_code'] = new sfValidatorString();
    $this->validatorSchema['promo_code']->setMessage('required', 'Promotion code is required');


    $this->validatorSchema->setPostValidator(new sfValidatorCallBack(array('callback' => array($this, 'postValidateForm'))));

    $this->useFields(
      array(
        'email_address',
        'password',
        'promo_code'
      )
    );
  }

  public function postValidateForm($validator, $values)
  {
    if (isset($values['promo_code'])){
      $promo_code = PromoCodeTable::getInstance()->findOneByCode($values['promo_code']);
      if (!$promo_code){
        throw new sfValidatorError($validator, 'Promotion Code is invalid');
      }else{
        $values['account_type'] = $promo_code->account_type;
      }
    }

    /** @var sfGuardUser $user */
    $user = sfGuardUserTable::getInstance()
      ->createQuery('u')
      ->where('u.email_address = ?', $values['email_address'])
      ->fetchOne();

    if ($user && $values['password'])
    {
      if ($user->getIsActive() && $user->checkPassword($values['password']))
      {
        sfContext::getInstance()->getUser()->signIn($user);
        sfContext::getInstance()->getController()->redirect('/project');
      }
      else
      {
        throw new sfValidatorError($validator, 'The email and/or password is invalid');
      }
    }

    $email = $values['email_address'];
    $domain = strtolower(substr($email, strpos($email, '@') + 1));

    if (DomainTable::getInstance()->findOneBy('name', $domain))
    {
      $error = new sfValidatorError($validator, 'That looks like a personal email address. Please use your company email.');
      throw new sfValidatorErrorSchema($validator, array( 'email_address' => $error ));
    }

    return $values;
  }

  public function processValues($values)
  {
    if (isset($values['is_admin']) && $values['is_admin'])
    {
      $this->object->link('Permissions', array(sfGuardPermission::ADMINISTRATION));
    }
    else
    {
      $this->object->unlink('Permissions', array(sfGuardPermission::ADMINISTRATION));
    }

    $values['username'] = $values['email_address'];
    if ($values['account_type'] !== "Light") {
      $demoData           = new DemoData(sfConfig::get('sf_data_dir') . '/demo.yml');
      $demoData->load($this->object);
    }

    $this->object->wizard = true;
    $this->object->link('Permissions', array(sfGuardPermission::DECISION_MANAGEMENT));

    return $values;
  }
}