<?php

/**
 * ProjectRelease form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ProjectReleaseForm extends BaseProjectReleaseForm
{
  protected $formatterName = 'bootstrap_horizontal_shortlabel';

  public function configure()
  {
    unset($this['decision_id'], $this['criterion_id']);

    /** @var sfWidget $widget */
    foreach ($this->widgetSchema->getFields() as $widget) {
      $widget->setAttribute('class', 'form-control');
    }

    /* Get all tags and create input field */
    $tags = array();
    foreach($this->getObject()->getTagRelease() as $tag) {
      $tags[] = $tag->Tag->name;
    }
    $this->widgetSchema['tags'] = new sfWidgetFormInputText(array(), array('value' => implode(',', $tags), 'class'=>'tags_input', 'data-role' => 'tagsinput'));

    $start_date_native = $this->getObject()->getStartDate();
    $end_date_native = $this->getObject()->getEndDate();
    $start_date = new DateTime($this->getObject()->getStartDate());
    $end_date = new DateTime($this->getObject()->getEndDate());
    $this->widgetSchema['start_date'] = new sfWidgetFormInputText(array(), array('value' => '', 'data-value' => (!empty($start_date_native) && $start_date_native !== '0000-00-00 00:00:00') ? $start_date->format('Y/m/j') : ''));
    $this->widgetSchema['end_date'] = new sfWidgetFormInputText(array(), array('value' => '', 'data-value' => (!empty($end_date_native) && $end_date_native !== '0000-00-00 00:00:00') ? $end_date->format('Y/m/j') : ''));

    $this->disableCSRFProtection();
  }
}
