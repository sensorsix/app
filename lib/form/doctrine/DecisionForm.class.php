<?php

/**
 * Decision form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class DecisionForm extends BaseDecisionForm
{
  protected $formatterName = 'bootstrap_horizontal_shortlabel';

  public function configure()
  {
    unset($this['lft'], $this['rgt'], $this['level'], $this['root_id'], $this['user_id'], $this['created_at'], $this['updated_at'], $this['folder_id'], $this['external_id']);

    $this->setValidator('name', new sfValidatorDecisionName(array('max_length' => 255, 'required' => true, 'user_id' => $this->getObject()->user_id, 'decision_id' => $this->getObject()->id)));

    if ($this->getOption('type') == 'edit'){
      unset($this['type_id'], $this['template_id']);
    }else{
      $this->widgetSchema['template_id'] = new sfWidgetFormChoice(array('choices' => array()));

      if (isset($this->getObject()->user_id) && !empty($this->getObject()->user_id)){
        $this->widgetSchema['type_id']->setOption(
          'query',
          DecisionTypeTable::getInstance()->createQuery('t')->innerJoin('t.Users u')->where(
            'u.id = ?',
            $this->getObject()->user_id
          )
        );
      }else{
        $this->widgetSchema['type_id']->setOption(
          'query',
          DecisionTypeTable::getInstance()->createQuery('t')
        );
      }

      $this->widgetSchema->setLabel('template_id', 'Template name');
      // One response is for dashboard by default
      if ($this->getObject()->Response->count() > 1)
      {
        $this->widgetSchema['template_id']->setAttribute('disabled', 'disabled');
        $this->widgetSchema->setHelp('template_id', 'Remove all responses first');
      }

      $this->validatorSchema['template_id']->setOption('required', false);
    }

    $this->widgetSchema['upload'] = new laWidgetFileUpload(array('module_partial' => 'decision/import'));


    $this->widgetSchema['assigned_to']->setOption('query', sfGuardUserTable::getInstance()->getUsersInTeamQuery($this->getObject()->getUser()));

    $this->widgetSchema['objective'] = new laWidgetFormCKEditor(array('config' => array('height' => '250px')));
    $this->validatorSchema['assigned_to']->setOption('query', sfGuardUserTable::getInstance()->getUsersInTeamQuery($this->getObject()->getUser()));
    /** @var sfWidget $widget */
    foreach ($this->widgetSchema->getFields() as $widget)
    {
      $widget->setAttribute('class', 'form-control autosave');
    }

    /* Get all tags and create input field */
    $tags = array();
    foreach($this->getObject()->getTagDecision() as $tag){
      $tags[] = $tag->Tag->name;
    }
    $this->widgetSchema['tags'] = new sfWidgetFormInputText(array(), array('value' => implode(',', $tags), 'class'=>'tags_input', 'data-role' => 'tagsinput'));

    $start_date_native = $this->getObject()->getStartDate();
    $end_date_native = $this->getObject()->getEndDate();
    $end_data = new DateTime($this->getObject()->getEndDate());
    $start_date = new DateTime($this->getObject()->getStartDate());
    $this->widgetSchema['start_date'] = new sfWidgetFormInputText(array(), array('value' => '', 'data-value' => (!empty($start_date_native) && $start_date_native !== '0000-00-00 00:00:00') ? $start_date->format('Y/m/j') : ''));
    $this->widgetSchema['end_date'] = new sfWidgetFormInputText(array(), array('value' => '', 'data-value' => (!empty($end_date_native) && $end_date_native !== '0000-00-00 00:00:00') ? $end_data->format('Y/m/j') : ''));

    $this->widgetSchema['color'] = new sfWidgetFormSelect(array('choices' => array(
      '#FFFFFF' => '#FFFFFF',
      '#CCCCCC' => '#CCCCCC',
      '#A0522D' => '#A0522D',
      '#CD5C5C' => '#CD5C5C',
      '#FF4500' => '#FF4500',
      '#008B8B' => '#008B8B',
      '#B8860B' => '#B8860B',
      '#32CD32' => '#32CD32',
      '#FFD700' => '#FFD700',
      '#48D1CC' => '#48D1CC',
      '#87CEEB' => '#87CEEB',
      '#FF69B4' => '#FF69B4',
      '#87CEFA' => '#87CEFA',
      '#6495ED' => '#6495ED',
      '#DC143C' => '#DC143C',
      '#FF8C00' => '#FF8C00',
      '#C71585' => '#C71585',
      '#000000' => '#000000',
    )));

    if ($this->isNew()) {
      $this->widgetSchema['assigned_to']->setDefault($this->getObject()->getUser()->getId());

      $now = new DateTime();
      $this->widgetSchema['start_date']->setDefault($now->format('Y/m/j'));
      $this->widgetSchema['start_date']->setAttribute('data-value', $now->format('Y/m/j'));
      $now = $now->modify('+1 month');
      $this->widgetSchema['end_date']->setDefault($now->format('Y/m/j'));
      $this->widgetSchema['end_date']->setAttribute('data-value', $now->format('Y/m/j'));
    }

    $this->disableCSRFProtection();
  }

  /**
   * @return string json
   */
  public function getTemplatesJson()
  {
    $q = Doctrine_Query::create()
      ->from('TypeTemplate')
      ->where('user_id = ? or user_id is null', $this->getOption('guard_user_id'));

    if ($this->getOption('manager_id')){
      $q->orWhere('user_id = ?', $this->getOption('manager_id'));
    }

    $templates = $q->execute();

    $result = array();
    foreach ($templates as $template)
    {
      if (!isset($result[$template->type_id]))
      {
        $result[$template->type_id] = array();
      }
      $result[$template->type_id][$template->id] = $template->name;
    }

    return json_encode($result);
  }
}
