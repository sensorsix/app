<?php

/**
 * Roadmap form base class.
 *
 * @method Roadmap getObject() Returns the current form's model object
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseRoadmapForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'user_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'name'              => new sfWidgetFormInputText(),
      'description'       => new sfWidgetFormTextarea(),
      'token'             => new sfWidgetFormInputText(),
      'folder_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Folder'), 'add_empty' => true)),
      'active'            => new sfWidgetFormInputCheckbox(),
      'status'            => new sfWidgetFormChoice(array('choices' => array('Draft' => 'Draft', 'Reviewed' => 'Reviewed', 'Approved' => 'Approved', 'Under revision' => 'Under revision'))),
      'show_items'        => new sfWidgetFormInputCheckbox(),
      'show_releases'     => new sfWidgetFormInputCheckbox(),
      'show_dependencies' => new sfWidgetFormInputCheckbox(),
      'show_description'  => new sfWidgetFormInputCheckbox(),
      'workspace_mode'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'user_id'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'required' => false)),
      'name'              => new sfValidatorString(array('max_length' => 64)),
      'description'       => new sfValidatorString(array('required' => false)),
      'token'             => new sfValidatorString(array('max_length' => 6)),
      'folder_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Folder'), 'required' => false)),
      'active'            => new sfValidatorBoolean(array('required' => false)),
      'status'            => new sfValidatorChoice(array('choices' => array(0 => 'Draft', 1 => 'Reviewed', 2 => 'Approved', 3 => 'Under revision'), 'required' => false)),
      'show_items'        => new sfValidatorBoolean(array('required' => false)),
      'show_releases'     => new sfValidatorBoolean(array('required' => false)),
      'show_dependencies' => new sfValidatorBoolean(array('required' => false)),
      'show_description'  => new sfValidatorBoolean(array('required' => false)),
      'workspace_mode'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('roadmap[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Roadmap';
  }

}
