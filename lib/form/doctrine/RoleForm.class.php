<?php

/**
 * Role form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class RoleForm extends BaseRoleForm
{
  protected $formatterName = 'bootstrap_horizontal_shortlabel';

  public function configure()
  {
    unset($this['decision_id'], $this['token'], $this['files_list'], $this['created_at'], $this['updated_at']);

    $choices = sfConfig::get('app_rating_method');
    $choices['pairwise comparison'] = 'Pairwise comparison';

    $this->widgetSchema['prioritization_method'] = new sfWidgetFormChoice(array('choices' => $choices, 'expanded' => true));
    $this->widgetSchema->setLabel('prioritization_method', 'Criteria prioritization method');
    $this->widgetSchema['comment'] = new laWidgetFormCKEditor();
    $this->widgetSchema['upload'] = new laWidgetFileUpload();
    $this->widgetSchema['updateable']->setOption('default', true);
    $this->widgetSchema['language']->setOption('choices', array('en' => 'English', 'da' => 'Danish'));
    $this->widgetSchema->setLabel('view_matrix', 'View as matrix');
    $this->widgetSchema->setLabel('show_comments', 'Show comments to users');
    $this->widgetSchema->setLabel('prioritize', 'Prioritize criteria');
    $this->widgetSchema->setLabel('anonymous', 'Make anonymous');
    $this->widgetSchema->setLabel('updateable', 'Make updateable');
    $this->widgetSchema->setLabel('show_criteria_weights', 'Show criteria graph');
    $this->widgetSchema->setLabel('show_alternatives_score', 'Show score graph');
    $this->widgetSchema->setLabel('collect_items', 'Collect suggestions for items');
    $this->widgetSchema->setLabel('display_items', 'Display existing items');
    $this->widgetSchema['link'] = new sfWidgetFormInputText(array(), array('readonly' => 'readonly'));
    $this->widgetSchema['linkEmbed'] = new sfWidgetFormTextarea(array(), array('readonly' => 'readonly'));
    $this->setDefault('link', sfContext::getInstance()->getConfiguration()->generateFrontendUrl('measure', array('token' => $this->getObject()->token)));
    $this->setDefault('linkEmbed', '<iframe  width="560px" height="315px" src="' . sfContext::getInstance()->getConfiguration()->generateFrontendUrl('measure', array('token' => $this->getObject()->token)) . '?embed=true" frameborder="0" allowfullscreen></iframe>');

    /** @var sfWidget $widget */
    foreach ($this->widgetSchema->getFields() as $key => $widget) {
      if ($widget instanceof sfWidgetFormInputCheckbox) {
        $widget->setAttribute('class', 'checkbox');
      } elseif ($key == 'prioritization_method') {

      } else {
        $widget->setAttribute('class', 'form-control');
      }
    }

    $this->validatorSchema['continue_url'] = new sfValidatorUrl(array('max_length' => 255, 'required' => false));
    $this->validatorSchema['prioritize']->setOption('true_values', array('checked'));
    $this->validatorSchema['show_comments']->setOption('true_values', array('checked'));
    $this->validatorSchema['view_matrix']->setOption('true_values', array('checked'));
    $this->validatorSchema['anonymous']->setOption('true_values', array('checked'));
    $this->validatorSchema['updateable']->setOption('true_values', array('checked'));
    $this->validatorSchema['show_criteria_weights']->setOption('true_values', array('checked'));
    $this->validatorSchema['show_alternatives_score']->setOption('true_values', array('checked'));
    $this->validatorSchema['collect_items']->setOption('true_values', array('checked'));
    $this->validatorSchema['display_items']->setOption('true_values', array('checked'));
    $this->validatorSchema['allow_voting']->setOption('true_values', array('checked'));
    $this->validatorSchema['active']->setOption('true_values', array('checked'));
    $this->validatorSchema->setOption('allow_extra_fields', true);

    $this->disableCSRFProtection();
  }
}
