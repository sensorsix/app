<?php

/**
 * Roadmap form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class RoadmapForm extends BaseRoadmapForm
{
  public function configure()
  {
    unset($this['folder_id'], $this['user_id'], $this['token']);

    $this->widgetSchema['active'] = new sfWidgetFormInputCheckboxExtended();
    $this->widgetSchema['show_items'] = new sfWidgetFormInputCheckboxExtended();
    $this->widgetSchema['show_releases'] = new sfWidgetFormInputCheckboxExtended();
    $this->widgetSchema['show_dependencies'] = new sfWidgetFormInputCheckboxExtended();
    $this->widgetSchema['show_description'] = new sfWidgetFormInputCheckboxExtended();


    $this->widgetSchema['workspace_mode'] = new sfWidgetFormSelectRadio(array('choices' => array('timeline' => 'Timeline view', 'list' => 'List view')));

    $this->validatorSchema['active']->setOption('true_values', array('checked'));
    $this->validatorSchema['show_items']->setOption('true_values', array('checked'));
    $this->validatorSchema['show_releases']->setOption('true_values', array('checked'));
    $this->validatorSchema['show_dependencies']->setOption('true_values', array('checked'));
    $this->validatorSchema['show_description']->setOption('true_values', array('checked'));

    $this->widgetSchema['description'] = new laWidgetFormCKEditor(array('config' => array('height' => '75px')));
    $this->disableCSRFProtection();
  }
}
