<?php

/**
 * Alternative form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class AlternativeForm extends BaseAlternativeForm
{
  protected $formatterName = 'bootstrap_horizontal_shortlabel';

  public function configure()
  {
    unset($this['lft'], $this['rgt'], $this['level'], $this['root_id'], $this['decision_id'], $this['files_list'], $this['created_at'], $this['updated_at'], $this['created_by'], $this['updated_by'], $this['type_id'], $this['item_id']);

    $this->widgetSchema['notes'] = $this->widgetSchema['additional_info'] = new laWidgetFormCKEditor(array('config' => array('height' => '75px')));
    $this->widgetSchema['upload'] = new laWidgetFileUpload();

    $this->widgetSchema['work_progress'] = new sfWidgetFormInputRange(array('min' => 0, 'max' => 100));

    /** @var sfWidget $widget */
    foreach ($this->widgetSchema->getFields() as $widget) {
      $widget->setAttribute('class', 'form-control');
    }

    /* Get all tags and create input field */
    $tags = array();
    foreach($this->getObject()->getTagAlternative() as $tag) {
      $tags[] = $tag->Tag->name;
    }
    $this->widgetSchema['tags'] = new sfWidgetFormInputText(array(), array('value' => implode(',', $tags), 'class'=>'tags_input', 'data-role' => 'tagsinput'));

    /* Create field with relations */
    $related_alternatives_choices = array();
    if ($this->getOption('user')){
      foreach(AlternativeTable::getInstance()->getListForUser($this->getOption('user')->getGuardUser()) as $alternative) {
        $related_alternatives_choices[$alternative->getId()] = $alternative->getName() . ' (' . $alternative->getDecision()->getName() . ')';
      }
    }
    unset($related_alternatives_choices[$this->getObject()->getId()]);
    $related_alternatives_default = array();
    foreach ($this->getObject()->getAlternativeRelation() as $related_alternative) {
      $related_alternatives_default[] = $related_alternative->to_id;
    }
    $this->widgetSchema['related_alternatives'] = new sfWidgetFormSelectMany(array('choices' => $related_alternatives_choices));
    $this->widgetSchema['related_alternatives']->setDefault($related_alternatives_default);

    $notify_date_native = $this->getObject()->getNotifyDate();
    $due_date_native = $this->getObject()->getDueDate();
    $due_data = new DateTime($this->getObject()->getDueDate());
    $notify_date = new DateTime($this->getObject()->getNotifyDate());
    $this->widgetSchema['notify_date'] = new sfWidgetFormInputText(array(), array('value' => '', 'data-value' => (!empty($notify_date_native) && $notify_date_native !== '0000-00-00 00:00:00') ? $notify_date->format('Y/m/j') : ''));
    $this->widgetSchema['due_date'] = new sfWidgetFormInputText(array(), array('value' => '', 'data-value' => (!empty($due_date_native) && $due_date_native !== '0000-00-00 00:00:00') ? $due_data->format('Y/m/j') : ''));

    if ($this->getOption('user')){
      $assigned_to = array('' => '');
      $team_users = sfGuardUserTable::getInstance()->getUsersInTeamQuery($this->getOption('user')->getGuardUser())->execute();
      foreach ($team_users as $team_user) {
        $assigned_to[$team_user->getId()] = $team_user->getUserName();
      }
      $this->widgetSchema['assigned_to'] = new sfWidgetFormChoice(array('choices' => $assigned_to), array('class' => 'form-control'));
      if ($this->getObject()->isNew()) {
        $this->widgetSchema['assigned_to']->setDefault($this->getOption('user')->getGuardUser()->getId());
      }
    }

    $this->disableCSRFProtection();
  }

  public function doSave($con = null)
  {

    if (!$this->isNew()) {
      unset($this->values['custom_fields']);
    }

    parent::doSave($con);
  }
}
