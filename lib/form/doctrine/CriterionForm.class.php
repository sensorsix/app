<?php

/**
 * Criterion form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CriterionForm extends BaseCriterionForm
{
  protected $formatterName = 'bootstrap_horizontal_shortlabel';
  public function configure()
  {
    unset($this['lft'], $this['rgt'], $this['level'], $this['root_id'], $this['decision_id'], $this['created_at'], $this['updated_at']);

    $choices = array('direct rating' => 'Direct Measure', 'direct float' => 'Direct (float)');
    $choices = array_merge($choices, sfConfig::get('app_rating_method'));
    $choices = array_merge($choices, array('comment' => 'Comment'));

    $this->widgetSchema['measurement'] = new sfWidgetFormChoice(array('choices' => $choices));
    $this->widgetSchema['description'] = new laWidgetFormCKEditor(array('config' => array('height' => '75px')));

    /** @var sfWidget $widget */
    foreach ($this->widgetSchema->getFields() as $widget)
    {
      $widget->setAttribute('class', 'form-control autosave');
    }

    $this->disableCSRFProtection();
  }
}
