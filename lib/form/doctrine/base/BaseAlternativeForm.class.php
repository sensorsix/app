<?php

/**
 * Alternative form base class.
 *
 * @method Alternative getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseAlternativeForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'decision_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Decision'), 'add_empty' => false)),
      'name'            => new sfWidgetFormInputText(),
      'additional_info' => new sfWidgetFormTextarea(),
      'score'           => new sfWidgetFormInputText(),
      'status'          => new sfWidgetFormChoice(array('choices' => array('Draft' => 'Draft', 'Reviewed' => 'Reviewed', 'Planned' => 'Planned', 'Doing' => 'Doing', 'Finished' => 'Finished', 'Parked' => 'Parked'))),
      'created_by'      => new sfWidgetFormInputText(),
      'updated_by'      => new sfWidgetFormInputText(),
      'external_id'     => new sfWidgetFormInputText(),
      'assigned_to'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Assigned'), 'add_empty' => true)),
      'notes'           => new sfWidgetFormTextarea(),
      'work_progress'   => new sfWidgetFormInputText(),
      'due_date'        => new sfWidgetFormInputText(),
      'notify_date'     => new sfWidgetFormInputText(),
      'type_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('AlternativeType'), 'add_empty' => true)),
      'item_id'         => new sfWidgetFormInputText(),
      'custom_fields'   => new sfWidgetFormInputText(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
      'root_id'         => new sfWidgetFormInputText(),
      'lft'             => new sfWidgetFormInputText(),
      'rgt'             => new sfWidgetFormInputText(),
      'level'           => new sfWidgetFormInputText(),
      'files_list'      => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'UploadedFile')),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'decision_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Decision'))),
      'name'            => new sfValidatorString(array('max_length' => 255)),
      'additional_info' => new sfValidatorString(array('required' => false)),
      'score'           => new sfValidatorInteger(array('required' => false)),
      'status'          => new sfValidatorChoice(array('choices' => array(0 => 'Draft', 1 => 'Reviewed', 2 => 'Planned', 3 => 'Doing', 4 => 'Finished', 5 => 'Parked'), 'required' => false)),
      'created_by'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'updated_by'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'external_id'     => new sfValidatorString(array('max_length' => 50, 'required' => false)),
      'assigned_to'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Assigned'), 'required' => false)),
      'notes'           => new sfValidatorString(array('required' => false)),
      'work_progress'   => new sfValidatorPass(array('required' => false)),
      'due_date'        => new sfValidatorPass(array('required' => false)),
      'notify_date'     => new sfValidatorPass(array('required' => false)),
      'type_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('AlternativeType'), 'required' => false)),
      'item_id'         => new sfValidatorString(array('max_length' => 8, 'required' => false)),
      'custom_fields'   => new sfValidatorPass(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
      'root_id'         => new sfValidatorInteger(array('required' => false)),
      'lft'             => new sfValidatorInteger(array('required' => false)),
      'rgt'             => new sfValidatorInteger(array('required' => false)),
      'level'           => new sfValidatorInteger(array('required' => false)),
      'files_list'      => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'UploadedFile', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('alternative[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Alternative';
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
