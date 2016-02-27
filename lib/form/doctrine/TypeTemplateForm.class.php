<?php

/**
 * TypeTemplate form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TypeTemplateForm extends BaseTypeTemplateForm
{
  public function configure()
  {
    unset($this['user_id']);

    /** @var sfWidget $widget */
    foreach ($this->widgetSchema->getFields() as $widget)
    {
      $widget->setAttribute('class', 'form-control autosave');
    }

    $this->disableCSRFProtection();
  }
}
