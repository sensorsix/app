<?php

/**
 * sfGuardUser form base class.
 *
 * @method sfGuardUser getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasesfGuardUserForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'first_name'        => new sfWidgetFormInputText(),
      'last_name'         => new sfWidgetFormInputText(),
      'email_address'     => new sfWidgetFormInputText(),
      'country'           => new sfWidgetFormInputText(),
      'username'          => new sfWidgetFormInputText(),
      'algorithm'         => new sfWidgetFormInputText(),
      'salt'              => new sfWidgetFormInputText(),
      'password'          => new sfWidgetFormInputText(),
      'is_active'         => new sfWidgetFormInputCheckbox(),
      'is_super_admin'    => new sfWidgetFormInputCheckbox(),
      'biography'         => new sfWidgetFormTextarea(),
      'last_login'        => new sfWidgetFormDateTime(),
      'last_payment_date' => new sfWidgetFormDateTime(),
      'account_type'      => new sfWidgetFormChoice(array('choices' => array('Trial' => 'Trial', 'Light' => 'Light', 'Basic' => 'Basic', 'Pro' => 'Pro', 'Enterprise' => 'Enterprise'))),
      'credit'            => new sfWidgetFormInputText(),
      'logo_file'         => new sfWidgetFormInputText(),
      'logo_url'          => new sfWidgetFormInputText(),
      'header_color'      => new sfWidgetFormInputText(),
      'wizard'            => new sfWidgetFormInputCheckbox(),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
      'groups_list'       => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardGroup')),
      'permissions_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardPermission')),
      'types_list'        => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'DecisionType')),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'first_name'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'last_name'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'email_address'     => new sfValidatorString(array('max_length' => 255)),
      'country'           => new sfValidatorString(array('max_length' => 2, 'required' => false)),
      'username'          => new sfValidatorString(array('max_length' => 128)),
      'algorithm'         => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'salt'              => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'password'          => new sfValidatorString(array('max_length' => 128, 'required' => false)),
      'is_active'         => new sfValidatorBoolean(array('required' => false)),
      'is_super_admin'    => new sfValidatorBoolean(array('required' => false)),
      'biography'         => new sfValidatorString(array('required' => false)),
      'last_login'        => new sfValidatorDateTime(array('required' => false)),
      'last_payment_date' => new sfValidatorDateTime(array('required' => false)),
      'account_type'      => new sfValidatorChoice(array('choices' => array(0 => 'Trial', 1 => 'Light', 2 => 'Basic', 3 => 'Pro', 4 => 'Enterprise'), 'required' => false)),
      'credit'            => new sfValidatorInteger(array('required' => false)),
      'logo_file'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'logo_url'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'header_color'      => new sfValidatorString(array('max_length' => 24, 'required' => false)),
      'wizard'            => new sfValidatorBoolean(array('required' => false)),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
      'groups_list'       => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardGroup', 'required' => false)),
      'permissions_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'sfGuardPermission', 'required' => false)),
      'types_list'        => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'DecisionType', 'required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorAnd(array(
        new sfValidatorDoctrineUnique(array('model' => 'sfGuardUser', 'column' => array('email_address'))),
        new sfValidatorDoctrineUnique(array('model' => 'sfGuardUser', 'column' => array('username'))),
      ))
    );

    $this->widgetSchema->setNameFormat('sf_guard_user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'sfGuardUser';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['groups_list']))
    {
      $this->setDefault('groups_list', $this->object->Groups->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['permissions_list']))
    {
      $this->setDefault('permissions_list', $this->object->Permissions->getPrimaryKeys());
    }

    if (isset($this->widgetSchema['types_list']))
    {
      $this->setDefault('types_list', $this->object->Types->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveGroupsList($con);
    $this->savePermissionsList($con);
    $this->saveTypesList($con);

    parent::doSave($con);
  }

  public function saveGroupsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['groups_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Groups->getPrimaryKeys();
    $values = $this->getValue('groups_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Groups', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Groups', array_values($link));
    }
  }

  public function savePermissionsList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['permissions_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Permissions->getPrimaryKeys();
    $values = $this->getValue('permissions_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Permissions', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Permissions', array_values($link));
    }
  }

  public function saveTypesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['types_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Types->getPrimaryKeys();
    $values = $this->getValue('types_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Types', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Types', array_values($link));
    }
  }

}
