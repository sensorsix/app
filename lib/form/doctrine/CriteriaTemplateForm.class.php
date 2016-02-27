<?php

/**
 * CriteriaTemplate form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CriteriaTemplateForm extends BaseCriteriaTemplateForm
{
  public function configure()
  {
    unset($this['template_id']);

    $this->widgetSchema->setLabel('variable_type', 'Type');

    /** @var sfWidget $widget */
    foreach ($this->widgetSchema->getFields() as $widget)
    {
      $widget->setAttribute('class', 'form-control c-autosave');
    }

    $this->disableCSRFProtection();
  }
}
