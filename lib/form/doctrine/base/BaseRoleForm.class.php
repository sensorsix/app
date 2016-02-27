<?php

/**
 * Role form base class.
 *
 * @method Role getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseRoleForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                      => new sfWidgetFormInputHidden(),
      'name'                    => new sfWidgetFormInputText(),
      'decision_id'             => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Decision'), 'add_empty' => true)),
      'prioritize'              => new sfWidgetFormInputCheckbox(),
      'show_criteria_weights'   => new sfWidgetFormInputCheckbox(),
      'show_alternatives_score' => new sfWidgetFormInputCheckbox(),
      'prioritization_method'   => new sfWidgetFormChoice(array('choices' => array('forced ranking' => 'forced ranking', 'five point scale' => 'five point scale', 'ten point scale' => 'ten point scale', 'pairwise comparison' => 'pairwise comparison'))),
      'view_matrix'             => new sfWidgetFormInputCheckbox(),
      'updateable'              => new sfWidgetFormInputCheckbox(),
      'anonymous'               => new sfWidgetFormInputCheckbox(),
      'show_comments'           => new sfWidgetFormInputCheckbox(),
      'collect_items'           => new sfWidgetFormInputCheckbox(),
      'display_items'           => new sfWidgetFormInputCheckbox(),
      'allow_voting'            => new sfWidgetFormInputCheckbox(),
      'dashboard'               => new sfWidgetFormInputCheckbox(),
      'comment'                 => new sfWidgetFormTextarea(),
      'token'                   => new sfWidgetFormInputText(),
      'continue_url'            => new sfWidgetFormInputText(),
      'language'                => new sfWidgetFormChoice(array('choices' => array('en' => 'en', 'da' => 'da'))),
      'active'                  => new sfWidgetFormInputCheckbox(),
      'created_at'              => new sfWidgetFormDateTime(),
      'updated_at'              => new sfWidgetFormDateTime(),
      'files_list'              => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'UploadedFile')),
    ));

    $this->setValidators(array(
      'id'                      => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'                    => new sfValidatorString(array('max_length' => 255)),
      'decision_id'             => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Decision'), 'required' => false)),
      'prioritize'              => new sfValidatorBoolean(array('required' => false)),
      'show_criteria_weights'   => new sfValidatorBoolean(array('required' => false)),
      'show_alternatives_score' => new sfValidatorBoolean(array('required' => false)),
      'prioritization_method'   => new sfValidatorChoice(array('choices' => array(0 => 'forced ranking', 1 => 'five point scale', 2 => 'ten point scale', 3 => 'pairwise comparison'), 'required' => false)),
      'view_matrix'             => new sfValidatorBoolean(array('required' => false)),
      'updateable'              => new sfValidatorBoolean(array('required' => false)),
      'anonymous'               => new sfValidatorBoolean(array('required' => false)),
      'show_comments'           => new sfValidatorBoolean(array('required' => false)),
      'collect_items'           => new sfValidatorBoolean(array('required' => false)),
      'display_items'           => new sfValidatorBoolean(array('required' => false)),
      'allow_voting'            => new sfValidatorBoolean(array('required' => false)),
      'dashboard'               => new sfValidatorBoolean(array('required' => false)),
      'comment'                 => new sfValidatorString(array('required' => false)),
      'token'                   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'continue_url'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'language'                => new sfValidatorChoice(array('choices' => array(0 => 'en', 1 => 'da'), 'required' => false)),
      'active'                  => new sfValidatorBoolean(array('required' => false)),
      'created_at'              => new sfValidatorDateTime(),
      'updated_at'              => new sfValidatorDateTime(),
      'files_list'              => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'UploadedFile', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('role[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Role';
  }

  public function updateDefaultsFromObject()
  {
    parent::updateDefaultsFromObject();

    if (isset($this->widgetSchema['files_list']))
    {
      $this->setDefault('files_list', $this->object->Files->getPrimaryKeys());
    }

  }

  protected function doSave($con = null)
  {
    $this->saveFilesList($con);

    parent::doSave($con);
  }

  public function saveFilesList($con = null)
  {
    if (!$this->isValid())
    {
      throw $this->getErrorSchema();
    }

    if (!isset($this->widgetSchema['files_list']))
    {
      // somebody has unset this widget
      return;
    }

    if (null === $con)
    {
      $con = $this->getConnection();
    }

    $existing = $this->object->Files->getPrimaryKeys();
    $values = $this->getValue('files_list');
    if (!is_array($values))
    {
      $values = array();
    }

    $unlink = array_diff($existing, $values);
    if (count($unlink))
    {
      $this->object->unlink('Files', array_values($unlink));
    }

    $link = array_diff($values, $existing);
    if (count($link))
    {
      $this->object->link('Files', array_values($link));
    }
  }

}
