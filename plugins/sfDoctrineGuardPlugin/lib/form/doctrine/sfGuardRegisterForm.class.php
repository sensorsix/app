<?php

/**
 * sfGuardRegisterForm for registering new users
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: BasesfGuardChangeUserPasswordForm.class.php 23536 2009-11-02 21:41:21Z Kris.Wallsmith $
 */
class sfGuardRegisterForm extends BasesfGuardRegisterForm
{
  protected $formatterName = 'bootstrap_horizontal';

  /**
   * @see sfForm
   */
  public function configure()
  {
    $this->validatorSchema['email_address']       = new sfValidatorEmail();
    foreach ($this->widgetSchema->getFields() as $widget)
    {
      if (in_array($widget->getOption('type'), array( 'text', 'password' )))
      {
        $widget->setAttribute('class', 'form-control');
      }
    }
    $this->validatorSchema->setOption('allow_extra_fields', true);

    // Drop validation of the parents
    $this->validatorSchema->setPostValidator(new sfValidatorPass());
    $this->widgetSchema->offsetUnset('password_again');

    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'verifyEmailDomain'))));
    $this->mergePostValidator(new sfValidatorCallback(array('callback' => array($this, 'verifyUnique'))));

    $this->useFields(
      array(
        'email_address',
        'password',
      )
    );
  }

  public function verifyEmailDomain($validator, $values)
  {
    $email = $values['email_address'];
    $domain = strtolower(substr($email, strpos($email, '@') + 1));

    if (DomainTable::getInstance()->findOneBy('name', $domain))
    {
      $error = new sfValidatorError($validator, 'That looks like a personal email address. Please use your company email.');
      throw new sfValidatorErrorSchema($validator, array( 'email_address' => $error ));
    }

    return $values;
  }

  public function verifyUnique($validator, $values)
  {
    if (sfGuardUserTable::getInstance()->findOneBy('email_address', $values['email_address'])) {
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('Url'));
      $error = new sfValidatorError($validator, 'You are already registered, maybe you <a href="'.url_for('@sf_guard_forgot_password').'">Forgot the password</a>?');
      throw new sfValidatorErrorSchema($validator, array( 'email_address' => $error ));
    }

    return $values;
  }

  public function processValues($values)
  {
    $values             = parent::processValues($values);
    $values['username'] = $values['email_address'];

    $this->object->account_type = 'Pro';
    $this->object->is_active = false;
    $this->object->wizard = true;
    $this->object->link('Permissions', array( sfGuardPermission::DECISION_MANAGEMENT ));

    return $values;
  }
}