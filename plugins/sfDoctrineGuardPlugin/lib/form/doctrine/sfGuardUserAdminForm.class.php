<?php

/**
 * sfGuardUserAdminForm for admin generators
 *
 * @package    sfDoctrineGuardPlugin
 * @subpackage form
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGuardUserAdminForm.class.php 23536 2009-11-02 21:41:21Z Kris.Wallsmith $
 */
class sfGuardUserAdminForm extends BasesfGuardUserAdminForm
{
  /**
   * @see sfForm
   */
  public function configure()
  {
    $bootstrap_decorator              = new sfWidgetFormSchemaFormatterBootstrap($this->getWidgetSchema(), $this->getValidatorSchema());
    $bootstrap_horizontal             = new sfWidgetFormSchemaFormatterBootstrapHorizontal($this->getWidgetSchema(), $this->getValidatorSchema());
    $bootstrap_horizontal_short_label = new sfWidgetFormSchemaFormatterBootstrapHorizontalShortLabel($this->getWidgetSchema(), $this->getValidatorSchema());
    $this->getWidgetSchema()->addFormFormatter('bootstrap', $bootstrap_decorator);
    $this->getWidgetSchema()->addFormFormatter('bootstrap_horizontal', $bootstrap_horizontal);
    $this->getWidgetSchema()->addFormFormatter('bootstrap_horizontal_shortlabel', $bootstrap_horizontal_short_label);
    $this->setFormatter();

    unset($this['permissions_list']);
    $this->widgetSchema['is_admin'] = new sfWidgetFormInputCheckbox();
    $this->setDefault('is_admin', $this->getObject()->hasPermission('admin'));
    $this->widgetSchema['country'] = new sfWidgetFormI18nChoiceCountry();
    $this->widgetSchema['last_payment_date'] = new laWidgetFormBootstrapDatepicker();
    $this->widgetSchema['biography'] = new laWidgetFormCKEditor();

    $this->validatorSchema['is_admin'] = new sfValidatorBoolean(array('required' => false));
    $this->validatorSchema['country'] = new sfValidatorI18nChoiceCountry(array('required' => false));
    $this->validatorSchema->setOption('allow_extra_fields', true);
    $this->validatorSchema['email_address'] = new sfValidatorEmail();
    $this->widgetSchema['types_list'] = new sfWidgetFormDoctrineChoice(array('multiple' => true, 'renderer_options' => array('label_separator' => ''), 'expanded' => true, 'model' => 'DecisionType'));
  }

  public function processValues($values)
  {
    $values = parent::processValues($values);

    if ($values['password'] === '' && $values['password_again'] === '')
    {
      $values['password'] = false;
    }

    $this->object->link('Permissions', array(sfGuardPermission::DECISION_MANAGEMENT));

    if (isset($values['is_admin']) && $values['is_admin'])
    {
      $this->object->link('Permissions', array(sfGuardPermission::ADMINISTRATION, sfGuardPermission::DECISION_MANAGEMENT));
    }
    else
    {
      $this->object->unlink('Permissions', array(sfGuardPermission::ADMINISTRATION));
    }

    return $values;
  }
}
